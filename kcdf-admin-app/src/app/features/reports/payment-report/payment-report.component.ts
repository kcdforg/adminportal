import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ApiService } from '../../../core/services/api.service';
import { Payment } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { CurrencyInrPipe } from '../../../shared/pipes/currency-inr.pipe';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';

interface PaymentSummary {
  total: number;
  by_type: Record<string, number>;
  by_method: Record<string, number>;
}

@Component({
  selector: 'app-payment-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, AppIconComponent, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent, CurrencyInrPipe],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Payment Report">
      <button type="button" (click)="exportCsv()" [disabled]="!transactions().length" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="download" aria-hidden="true" class="h-6 w-6"></app-icon> Export CSV</button>
    </app-page-header>

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form [formGroup]="filterForm" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-5">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Date<input type="date" formControlName="date_from" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">To Date<input type="date" formControlName="date_to" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment Type
            <select formControlName="payment_type" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">All</option><option value="class_fee">Class Fee</option><option value="donation">Donation</option><option value="event_fee">Event Fee</option>
            </select>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status
            <select formControlName="status" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">All</option><option value="completed">Completed</option><option value="pending">Pending</option>
            </select>
          </label>
          <button type="button" (click)="load()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><app-icon name="search" aria-hidden="true" class="h-6 w-6"></app-icon> Generate</button>
        </form>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" *ngIf="summary()">
      <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"><h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Collected</h2><p class="mt-3 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ summary()!.total | currencyInr }}</p></article>
      <article *ngFor="let entry of typeEntries()" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"><h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ entry[0] }}</h2><p class="mt-3 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ entry[1] | currencyInr }}</p></article>
    </div>

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900" *ngIf="transactions().length">
      <h2 class="border-b border-gray-200 px-5 py-4 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white">Transactions</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Family</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Date</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><tr *ngFor="let p of transactions()" class="text-gray-700 dark:text-gray-200"><td class="whitespace-nowrap px-4 py-3">{{ p.family?.family_name ?? '—' }}</td><td class="whitespace-nowrap px-4 py-3">{{ p.payment_type }}</td><td class="whitespace-nowrap px-4 py-3">{{ p.amount | currencyInr }}</td><td class="whitespace-nowrap px-4 py-3">{{ p.payment_method }}</td><td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="p.status"></app-status-badge></td><td class="whitespace-nowrap px-4 py-3">{{ p.payment_date | date:'dd MMM yyyy' }}</td></tr></tbody>
        </table>
      </div>
    </section>
  `,
})
export class PaymentReportComponent {
  private readonly apiService = inject(ApiService);
  private readonly toastService = inject(ToastService);

  readonly loading = signal(false);
  readonly summary = signal<PaymentSummary | null>(null);
  readonly transactions = signal<Payment[]>([]);

  typeEntries = () => Object.entries(this.summary()?.by_type ?? {});

  readonly filterForm = new FormGroup({
    date_from: new FormControl(''),
    date_to: new FormControl(''),
    payment_type: new FormControl(''),
    status: new FormControl(''),
  });

  load(): void {
    this.loading.set(true);
    const val = this.filterForm.getRawValue();
    this.apiService.get<{ summary: PaymentSummary; transactions: Payment[] }>('/reports/payments', {
      date_from: val.date_from ?? undefined,
      date_to: val.date_to ?? undefined,
      payment_type: val.payment_type ?? undefined,
      status: val.status ?? undefined,
    }).subscribe({
      next: (res) => {
        this.summary.set(res.data.summary);
        this.transactions.set(res.data.transactions ?? []);
        this.loading.set(false);
      },
      error: () => { this.loading.set(false); this.toastService.show('Failed to load report', { variant: 'error', durationMs: 3000, actionLabel: 'Close' }); }
    });
  }

  exportCsv(): void {
    const header = ['Family', 'Type', 'Amount', 'Method', 'Status', 'Date'].join(',');
    const rows = this.transactions().map(p =>
      [p.family?.family_name ?? '', p.payment_type, p.amount, p.payment_method, p.status, p.payment_date].join(',')
    );
    const csv = [header, ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'payment-report.csv';
    a.click();
  }
}
