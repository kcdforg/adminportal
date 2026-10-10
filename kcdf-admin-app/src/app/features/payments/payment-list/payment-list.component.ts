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
  templateUrl: './payment-list.component.html',
  styleUrl: './payment-list.component.scss',
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
