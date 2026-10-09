import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { GroupService } from '../../../core/services/group.service';
import { Group, GroupMember } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-group-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, AppIconComponent, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="group()?.group_name ?? 'Group'" subtitle="Group Detail">
      <button type="button" routerLink="/groups" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="arrow_back" aria-hidden="true" class="h-6 w-6"></app-icon> Back</button>
    </app-page-header>

    <div class="space-y-6" *ngIf="group()">
      <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Info</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Name</span><span class="text-sm text-gray-900 dark:text-white">{{ group()!.group_name }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Visibility</span><span class="text-sm text-gray-900 dark:text-white">{{ group()!.visibility }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</span><app-status-badge [status]="group()!.status"></app-status-badge></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Members</span><span class="text-sm text-gray-900 dark:text-white">{{ members().length }}</span></div>
          <div class="flex flex-col gap-1 sm:col-span-2"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Description</span><span class="text-sm text-gray-900 dark:text-white">{{ group()!.description ?? '—' }}</span></div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <h2 class="border-b border-gray-200 px-5 py-4 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white">Members</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Member</th><th class="px-4 py-3">Joined</th><th class="px-4 py-3">Banned</th><th class="px-4 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <tr *ngFor="let m of members()" class="text-gray-700 dark:text-gray-200">
                <td class="whitespace-nowrap px-4 py-3">{{ m.member?.first_name }} {{ m.member?.last_name }}</td><td class="whitespace-nowrap px-4 py-3">{{ m.joined_at | date:'dd MMM yyyy' }}</td>
                <td class="whitespace-nowrap px-4 py-3"><app-icon [name]="m.is_banned ? 'block' : 'check_circle'" [ngClass]="m.is_banned ? 'text-red-600' : 'text-gray-400'" aria-hidden="true" class="h-6 w-6"></app-icon></td>
                <td class="whitespace-nowrap px-4 py-3">
                  <button type="button" (click)="banMember(m)" [disabled]="m.is_banned" [attr.aria-label]="'Ban ' + (m.member?.first_name ?? 'member')" title="Ban" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800"><app-icon name="block" aria-hidden="true" class="h-6 w-6"></app-icon></button>
                  <button type="button" (click)="removeMember(m)" [attr.aria-label]="'Remove ' + (m.member?.first_name ?? 'member')" title="Remove" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-800"><app-icon name="person_remove" aria-hidden="true" class="h-6 w-6"></app-icon></button>
                </td>
              </tr>
              <tr *ngIf="!members().length"><td colspan="4" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No members</td></tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  `,
})
export class GroupDetailComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toastService = inject(ToastService);
  readonly loading = signal(false);
  readonly group = signal<Group | null>(null);
  readonly members = signal<GroupMember[]>([]);
  readonly cols = ['name', 'joined', 'banned', 'actions'];

  ngOnInit(): void { this.load(); }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({ group: this.groupService.get(id), members: this.groupService.getMembers(id) }).subscribe({
      next: ({ group, members }) => { this.group.set(group.data); this.members.set(members.data); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  banMember(member: GroupMember): void {
    const ref = this.dialog.open<ConfirmDialogComponent, { title: string; message: string; danger: boolean }, boolean>(ConfirmDialogComponent, { data: { title: 'Ban Member', message: 'Ban this member from the group?', danger: true }, ariaLabel: 'Confirm banning group member' });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.banMember(this.group()!.id, member.member_id).subscribe({ next: () => { this.toastService.show('Member banned', { variant: 'success', durationMs: 2000, actionLabel: 'Close' }); this.load(); } });
    });
  }

  removeMember(member: GroupMember): void {
    const ref = this.dialog.open<ConfirmDialogComponent, { title: string; message: string; danger: boolean }, boolean>(ConfirmDialogComponent, { data: { title: 'Remove Member', message: 'Remove this member from the group?', danger: true }, ariaLabel: 'Confirm removing group member' });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.removeMember(this.group()!.id, member.member_id).subscribe({ next: () => { this.toastService.show('Member removed', { variant: 'success', durationMs: 2000, actionLabel: 'Close' }); this.load(); } });
    });
  }
}
