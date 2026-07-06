import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { EnrollmentService } from '../../../core/services/enrollment.service';
import { Enrollment } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { EnrollmentFormComponent } from '../enrollment-form/enrollment-form.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-enrollment-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Enrollments" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" (click)="openForm()"><mat-icon>add</mat-icon> Enroll Member</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="cancelled">Cancelled</mat-option>
              <mat-option value="completed">Completed</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Payment Status</mat-label>
            <mat-select [formControl]="payStatusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="pending">Pending</mat-option>
              <mat-option value="paid">Paid</mat-option>
              <mat-option value="overdue">Overdue</mat-option>
              <mat-option value="waived">Waived</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
        <table mat-table [dataSource]="enrollments()" class="full-width">
          <ng-container matColumnDef="member"><th mat-header-cell *matHeaderCellDef>Member</th><td mat-cell *matCellDef="let e">{{ e.member?.first_name }} {{ e.member?.last_name }}</td></ng-container>
          <ng-container matColumnDef="batch"><th mat-header-cell *matHeaderCellDef>Batch</th><td mat-cell *matCellDef="let e">{{ e.batch?.batch_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="family"><th mat-header-cell *matHeaderCellDef>Family</th><td mat-cell *matCellDef="let e">{{ e.family?.family_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="enrolled_at"><th mat-header-cell *matHeaderCellDef>Enrolled</th><td mat-cell *matCellDef="let e">{{ e.enrolled_at | date:'dd MMM yyyy' }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let e"><app-status-badge [status]="e.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="payment_status"><th mat-header-cell *matHeaderCellDef>Payment</th><td mat-cell *matCellDef="let e"><app-status-badge [status]="e.payment_status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let e">
              <button mat-icon-button color="warn" (click)="cancel(e)" [disabled]="e.status !== 'active'" matTooltip="Cancel enrollment">
                <mat-icon>cancel</mat-icon>
              </button>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No enrollments found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;margin-bottom:8px}.full-width{width:100%}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class EnrollmentListComponent implements OnInit {
  private readonly enrollmentService = inject(EnrollmentService);
  private readonly dialog = inject(MatDialog);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  readonly enrollments = signal<Enrollment[]>([]);
  readonly total = signal(0);
  readonly cols = ['member', 'batch', 'family', 'enrolled_at', 'status', 'payment_status', 'actions'];
  readonly statusCtrl = new FormControl('');
  readonly payStatusCtrl = new FormControl('');
  private page = 1;

  ngOnInit(): void {
    this.load();
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
    this.payStatusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.enrollmentService.list({ page: this.page, per_page: 20, status: this.statusCtrl.value ?? undefined, payment_status: this.payStatusCtrl.value ?? undefined }).subscribe({
      next: res => { this.enrollments.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }

  openForm(): void {
    const ref = this.dialog.open(EnrollmentFormComponent, { width: '560px', data: null });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  cancel(enrollment: Enrollment): void {
    const ref = this.dialog.open(ConfirmDialogComponent, {
      data: { title: 'Cancel Enrollment', message: 'Cancel this enrollment?', danger: true, confirmLabel: 'Cancel Enrollment' }
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.enrollmentService.cancel(enrollment.id).subscribe({
        next: () => { this.snackBar.open('Enrollment cancelled', 'Close', { duration: 3000 }); this.load(); },
        error: () => this.snackBar.open('Failed to cancel enrollment', 'Close', { duration: 3000 })
      });
    });
  }
}
