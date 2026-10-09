import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { GroupService } from '../../../core/services/group.service';
import { Group } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef, TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-group-form-dialog',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <div class="px-6 pt-6 text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ group ? 'Edit Group' : 'Create Group' }}</div>
    <div class="px-6 py-4">
      <form [formGroup]="form" class="flex min-w-0 flex-col gap-4 sm:min-w-[26rem]">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Group Name
          <input formControlName="group_name" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          <span *ngIf="form.controls.group_name.touched && form.controls.group_name.invalid" class="mt-1 block text-sm text-red-600">Required</span>
        </label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description<textarea formControlName="description" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea></label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Visibility
          <select formControlName="visibility" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="public">Public</option><option value="private">Private</option>
          </select>
        </label>
      </form>
    </div>
    <div class="flex justify-end gap-2 px-6 pb-5">
      <button type="button" (click)="dialogRef.close()" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
      <button (click)="save()" [disabled]="saving" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">{{ saving ? 'Saving...' : 'Save' }}</button>
    </div>
  `
})
export class GroupFormDialogComponent {
  readonly group = inject(TAILWIND_DIALOG_DATA) as Group | null;
  private readonly groupService = inject(GroupService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toastService = inject(ToastService);
  saving = false;
  readonly form = new FormGroup({
    group_name: new FormControl(this.group?.group_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    description: new FormControl(this.group?.description ?? ''),
    visibility: new FormControl(this.group?.visibility ?? 'public', { nonNullable: true }),
  });
  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const obs = this.group ? this.groupService.update(this.group.id, this.form.getRawValue()) : this.groupService.create(this.form.getRawValue());
    obs.subscribe({
      next: () => { this.toastService.show('Group saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toastService.show(err?.error?.error?.message ?? 'Error', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}

@Component({
  selector: 'app-group-list',
  standalone: true,
  imports: [CommonModule, RouterModule, ReactiveFormsModule, AppIconComponent, TailwindPaginatorComponent, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Groups" [subtitle]="'Total: ' + total()">
      <button type="button" (click)="openForm()" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><app-icon name="add" aria-hidden="true" class="h-6 w-6"></app-icon> Create Group</button>
    </app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Visibility</th><th class="px-4 py-3">Members</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <tr *ngFor="let g of groups()" class="cursor-pointer text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800/50" [routerLink]="['/groups', g.id]">
              <td class="whitespace-nowrap px-4 py-3 font-medium">{{ g.group_name }}</td><td class="whitespace-nowrap px-4 py-3">{{ g.visibility }}</td><td class="whitespace-nowrap px-4 py-3">{{ g.member_count ?? '—' }}</td><td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="g.status"></app-status-badge></td>
              <td class="whitespace-nowrap px-4 py-3">
                <button type="button" [routerLink]="['/groups', g.id]" (click)="$event.stopPropagation()" [attr.aria-label]="'View ' + g.group_name" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-indigo-600 dark:hover:bg-gray-800"><app-icon name="visibility" aria-hidden="true" class="h-6 w-6"></app-icon></button>
                <button type="button" (click)="openForm(g); $event.stopPropagation()" [attr.aria-label]="'Edit ' + g.group_name" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-indigo-600 dark:hover:bg-gray-800"><app-icon name="edit" aria-hidden="true" class="h-6 w-6"></app-icon></button>
              </td>
            </tr>
            <tr *ngIf="!groups().length"><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No groups found</td></tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize()" [pageSizeOptions]="[10,20,50]" selectId="group-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
})
export class GroupListComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly groups = signal<Group[]>([]);
  readonly total = signal(0);
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading.set(true);
    this.groupService.list({ page: this.page, per_page: this.pageSize() }).subscribe({
      next: res => { this.groups.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }
  openForm(group?: Group): void {
    const ref = this.dialog.open<GroupFormDialogComponent, Group | null, boolean>(GroupFormDialogComponent, { width: '480px', data: group ?? null, ariaLabel: group ? 'Edit group' : 'Create group' });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
