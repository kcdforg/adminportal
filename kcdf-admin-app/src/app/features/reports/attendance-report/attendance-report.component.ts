import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
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
import { Batch } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';

interface AttendanceReportRow {
  member_id: number;
  member_name: string;
  sessions: Record<string, string>;
}

@Component({
  selector: 'app-attendance-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatTableModule, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Attendance Report">
      <button mat-stroked-button (click)="exportCsv()" [disabled]="!rows().length"><mat-icon>download</mat-icon> Export CSV</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <form [formGroup]="filterForm" class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Batch *</mat-label>
            <mat-select formControlName="batch_id">
              <mat-option *ngFor="let b of batches()" [value]="b.id">{{ b.batch_name }}</mat-option>
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
          <button mat-flat-button color="primary" (click)="load()">
            <mat-icon>search</mat-icon> Generate
          </button>
        </form>
      </mat-card-content>
    </mat-card>

    <mat-card class="mt-16" *ngIf="rows().length">
      <mat-card-content class="overflow-x">
        <table mat-table [dataSource]="rows()" class="report-table">
          <ng-container matColumnDef="member_name">
            <th mat-header-cell *matHeaderCellDef>Member</th>
            <td mat-cell *matCellDef="let r">{{ r.member_name }}</td>
          </ng-container>
          <ng-container *ngFor="let date of sessionDates()" [matColumnDef]="date">
            <th mat-header-cell *matHeaderCellDef class="date-header">{{ date }}</th>
            <td mat-cell *matCellDef="let r">
              <app-status-badge [status]="r.sessions[date] ?? 'absent'"></app-status-badge>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="displayedCols()"></tr>
          <tr mat-row *matRowDef="let r; columns: displayedCols();"></tr>
        </table>
      </mat-card-content>
    </mat-card>

    <mat-card class="mt-16" *ngIf="!rows().length && !loading()">
      <mat-card-content class="empty-state">
        <mat-icon>fact_check</mat-icon>
        <p>Select a batch and date range to generate the attendance report.</p>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;flex-wrap:wrap;align-items:center}.mt-16{margin-top:16px}.overflow-x{overflow-x:auto}.report-table{min-width:600px}.date-header{min-width:100px;font-size:11px}.empty-state{display:flex;flex-direction:column;align-items:center;padding:48px;color:#999}.empty-state mat-icon{font-size:48px;height:48px;width:48px;margin-bottom:8px;opacity:0.4}`]
})
export class AttendanceReportComponent implements OnInit {
  private readonly apiService = inject(ApiService);
  private readonly batchService = inject(BatchService);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
  readonly rows = signal<AttendanceReportRow[]>([]);
  readonly sessionDates = signal<string[]>([]);

  displayedCols = () => ['member_name', ...this.sessionDates()];

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
      error: () => { this.loading.set(false); this.snackBar.open('Failed to load report', 'Close', { duration: 3000 }); }
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
