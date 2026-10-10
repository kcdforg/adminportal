import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { EnrollmentService } from '../../../core/services/enrollment.service';
import { Enrollment } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { EnrollmentFormComponent } from '../enrollment-form/enrollment-form.component';
import { ConfirmDialogComponent, ConfirmDialogData } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-enrollment-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, AppIconComponent, TooltipDirective, TailwindPaginatorComponent,
  ],
  templateUrl: './enrollment-list.component.html',
  styleUrl: './enrollment-list.component.scss'
})
export class EnrollmentListComponent implements OnInit {
  private readonly enrollmentService = inject(EnrollmentService);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toast = inject(ToastService);

  readonly loading = signal(false);
  readonly enrollments = signal<Enrollment[]>([]);
  readonly total = signal(0);
  readonly statusCtrl = new FormControl('');
  readonly payStatusCtrl = new FormControl('');
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void {
    this.load();
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
    this.payStatusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.enrollmentService.list({ page: this.page, per_page: this.pageSize(), status: this.statusCtrl.value ?? undefined, payment_status: this.payStatusCtrl.value ?? undefined }).subscribe({
      next: res => { this.enrollments.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }

  openForm(): void {
    const ref = this.dialog.open<EnrollmentFormComponent, null, boolean>(EnrollmentFormComponent, {
      width: '560px',
      data: null,
      ariaLabel: 'Enroll member',
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  cancel(enrollment: Enrollment): void {
    const ref = this.dialog.open<ConfirmDialogComponent, ConfirmDialogData, boolean>(ConfirmDialogComponent, {
      data: { title: 'Cancel Enrollment', message: 'Cancel this enrollment?', danger: true, confirmLabel: 'Cancel Enrollment' },
      ariaLabel: 'Confirm enrollment cancellation',
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.enrollmentService.cancel(enrollment.id).subscribe({
        next: () => { this.toast.show('Enrollment cancelled', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.load(); },
        error: () => this.toast.show('Failed to cancel enrollment', { variant: 'error', durationMs: 3000, actionLabel: 'Close' })
      });
    });
  }
}
