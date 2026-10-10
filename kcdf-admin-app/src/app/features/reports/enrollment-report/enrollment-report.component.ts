import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ApiService } from '../../../core/services/api.service';
import { BatchService } from '../../../core/services/batch.service';
import { Enrollment, Batch } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';

interface EnrollmentSummary {
  by_status: Record<string, number>;
  by_payment_status: Record<string, number>;
  total: number;
}

@Component({
  selector: 'app-enrollment-report',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, AppIconComponent, PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent],
  templateUrl: './enrollment-report.component.html',
  styleUrl: './enrollment-report.component.scss',
})
export class EnrollmentReportComponent implements OnInit {
  private readonly apiService = inject(ApiService);
  private readonly batchService = inject(BatchService);
  private readonly toastService = inject(ToastService);

  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
  readonly summary = signal<EnrollmentSummary | null>(null);
  readonly enrollments = signal<Enrollment[]>([]);

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
      error: () => { this.loading.set(false); this.toastService.show('Failed to load report', { variant: 'error', durationMs: 3000, actionLabel: 'Close' }); }
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
