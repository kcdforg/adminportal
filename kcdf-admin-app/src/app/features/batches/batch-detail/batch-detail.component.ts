import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTabsModule } from '@angular/material/tabs';
import { MatTableModule } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { forkJoin } from 'rxjs';
import { BatchService } from '../../../core/services/batch.service';
import { SessionService } from '../../../core/services/session.service';
import { Batch, Session, MemberProfile } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { SessionFormComponent } from '../../sessions/session-form/session-form.component';

@Component({
  selector: 'app-batch-detail',
  standalone: true,
  imports: [
    CommonModule, RouterModule,
    MatCardModule, MatButtonModule, MatIconModule, MatTabsModule, MatTableModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="batch()?.batch_name ?? 'Batch'" subtitle="Batch Detail">
      <button mat-stroked-button routerLink="/batches"><mat-icon>arrow_back</mat-icon> Back</button>
    </app-page-header>

    <mat-tab-group *ngIf="batch()">
      <mat-tab label="Info">
        <div class="tab-content">
          <div class="info-grid">
            <div class="info-item"><span class="info-label">Batch Name</span><span>{{ batch()!.batch_name }}</span></div>
            <div class="info-item"><span class="info-label">Program</span><span>{{ batch()!.program?.name ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Trainer</span><span>{{ batch()!.trainer?.member?.first_name }} {{ batch()!.trainer?.member?.last_name }}</span></div>
            <div class="info-item"><span class="info-label">Capacity</span><span>{{ batch()!.enrolled_count ?? 0 }} / {{ batch()!.capacity }}</span></div>
            <div class="info-item"><span class="info-label">Start Date</span><span>{{ batch()!.start_date | date:'dd MMM yyyy' }}</span></div>
            <div class="info-item"><span class="info-label">End Date</span><span>{{ (batch()!.end_date | date:'dd MMM yyyy') ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Schedule</span><span>{{ batch()!.schedule_days ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Status</span><app-status-badge [status]="batch()!.status"></app-status-badge></div>
          </div>
        </div>
      </mat-tab>

      <mat-tab label="Members ({{ members().length }})">
        <div class="tab-content">
          <table mat-table [dataSource]="members()" class="full-width">
            <ng-container matColumnDef="name"><th mat-header-cell *matHeaderCellDef>Name</th><td mat-cell *matCellDef="let m">{{ m.first_name }} {{ m.last_name }}</td></ng-container>
            <ng-container matColumnDef="email"><th mat-header-cell *matHeaderCellDef>Email</th><td mat-cell *matCellDef="let m">{{ m.email ?? '—' }}</td></ng-container>
            <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let m"><app-status-badge [status]="m.status"></app-status-badge></td></ng-container>
            <tr mat-header-row *matHeaderRowDef="memberCols"></tr>
            <tr mat-row *matRowDef="let r; columns: memberCols;"></tr>
            <tr class="mat-row" *matNoDataRow><td [colSpan]="memberCols.length" class="empty-row">No members enrolled</td></tr>
          </table>
        </div>
      </mat-tab>

      <mat-tab label="Sessions ({{ sessions().length }})">
        <div class="tab-content">
          <div class="tab-actions">
            <button mat-flat-button color="primary" (click)="addSession()">
              <mat-icon>add</mat-icon> Add Session
            </button>
          </div>
          <table mat-table [dataSource]="sessions()" class="full-width">
            <ng-container matColumnDef="session_date"><th mat-header-cell *matHeaderCellDef>Date</th><td mat-cell *matCellDef="let s">{{ s.session_date | date:'dd MMM yyyy' }}</td></ng-container>
            <ng-container matColumnDef="title"><th mat-header-cell *matHeaderCellDef>Title</th><td mat-cell *matCellDef="let s">{{ s.title ?? '—' }}</td></ng-container>
            <ng-container matColumnDef="session_type"><th mat-header-cell *matHeaderCellDef>Type</th><td mat-cell *matCellDef="let s">{{ s.session_type }}</td></ng-container>
            <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let s"><app-status-badge [status]="s.status"></app-status-badge></td></ng-container>
            <ng-container matColumnDef="locked"><th mat-header-cell *matHeaderCellDef>Locked</th>
              <td mat-cell *matCellDef="let s"><mat-icon [style.color]="s.attendance_locked ? '#2e7d32' : '#999'">{{ s.attendance_locked ? 'lock' : 'lock_open' }}</mat-icon></td>
            </ng-container>
            <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
              <td mat-cell *matCellDef="let s">
                <button mat-icon-button [routerLink]="['/sessions', s.id, 'attendance']" matTooltip="Attendance">
                  <mat-icon>fact_check</mat-icon>
                </button>
              </td>
            </ng-container>
            <tr mat-header-row *matHeaderRowDef="sessionCols"></tr>
            <tr mat-row *matRowDef="let r; columns: sessionCols;"></tr>
            <tr class="mat-row" *matNoDataRow><td [colSpan]="sessionCols.length" class="empty-row">No sessions yet</td></tr>
          </table>
        </div>
      </mat-tab>
    </mat-tab-group>
  `,
  styles: [`
    .tab-content { padding: 16px 0; }
    .tab-actions { margin-bottom: 16px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 16px 0; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: 600; }
    .full-width { width: 100%; }
    .empty-row { text-align: center; padding: 24px; color: #999; }
  `]
})
export class BatchDetailComponent implements OnInit {
  private readonly batchService = inject(BatchService);
  private readonly sessionService = inject(SessionService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(MatDialog);

  readonly loading = signal(false);
  readonly batch = signal<Batch | null>(null);
  readonly members = signal<MemberProfile[]>([]);
  readonly sessions = signal<Session[]>([]);
  readonly memberCols = ['name', 'email', 'status'];
  readonly sessionCols = ['session_date', 'title', 'session_type', 'status', 'locked', 'actions'];

  ngOnInit(): void { this.load(); }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({
      batch: this.batchService.get(id),
      members: this.batchService.getMembers(id),
      sessions: this.sessionService.getByBatch(id),
    }).subscribe({
      next: ({ batch, members, sessions }) => {
        this.batch.set(batch.data);
        this.members.set(members.data);
        this.sessions.set(sessions.data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  addSession(): void {
    const ref = this.dialog.open(SessionFormComponent, {
      width: '560px',
      data: { batch_id: this.batch()!.id }
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
