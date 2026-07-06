import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatTableModule } from '@angular/material/table';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ApiService } from '../../../core/services/api.service';
import { Payment } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { CurrencyInrPipe } from '../../../shared/pipes/currency-inr.pipe';

interface PaymentSummary {
  total: number;
  by_type: Record<string, number>;
  by_method: Record<string, number>;
}

@Component({
  selector: 'app-payment-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatTableModule, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent, CurrencyInrPipe],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Payment Report">
      <button mat-stroked-button (click)="exportCsv()" [disabled]="!transactions().length"><mat-icon>download</mat-icon> Export CSV</button>
    </app-page-header>

    <mat-card>
      <mat-card-content>
        <form [formGroup]="filterForm" class="filters">
          <mat-form-field appearance="outline">
            <mat-label>From Date</mat-label>
            <input matInput type="date" formControlName="date_from" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>To Date</mat-label>
            <input matInput type="date" formControlName="date_to" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Payment Type</mat-label>
            <mat-select formControlName="payment_type">
              <mat-option value="">All</mat-option>
              <mat-option value="class_fee">Class Fee</mat-option>
              <mat-option value="donation">Donation</mat-option>
              <mat-option value="event_fee">Event Fee</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select formControlName="status">
              <mat-option value="">All</mat-option>
              <mat-option value="completed">Completed</mat-option>
              <mat-option value="pending">Pending</mat-option>
            </mat-select>
          </mat-form-field>
          <button mat-flat-button color="primary" (click)="load()"><mat-icon>search</mat-icon> Generate</button>
        </form>
      </mat-card-content>
    </mat-card>

    <div class="summary-grid mt-16" *ngIf="summary()">
      <mat-card>
        <mat-card-header><mat-card-title>Total Collected</mat-card-title></mat-card-header>
        <mat-card-content><div class="big-number">{{ summary()!.total | currencyInr }}</div></mat-card-content>
      </mat-card>
      <mat-card *ngFor="let entry of typeEntries()">
        <mat-card-header><mat-card-title>{{ entry[0] }}</mat-card-title></mat-card-header>
        <mat-card-content><div class="big-number">{{ entry[1] | currencyInr }}</div></mat-card-content>
      </mat-card>
    </div>

    <mat-card class="mt-16" *ngIf="transactions().length">
      <mat-card-header><mat-card-title>Transactions</mat-card-title></mat-card-header>
      <mat-card-content>
        <table mat-table [dataSource]="transactions()" class="full-width">
          <ng-container matColumnDef="family"><th mat-header-cell *matHeaderCellDef>Family</th><td mat-cell *matCellDef="let p">{{ p.family?.family_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="type"><th mat-header-cell *matHeaderCellDef>Type</th><td mat-cell *matCellDef="let p">{{ p.payment_type }}</td></ng-container>
          <ng-container matColumnDef="amount"><th mat-header-cell *matHeaderCellDef>Amount</th><td mat-cell *matCellDef="let p">{{ p.amount | currencyInr }}</td></ng-container>
          <ng-container matColumnDef="method"><th mat-header-cell *matHeaderCellDef>Method</th><td mat-cell *matCellDef="let p">{{ p.payment_method }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let p"><app-status-badge [status]="p.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="date"><th mat-header-cell *matHeaderCellDef>Date</th><td mat-cell *matCellDef="let p">{{ p.payment_date | date:'dd MMM yyyy' }}</td></ng-container>
          <tr mat-header-row *matHeaderRowDef="txCols"></tr>
          <tr mat-row *matRowDef="let r; columns: txCols;"></tr>
        </table>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;flex-wrap:wrap;align-items:center}.mt-16{margin-top:16px}.summary-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px}.big-number{font-size:28px;font-weight:700;color:#1a237e;padding:8px 0}.full-width{width:100%}`]
})
export class PaymentReportComponent {
  private readonly apiService = inject(ApiService);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  readonly summary = signal<PaymentSummary | null>(null);
  readonly transactions = signal<Payment[]>([]);
  readonly txCols = ['family', 'type', 'amount', 'method', 'status', 'date'];

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
      error: () => { this.loading.set(false); this.snackBar.open('Failed to load report', 'Close', { duration: 3000 }); }
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
