import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ApiService } from '../../../core/services/api.service';
import { BatchService } from '../../../core/services/batch.service';
import { Batch } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';

interface AttendanceReportRow {
  member_id: number;
  member_name: string;
  sessions: Record<string, string>;
}

@Component({
  selector: 'app-attendance-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, AppIconComponent, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Attendance Report">
      <button type="button" (click)="exportCsv()" [disabled]="!rows().length" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="download" aria-hidden="true" class="h-6 w-6"></app-icon> Export CSV</button>
    </app-page-header>
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form [formGroup]="filterForm" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Batch *
            <select formControlName="batch_id" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option [ngValue]="null" disabled>Select a batch</option><option *ngFor="let b of batches()" [ngValue]="b.id">{{ b.batch_name }}</option>
            </select>
            <span *ngIf="filterForm.controls.batch_id.touched && filterForm.controls.batch_id.invalid" class="mt-1 block text-sm text-red-600">Required</span>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Date<input type="date" formControlName="date_from" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">To Date<input type="date" formControlName="date_to" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
          <button type="button" (click)="load()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><app-icon name="search" aria-hidden="true" class="h-6 w-6"></app-icon> Generate</button>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900" *ngIf="rows().length">
      <div class="overflow-x-auto">
        <table class="min-w-[600px] divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="sticky left-0 min-w-48 bg-gray-50 px-4 py-3 dark:bg-gray-800">Member</th><th *ngFor="let date of sessionDates()" class="min-w-28 px-4 py-3">{{ date }}</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><tr *ngFor="let r of rows()" class="text-gray-700 dark:text-gray-200"><td class="sticky left-0 bg-white px-4 py-3 font-medium dark:bg-gray-900">{{ r.member_name }}</td><td *ngFor="let date of sessionDates()" class="px-4 py-3"><app-status-badge [status]="r.sessions[date]"></app-status-badge></td></tr></tbody>
        </table>
      </div>
    </section>

    <section class="mt-6 flex flex-col items-center rounded-xl border border-gray-200 bg-white px-6 py-12 text-center text-gray-500 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400" *ngIf="!rows().length && !loading()">
      <app-icon name="fact_check" aria-hidden="true" class="mb-2 !h-12 !w-12 !text-5xl opacity-40"></app-icon>
      <p>Select a batch and date range to generate the attendance report.</p>
    </section>
  `,
})
export class AttendanceReportComponent implements OnInit {
  private readonly apiService = inject(ApiService);
  private readonly batchService = inject(BatchService);
  private readonly toastService = inject(ToastService);

  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
  readonly rows = signal<AttendanceReportRow[]>([]);
  readonly sessionDates = signal<string[]>([]);

  readonly filterForm = new FormGroup({
    batch_id: new FormControl<number | null>(null, [Validators.required]),
    date_from: new FormControl(''),
    date_to: new FormControl(''),
  });

  ngOnInit(): void {
    this.batchService.list({ per_page: 100 }).subscribe(res => this.batches.set(res.data));
  }

  load(): void {
    if (this.filterForm.invalid) { this.filterForm.markAllAsTouched(); return; }
    this.loading.set(true);
    const val = this.filterForm.getRawValue();
    this.apiService.get<{ members: AttendanceReportRow[]; session_dates: string[] }>('/reports/attendance', {
      batch_id: val.batch_id ?? undefined,
      date_from: val.date_from ?? undefined,
      date_to: val.date_to ?? undefined,
    }).subscribe({
      next: (res) => {
        this.rows.set(res.data.members ?? []);
        this.sessionDates.set(res.data.session_dates ?? []);
        this.loading.set(false);
      },
      error: () => { this.loading.set(false); this.toastService.show('Failed to load report', { variant: 'error', durationMs: 3000, actionLabel: 'Close' }); }
    });
  }

  exportCsv(): void {
    const header = ['Member', ...this.sessionDates()].join(',');
    const csvRows = this.rows().map(r => [r.member_name, ...this.sessionDates().map(d => r.sessions[d] ?? 'absent')].join(','));
    const csv = [header, ...csvRows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'attendance-report.csv';
    a.click();
  }
}
