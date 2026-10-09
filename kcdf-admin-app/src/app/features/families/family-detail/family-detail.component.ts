import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { FamilyService } from '../../../core/services/family.service';
import { Family, FamilyMember } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { FamilyFormComponent } from '../family-form/family-form.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-family-detail',
  standalone: true,
  imports: [
    CommonModule, RouterModule,
    AppIconComponent, TooltipDirective,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="family()?.family_name ?? 'Family'" subtitle="Family Detail">
      <button type="button" routerLink="/families"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
        <app-icon name="arrow_back" aria-hidden="true" class="h-6 w-6"></app-icon> Back
      </button>
      <button type="button" (click)="openEdit()"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <app-icon name="edit" aria-hidden="true" class="h-6 w-6"></app-icon> Edit
      </button>
    </app-page-header>

    <div class="grid gap-5" *ngIf="family()">
      <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">Family Info</h2>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Family Code</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.family_code }}</span></div>
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Family Name</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.family_name }}</span></div>
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</span><app-status-badge [status]="family()!.status"></app-status-badge></div>
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Created</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.created_at | date:'dd MMM yyyy' }}</span></div>
          </div>
          <div class="my-5 border-t border-gray-200 dark:border-gray-800"></div>
          <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Address</p>
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4" *ngIf="family()!.address">
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Line 1</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.address!.line1 }}</span></div>
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">City</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.address!.city }}</span></div>
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">State</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.address!.state }}</span></div>
            <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pincode</span><span class="text-sm text-gray-900 dark:text-white">{{ family()!.address!.pincode }}</span></div>
          </div>
          <p *ngIf="!family()!.address" class="text-sm text-gray-500 dark:text-gray-400">No address on record</p>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">Members ({{ members().length }})</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[520px] text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
              <tr><th scope="col" class="px-5 py-3 font-medium">Name</th><th scope="col" class="px-5 py-3 font-medium">Role</th><th scope="col" class="px-5 py-3 font-medium">Joined</th><th scope="col" class="px-5 py-3 text-right font-medium"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <tr *ngFor="let m of members()" class="text-gray-700 dark:text-gray-300">
                <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">{{ m.member?.first_name }} {{ m.member?.last_name }}</td>
                <td class="px-5 py-4"><app-status-badge [status]="m.member_role"></app-status-badge></td>
                <td class="px-5 py-4">{{ m.joined_at | date:'dd MMM yyyy' }}</td>
                <td class="px-5 py-4 text-right">
                  <button type="button" (click)="removeMember(m)" [appTooltip]="'Remove'"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-rose-600 transition hover:bg-rose-50 focus-visible:outline-2 focus-visible:outline-rose-600 dark:text-rose-400 dark:hover:bg-rose-500/10"
                    aria-label="Remove member"><app-icon name="person_remove" aria-hidden="true" class="h-6 w-6"></app-icon></button>
                </td>
              </tr>
              <tr *ngIf="members().length === 0">
                <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No members</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  `,
})
export class FamilyDetailComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toast = inject(ToastService);

  readonly loading = signal(false);
  readonly family = signal<Family | null>(null);
  readonly members = signal<FamilyMember[]>([]);

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({
      family: this.familyService.get(id),
      members: this.familyService.getMembers(id),
    }).subscribe({
      next: ({ family, members }) => {
        this.family.set(family.data);
        this.members.set(members.data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  openEdit(): void {
    const family = this.family();
    const ref = this.dialog.open(FamilyFormComponent, {
      width: '560px',
      ariaLabel: family?.family_name ? `Edit family ${family.family_name}` : 'Edit family',
      data: family,
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  removeMember(member: FamilyMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, {
      ariaLabel: 'Confirm family member removal',
      data: { title: 'Remove Member', message: 'Remove this member from the family?', danger: true }
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.familyService.removeMember(this.family()!.id, member.member_id).subscribe({
        next: () => {
          this.toast.show('Member removed', {
            variant: 'success',
            durationMs: 2000,
            actionLabel: 'Close',
          });
          this.load();
        },
        error: () => this.toast.show('Failed to remove member', {
          variant: 'error',
          durationMs: 3000,
          actionLabel: 'Close',
        })
      });
    });
  }
}
