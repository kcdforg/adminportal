import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ApiService } from '../../../core/services/api.service';
import { BatchService } from '../../../core/services/batch.service';
import { Enrollment, Batch } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';

interface EnrollmentSummary {
  by_status: Record<string, number>;
  by_payment_status: Record<string, number>;
  total: number;
}

@Component({
  selector: 'app-enrollment-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, AppIconComponent, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Enrollment Report">
      <button type="button" (click)="exportCsv()" [disabled]="!enrollments().length" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="download" aria-hidden="true" class="h-6 w-6"></app-icon> Export CSV</button>
    </app-page-header>
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form [formGroup]="filterForm" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-5">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Batch
            <select formControlName="batch_id" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option [ngValue]="''">All</option><option *ngFor="let b of batches()" [ngValue]="b.id">{{ b.batch_name }}</option>
            </select>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status
            <select formControlName="status" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">All</option><option value="active">Active</option><option value="cancelled">Cancelled</option><option value="completed">Completed</option>
            </select>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Date<input type="date" formControlName="date_from" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">To Date<input type="date" formControlName="date_to" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
          <button type="button" (click)="load()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><app-icon name="search" aria-hidden="true" class="h-6 w-6"></app-icon> Generate</button>
        </form>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" *ngIf="summary()">
      <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"><h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Enrollments</h2><p class="mt-3 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ summary()!.total }}</p></article>
      <article *ngFor="let e of statusEntries()" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"><h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ e[0] | titlecase }}</h2><p class="mt-3 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ e[1] }}</p></article>
    </div>

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900" *ngIf="enrollments().length">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Member</th><th class="px-4 py-3">Batch</th><th class="px-4 py-3">Family</th><th class="px-4 py-3">Enrolled</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Payment</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><tr *ngFor="let e of enrollments()" class="text-gray-700 dark:text-gray-200"><td class="whitespace-nowrap px-4 py-3">{{ e.member?.first_name }} {{ e.member?.last_name }}</td><td class="whitespace-nowrap px-4 py-3">{{ e.batch?.batch_name ?? '—' }}</td><td class="whitespace-nowrap px-4 py-3">{{ e.family?.family_name ?? '—' }}</td><td class="whitespace-nowrap px-4 py-3">{{ e.enrolled_at | date:'dd MMM yyyy' }}</td><td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="e.status"></app-status-badge></td><td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="e.payment_status"></app-status-badge></td></tr></tbody>
        </table>
      </div>
    </section>
  `,
})
export class EnrollmentReportComponent implements OnInit {
  private readonly apiService = inject(ApiService);
  private readonly batchService = inject(BatchService);
  private readonly toastService = inject(ToastService);

  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
  readonly summary = signal<EnrollmentSummary | null>(null);
  readonly enrollments = signal<Enrollment[]>([]);

  statusEntries = () => Object.entries(this.summary()?.by_status ?? {});

  readonly filterForm = new FormGroup({
    batch_id: new FormControl(''),
    status: new FormControl(''),
    date_from: new FormControl(''),
    date_to: new FormControl(''),
  });

  ngOnInit(): void {
    this.batchService.list({ per_page: 100 }).subscribe(res => this.batches.set(res.data));
  }

  load(): void {
    this.loading.set(true);
    const val = this.filterForm.getRawValue();
    this.apiService.get<{ summary: EnrollmentSummary; enrollments: Enrollment[] }>('/reports/enrollments', {
      batch_id: val.batch_id ?? undefined,
      status: val.status ?? undefined,
      date_from: val.date_from ?? undefined,
      date_to: val.date_to ?? undefined,
    }).subscribe({
      next: (res) => {
        this.summary.set(res.data.summary);
        this.enrollments.set(res.data.enrollments ?? []);
        this.loading.set(false);
      },
      error: () => { this.loading.set(false); this.toastService.show('Failed to load report', { variant: 'error', durationMs: 3000, actionLabel: 'Close' }); }
    });
  }

  exportCsv(): void {
    const header = ['Member', 'Batch', 'Family', 'Enrolled', 'Status', 'Payment Status'].join(',');
    const rows = this.enrollments().map(e =>
      [`${e.member?.first_name} ${e.member?.last_name}`, e.batch?.batch_name ?? '', e.family?.family_name ?? '', e.enrolled_at, e.status, e.payment_status].join(',')
    );
    const csv = [header, ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'enrollment-report.csv';
    a.click();
  }
}
