import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { PaymentService } from '../../../core/services/payment.service';
import { Payment } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { CurrencyInrPipe } from '../../../shared/pipes/currency-inr.pipe';
import { PaymentFormComponent } from '../payment-form/payment-form.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-payment-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    AppIconComponent, TailwindPaginatorComponent,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, CurrencyInrPipe,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Payments" [subtitle]="'Total: ' + total()">
      <button type="button" (click)="openRefund()" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="keyboard_return" aria-hidden="true" class="h-6 w-6"></app-icon> Refund</button>
      <button type="button" (click)="openForm()" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><app-icon name="add" aria-hidden="true" class="h-6 w-6"></app-icon> Record Payment</button>
    </app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="grid grid-cols-1 gap-4 border-b border-gray-200 p-4 sm:grid-cols-2 xl:grid-cols-4 dark:border-gray-800">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment Type
          <select [formControl]="typeCtrl" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
            <option value="">All</option><option value="class_fee">Class Fee</option><option value="donation">Donation</option><option value="event_fee">Event Fee</option><option value="refund">Refund</option>
          </select>
        </label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status
          <select [formControl]="statusCtrl" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
            <option value="">All</option><option value="completed">Completed</option><option value="pending">Pending</option><option value="failed">Failed</option><option value="refunded">Refunded</option>
          </select>
        </label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Date<input type="date" [formControl]="dateFromCtrl" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900" /></label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">To Date<input type="date" [formControl]="dateToCtrl" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900" /></label>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Family</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Date</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <tr *ngFor="let p of payments()" class="text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800/50">
              <td class="whitespace-nowrap px-4 py-3">{{ p.family?.family_name ?? '—' }}</td><td class="whitespace-nowrap px-4 py-3">{{ p.payment_type }}</td><td class="whitespace-nowrap px-4 py-3">{{ p.amount | currencyInr }}</td><td class="whitespace-nowrap px-4 py-3">{{ p.payment_method }}</td><td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="p.status"></app-status-badge></td><td class="whitespace-nowrap px-4 py-3">{{ p.payment_date | date:'dd MMM yyyy' }}</td>
            </tr>
            <tr *ngIf="!payments().length"><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No payments found</td></tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize()" [pageSizeOptions]="[10,20,50]" selectId="payment-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
})
export class PaymentListComponent implements OnInit {
  private readonly paymentService = inject(PaymentService);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly payments = signal<Payment[]>([]);
  readonly total = signal(0);
  readonly typeCtrl = new FormControl('');
  readonly statusCtrl = new FormControl('');
  readonly dateFromCtrl = new FormControl('');
  readonly dateToCtrl = new FormControl('');
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void {
    this.load();
    [this.typeCtrl, this.statusCtrl, this.dateFromCtrl, this.dateToCtrl].forEach(ctrl =>
      ctrl.valueChanges.subscribe(() => { this.page = 1; this.load(); })
    );
  }

  load(): void {
    this.loading.set(true);
    this.paymentService.list({
      page: this.page, per_page: this.pageSize(),
      payment_type: this.typeCtrl.value ?? undefined,
      status: this.statusCtrl.value ?? undefined,
      payment_date_from: this.dateFromCtrl.value ?? undefined,
      payment_date_to: this.dateToCtrl.value ?? undefined,
    }).subscribe({
      next: res => { this.payments.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }
  openForm(): void { const ref = this.dialog.open<PaymentFormComponent, { isRefund: boolean }, boolean>(PaymentFormComponent, { width: '520px', data: { isRefund: false }, ariaLabel: 'Record payment' }); ref.afterClosed().subscribe(s => { if (s) this.load(); }); }
  openRefund(): void { const ref = this.dialog.open<PaymentFormComponent, { isRefund: boolean }, boolean>(PaymentFormComponent, { width: '520px', data: { isRefund: true }, ariaLabel: 'Record refund' }); ref.afterClosed().subscribe(s => { if (s) this.load(); }); }
}
