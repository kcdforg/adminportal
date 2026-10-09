import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { EnrollmentService } from '../../../core/services/enrollment.service';
import { Enrollment } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { EnrollmentFormComponent } from '../enrollment-form/enrollment-form.component';
import { ConfirmDialogComponent, ConfirmDialogData } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-enrollment-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, AppIconComponent, TooltipDirective, TailwindPaginatorComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Enrollments" [subtitle]="'Total: ' + total()">
      <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" (click)="openForm()"><app-icon name="add" class="h-6 w-6"></app-icon> Enroll Member</button>
    </app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 p-4 dark:border-gray-800 sm:p-6">
        <div class="filters">
          <label for="enrollment-status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status
            <select id="enrollment-status-filter" [formControl]="statusCtrl" class="mt-1 block w-full min-w-[200px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="cancelled">Cancelled</option>
              <option value="completed">Completed</option>
            </select>
          </label>
          <label for="enrollment-payment-status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment Status
            <select id="enrollment-payment-status-filter" [formControl]="payStatusCtrl" class="mt-1 block w-full min-w-[200px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">All</option>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
              <option value="waived">Waived</option>
            </select>
          </label>
        </div>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[900px] divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300">
            <tr><th scope="col" class="px-4 py-3">Member</th><th scope="col" class="px-4 py-3">Batch</th><th scope="col" class="px-4 py-3">Family</th><th scope="col" class="px-4 py-3">Enrolled</th><th scope="col" class="px-4 py-3">Status</th><th scope="col" class="px-4 py-3">Payment</th><th scope="col" class="px-4 py-3">Actions</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <tr *ngFor="let e of enrollments()" class="text-gray-700 dark:text-gray-200">
              <td class="whitespace-nowrap px-4 py-3">{{ e.member?.first_name }} {{ e.member?.last_name }}</td>
              <td class="whitespace-nowrap px-4 py-3">{{ e.batch?.batch_name ?? '—' }}</td>
              <td class="whitespace-nowrap px-4 py-3">{{ e.family?.family_name ?? '—' }}</td>
              <td class="whitespace-nowrap px-4 py-3">{{ e.enrolled_at | date:'dd MMM yyyy' }}</td>
              <td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="e.status"></app-status-badge></td>
              <td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="e.payment_status"></app-status-badge></td>
              <td class="whitespace-nowrap px-4 py-3">
              <button type="button" (click)="cancel(e)" [disabled]="e.status !== 'active'" appTooltip="Cancel enrollment" aria-label="Cancel enrollment" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg text-red-600 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:cursor-not-allowed disabled:opacity-50 dark:text-red-400 dark:hover:bg-gray-800">
                <app-icon name="cancel" aria-hidden="true" class="h-6 w-6"></app-icon>
              </button>
              </td>
            </tr>
            <tr *ngIf="!enrollments().length"><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No enrollments found</td></tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize()" [pageSizeOptions]="[10,20,50]" selectId="enrollment-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
  styles: [`.filters{display:flex;flex-wrap:wrap;gap:16px}`]
})
export class EnrollmentListComponent implements OnInit {
  private readonly enrollmentService = inject(EnrollmentService);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toast = inject(ToastService);

  readonly loading = signal(false);
  readonly enrollments = signal<Enrollment[]>([]);
  readonly total = signal(0);
  readonly statusCtrl = new FormControl('');
  readonly payStatusCtrl = new FormControl('');
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void {
    this.load();
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
    this.payStatusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.enrollmentService.list({ page: this.page, per_page: this.pageSize(), status: this.statusCtrl.value ?? undefined, payment_status: this.payStatusCtrl.value ?? undefined }).subscribe({
      next: res => { this.enrollments.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }

  openForm(): void {
    const ref = this.dialog.open<EnrollmentFormComponent, null, boolean>(EnrollmentFormComponent, {
      width: '560px',
      data: null,
      ariaLabel: 'Enroll member',
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  cancel(enrollment: Enrollment): void {
    const ref = this.dialog.open<ConfirmDialogComponent, ConfirmDialogData, boolean>(ConfirmDialogComponent, {
      data: { title: 'Cancel Enrollment', message: 'Cancel this enrollment?', danger: true, confirmLabel: 'Cancel Enrollment' },
      ariaLabel: 'Confirm enrollment cancellation',
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.enrollmentService.cancel(enrollment.id).subscribe({
        next: () => { this.toast.show('Enrollment cancelled', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.load(); },
        error: () => this.toast.show('Failed to cancel enrollment', { variant: 'error', durationMs: 3000, actionLabel: 'Close' })
      });
    });
  }
}
