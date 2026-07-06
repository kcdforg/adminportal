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
import { BatchService } from '../../../core/services/batch.service';
import { Enrollment, Batch } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';

interface EnrollmentSummary {
  by_status: Record<string, number>;
  by_payment_status: Record<string, number>;
  total: number;
}

@Component({
  selector: 'app-enrollment-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatTableModule, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Enrollment Report">
      <button mat-stroked-button (click)="exportCsv()" [disabled]="!enrollments().length"><mat-icon>download</mat-icon> Export CSV</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <form [formGroup]="filterForm" class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Batch</mat-label>
            <mat-select formControlName="batch_id">
              <mat-option value="">All</mat-option>
              <mat-option *ngFor="let b of batches()" [value]="b.id">{{ b.batch_name }}</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select formControlName="status">
              <mat-option value="">All</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="cancelled">Cancelled</mat-option>
              <mat-option value="completed">Completed</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>From Date</mat-label>
            <input matInput type="date" formControlName="date_from" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>To Date</mat-label>
            <input matInput type="date" formControlName="date_to" />
          </mat-form-field>
          <button mat-flat-button color="primary" (click)="load()"><mat-icon>search</mat-icon> Generate</button>
        </form>
      </mat-card-content>
    </mat-card>

    <div class="summary-grid mt-16" *ngIf="summary()">
      <mat-card>
        <mat-card-header><mat-card-title>Total Enrollments</mat-card-title></mat-card-header>
        <mat-card-content><div class="big-number">{{ summary()!.total }}</div></mat-card-content>
      </mat-card>
      <mat-card *ngFor="let e of statusEntries()">
        <mat-card-header><mat-card-title>{{ e[0] | titlecase }}</mat-card-title></mat-card-header>
        <mat-card-content><div class="big-number">{{ e[1] }}</div></mat-card-content>
      </mat-card>
    </div>

    <mat-card class="mt-16" *ngIf="enrollments().length">
      <mat-card-content>
        <table mat-table [dataSource]="enrollments()" class="full-width">
          <ng-container matColumnDef="member"><th mat-header-cell *matHeaderCellDef>Member</th><td mat-cell *matCellDef="let e">{{ e.member?.first_name }} {{ e.member?.last_name }}</td></ng-container>
          <ng-container matColumnDef="batch"><th mat-header-cell *matHeaderCellDef>Batch</th><td mat-cell *matCellDef="let e">{{ e.batch?.batch_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="family"><th mat-header-cell *matHeaderCellDef>Family</th><td mat-cell *matCellDef="let e">{{ e.family?.family_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="enrolled_at"><th mat-header-cell *matHeaderCellDef>Enrolled</th><td mat-cell *matCellDef="let e">{{ e.enrolled_at | date:'dd MMM yyyy' }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let e"><app-status-badge [status]="e.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="payment_status"><th mat-header-cell *matHeaderCellDef>Payment</th><td mat-cell *matCellDef="let e"><app-status-badge [status]="e.payment_status"></app-status-badge></td></ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;"></tr>
        </table>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;flex-wrap:wrap;align-items:center}.mt-16{margin-top:16px}.summary-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px}.big-number{font-size:28px;font-weight:700;color:#1a237e;padding:8px 0}.full-width{width:100%}`]
})
export class EnrollmentReportComponent implements OnInit {
  private readonly apiService = inject(ApiService);
  private readonly batchService = inject(BatchService);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
  readonly summary = signal<EnrollmentSummary | null>(null);
  readonly enrollments = signal<Enrollment[]>([]);
  readonly cols = ['member', 'batch', 'family', 'enrolled_at', 'status', 'payment_status'];

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
      error: () => { this.loading.set(false); this.snackBar.open('Failed to load report', 'Close', { duration: 3000 }); }
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
