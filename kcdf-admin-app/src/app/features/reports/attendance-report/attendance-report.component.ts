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
  templateUrl: './attendance-report.component.html',
  styleUrl: './attendance-report.component.scss',
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
