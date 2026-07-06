import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { PaymentService } from '../../../core/services/payment.service';
import { Payment } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { CurrencyInrPipe } from '../../../shared/pipes/currency-inr.pipe';
import { PaymentFormComponent } from '../payment-form/payment-form.component';

@Component({
  selector: 'app-payment-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, CurrencyInrPipe,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Payments" [subtitle]="'Total: ' + total()">
      <button mat-stroked-button (click)="openRefund()"><mat-icon>keyboard_return</mat-icon> Refund</button>
      <button mat-flat-button color="primary" (click)="openForm()"><mat-icon>add</mat-icon> Record Payment</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Payment Type</mat-label>
            <mat-select [formControl]="typeCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="class_fee">Class Fee</mat-option>
              <mat-option value="donation">Donation</mat-option>
              <mat-option value="event_fee">Event Fee</mat-option>
              <mat-option value="refund">Refund</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="completed">Completed</mat-option>
              <mat-option value="pending">Pending</mat-option>
              <mat-option value="failed">Failed</mat-option>
              <mat-option value="refunded">Refunded</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>From Date</mat-label>
            <input matInput type="date" [formControl]="dateFromCtrl" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>To Date</mat-label>
            <input matInput type="date" [formControl]="dateToCtrl" />
          </mat-form-field>
        </div>
        <table mat-table [dataSource]="payments()" class="full-width">
          <ng-container matColumnDef="family"><th mat-header-cell *matHeaderCellDef>Family</th><td mat-cell *matCellDef="let p">{{ p.family?.family_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="payment_type"><th mat-header-cell *matHeaderCellDef>Type</th><td mat-cell *matCellDef="let p">{{ p.payment_type }}</td></ng-container>
          <ng-container matColumnDef="amount"><th mat-header-cell *matHeaderCellDef>Amount</th><td mat-cell *matCellDef="let p">{{ p.amount | currencyInr }}</td></ng-container>
          <ng-container matColumnDef="payment_method"><th mat-header-cell *matHeaderCellDef>Method</th><td mat-cell *matCellDef="let p">{{ p.payment_method }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let p"><app-status-badge [status]="p.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="payment_date"><th mat-header-cell *matHeaderCellDef>Date</th><td mat-cell *matCellDef="let p">{{ p.payment_date | date:'dd MMM yyyy' }}</td></ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No payments found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:8px}.full-width{width:100%}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class PaymentListComponent implements OnInit {
  private readonly paymentService = inject(PaymentService);
  private readonly dialog = inject(MatDialog);
  readonly loading = signal(false);
  readonly payments = signal<Payment[]>([]);
  readonly total = signal(0);
  readonly cols = ['family', 'payment_type', 'amount', 'payment_method', 'status', 'payment_date'];
  readonly typeCtrl = new FormControl('');
  readonly statusCtrl = new FormControl('');
  readonly dateFromCtrl = new FormControl('');
  readonly dateToCtrl = new FormControl('');
  private page = 1;

  ngOnInit(): void {
    this.load();
    [this.typeCtrl, this.statusCtrl, this.dateFromCtrl, this.dateToCtrl].forEach(ctrl =>
      ctrl.valueChanges.subscribe(() => { this.page = 1; this.load(); })
    );
  }

  load(): void {
    this.loading.set(true);
    this.paymentService.list({
      page: this.page, per_page: 20,
      payment_type: this.typeCtrl.value ?? undefined,
      status: this.statusCtrl.value ?? undefined,
      payment_date_from: this.dateFromCtrl.value ?? undefined,
      payment_date_to: this.dateToCtrl.value ?? undefined,
    }).subscribe({
      next: res => { this.payments.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }
  openForm(): void { const ref = this.dialog.open(PaymentFormComponent, { width: '520px', data: { isRefund: false } }); ref.afterClosed().subscribe(s => { if (s) this.load(); }); }
  openRefund(): void { const ref = this.dialog.open(PaymentFormComponent, { width: '520px', data: { isRefund: true } }); ref.afterClosed().subscribe(s => { if (s) this.load(); }); }
}
