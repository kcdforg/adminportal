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
  templateUrl: './payment-report.component.html',
  styleUrl: './payment-report.component.scss',
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
