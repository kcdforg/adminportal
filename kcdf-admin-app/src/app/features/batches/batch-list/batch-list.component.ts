import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { BatchService } from '../../../core/services/batch.service';
import { Batch } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';

@Component({
  selector: 'app-batch-list',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Batches" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" routerLink="/batches/new"><mat-icon>add</mat-icon> New Batch</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="upcoming">Upcoming</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="completed">Completed</mat-option>
              <mat-option value="cancelled">Cancelled</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
        <table mat-table [dataSource]="batches()" class="full-width">
          <ng-container matColumnDef="batch_name"><th mat-header-cell *matHeaderCellDef>Batch Name</th><td mat-cell *matCellDef="let b">{{ b.batch_name }}</td></ng-container>
          <ng-container matColumnDef="program"><th mat-header-cell *matHeaderCellDef>Program</th><td mat-cell *matCellDef="let b">{{ b.program?.name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="trainer"><th mat-header-cell *matHeaderCellDef>Trainer</th><td mat-cell *matCellDef="let b">{{ b.trainer?.member?.first_name ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="start_date"><th mat-header-cell *matHeaderCellDef>Start Date</th><td mat-cell *matCellDef="let b">{{ b.start_date | date:'dd MMM yyyy' }}</td></ng-container>
          <ng-container matColumnDef="capacity"><th mat-header-cell *matHeaderCellDef>Capacity</th><td mat-cell *matCellDef="let b">{{ b.enrolled_count ?? 0 }}/{{ b.capacity }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let b"><app-status-badge [status]="b.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let b">
              <button mat-icon-button [routerLink]="['/batches', b.id]"><mat-icon>visibility</mat-icon></button>
              <button mat-icon-button [routerLink]="['/batches', b.id, 'edit']"><mat-icon>edit</mat-icon></button>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;" class="clickable" [routerLink]="['/batches', r.id]"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No batches found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;margin-bottom:8px}.full-width{width:100%}.clickable{cursor:pointer}.clickable:hover{background:#f5f5f5}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class BatchListComponent implements OnInit {
  private readonly batchService = inject(BatchService);
  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
  readonly total = signal(0);
  readonly cols = ['batch_name', 'program', 'trainer', 'start_date', 'capacity', 'status', 'actions'];
  readonly statusCtrl = new FormControl('');
  private page = 1;

  ngOnInit(): void {
    this.load();
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.batchService.list({ page: this.page, per_page: 20, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.batches.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }
}
