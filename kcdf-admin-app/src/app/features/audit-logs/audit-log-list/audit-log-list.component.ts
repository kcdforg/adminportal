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
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { ActivityLogService } from '../../../core/services/activity-log.service';
import { ActivityLog } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';

@Component({
  selector: 'app-log-diff-dialog',
  standalone: true,
  imports: [CommonModule, MatDialogModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>Audit Log Detail</h2>
    <mat-dialog-content>
      <div class="log-meta">
        <span><strong>Action:</strong> {{ log.action }}</span>
        <span><strong>Entity:</strong> {{ log.entity_type }} #{{ log.entity_id }}</span>
        <span><strong>Actor:</strong> {{ log.actor?.first_name ?? log.actor_type }} #{{ log.actor_id }}</span>
        <span><strong>Time:</strong> {{ log.created_at | date:'dd MMM yyyy HH:mm:ss' }}</span>
      </div>
      <div class="diff-grid">
        <div>
          <p class="diff-label">Old Values</p>
          <pre class="diff-box">{{ log.old_values | json }}</pre>
        </div>
        <div>
          <p class="diff-label">New Values</p>
          <pre class="diff-box">{{ log.new_values | json }}</pre>
        </div>
      </div>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Close</button>
    </mat-dialog-actions>
  `,
  styles: [`.log-meta{display:flex;flex-wrap:wrap;gap:16px;margin-bottom:16px;font-size:14px}.diff-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;min-width:600px}.diff-label{font-size:12px;font-weight:600;color:#666;text-transform:uppercase;margin:0 0 4px}.diff-box{background:#f5f5f5;padding:12px;border-radius:6px;overflow:auto;max-height:300px;font-size:12px;margin:0}`]
})
export class LogDiffDialogComponent {
  readonly log: ActivityLog = inject(MAT_DIALOG_DATA);
}

@Component({
  selector: 'app-audit-log-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Audit Logs" [subtitle]="'Total: ' + total()"></app-page-header>
    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Entity Type</mat-label>
            <input matInput [formControl]="entityCtrl" placeholder="e.g. Family, Member" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Action</mat-label>
            <input matInput [formControl]="actionCtrl" placeholder="e.g. create, update" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>From Date</mat-label>
            <input matInput type="date" [formControl]="dateFromCtrl" />
          </mat-form-field>
        </div>
        <table mat-table [dataSource]="logs()" class="full-width">
          <ng-container matColumnDef="actor"><th mat-header-cell *matHeaderCellDef>Actor</th><td mat-cell *matCellDef="let l">{{ l.actor?.first_name ?? l.actor_type }} {{ l.actor?.last_name ?? '' }}</td></ng-container>
          <ng-container matColumnDef="action"><th mat-header-cell *matHeaderCellDef>Action</th><td mat-cell *matCellDef="let l">{{ l.action }}</td></ng-container>
          <ng-container matColumnDef="entity_type"><th mat-header-cell *matHeaderCellDef>Entity</th><td mat-cell *matCellDef="let l">{{ l.entity_type }}</td></ng-container>
          <ng-container matColumnDef="entity_id"><th mat-header-cell *matHeaderCellDef>ID</th><td mat-cell *matCellDef="let l">{{ l.entity_id ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="created_at"><th mat-header-cell *matHeaderCellDef>Time</th><td mat-cell *matCellDef="let l">{{ l.created_at | date:'dd MMM yyyy HH:mm' }}</td></ng-container>
          <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef></th>
            <td mat-cell *matCellDef="let l">
              <button mat-icon-button (click)="viewDiff(l)" matTooltip="View diff"><mat-icon>diff</mat-icon></button>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;" class="clickable" (click)="viewDiff(r)"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No audit logs found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:8px}.full-width{width:100%}.clickable{cursor:pointer}.clickable:hover{background:#f5f5f5}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class AuditLogListComponent implements OnInit {
  private readonly logService = inject(ActivityLogService);
  private readonly dialog = inject(MatDialog);

  readonly loading = signal(false);
  readonly logs = signal<ActivityLog[]>([]);
  readonly total = signal(0);
  readonly cols = ['actor', 'action', 'entity_type', 'entity_id', 'created_at', 'actions'];
  readonly entityCtrl = new FormControl('');
  readonly actionCtrl = new FormControl('');
  readonly dateFromCtrl = new FormControl('');
  private page = 1;

  ngOnInit(): void {
    this.load();
    [this.entityCtrl, this.actionCtrl, this.dateFromCtrl].forEach(ctrl =>
      ctrl.valueChanges.pipe(debounceTime(400), distinctUntilChanged()).subscribe(() => { this.page = 1; this.load(); })
    );
  }

  load(): void {
    this.loading.set(true);
    this.logService.list({
      page: this.page, per_page: 20,
      entity_type: this.entityCtrl.value ?? undefined,
      action: this.actionCtrl.value ?? undefined,
      date_from: this.dateFromCtrl.value ?? undefined,
    }).subscribe({
      next: res => { this.logs.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }

  viewDiff(log: ActivityLog): void {
    this.dialog.open(LogDiffDialogComponent, { data: log, width: '680px' });
  }
}
