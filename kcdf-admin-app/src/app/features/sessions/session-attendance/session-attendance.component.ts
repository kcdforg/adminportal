import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { MatSelectModule } from '@angular/material/select';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AttendanceService, AttendanceRecord } from '../../../core/services/attendance.service';
import { SessionService } from '../../../core/services/session.service';
import { Attendance, Session, AttendanceStatus } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { forkJoin } from 'rxjs';

interface AttendanceRow {
  member_id: number;
  enrollment_id: number;
  name: string;
  statusCtrl: FormControl<AttendanceStatus>;
  notesCtrl: FormControl<string>;
}

@Component({
  selector: 'app-session-attendance',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatTableModule, MatSelectModule, MatFormFieldModule,
    PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="'Attendance: ' + (session()?.title ?? sessionDate())" subtitle="Mark session attendance">
      <button mat-stroked-button (click)="goBack()"><mat-icon>arrow_back</mat-icon> Back</button>
      <button mat-flat-button color="accent" (click)="lockSession()" [disabled]="session()?.attendance_locked" *ngIf="session()">
        <mat-icon>lock</mat-icon> {{ session()!.attendance_locked ? 'Locked' : 'Lock Session' }}
      </button>
      <button mat-flat-button color="primary" (click)="saveAll()" [disabled]="saving || !!session()?.attendance_locked">
        <mat-icon>save</mat-icon> {{ saving ? 'Saving...' : 'Save All' }}
      </button>
    </app-page-header>

    <mat-card>
      <mat-card-content>
        <div class="session-info" *ngIf="session()">
          <span>Date: <strong>{{ session()!.session_date | date:'dd MMM yyyy' }}</strong></span>
          <span>Time: <strong>{{ session()!.start_time }} – {{ session()!.end_time }}</strong></span>
          <span>Status: <app-status-badge [status]="session()!.status"></app-status-badge></span>
          <span *ngIf="session()!.attendance_locked" class="locked-badge"><mat-icon>lock</mat-icon> Locked</span>
        </div>

        <table mat-table [dataSource]="rows()" class="full-width">
          <ng-container matColumnDef="name">
            <th mat-header-cell *matHeaderCellDef>Member</th>
            <td mat-cell *matCellDef="let r">{{ r.name }}</td>
          </ng-container>
          <ng-container matColumnDef="status">
            <th mat-header-cell *matHeaderCellDef>Attendance</th>
            <td mat-cell *matCellDef="let r">
              <mat-select [formControl]="r.statusCtrl" [disabled]="!!session()?.attendance_locked">
                <mat-option value="present">Present</mat-option>
                <mat-option value="absent">Absent</mat-option>
                <mat-option value="late">Late</mat-option>
                <mat-option value="excused">Excused</mat-option>
              </mat-select>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No enrolled members</td></tr>
        </table>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`
    .session-info { display: flex; gap: 24px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; font-size: 14px; }
    .locked-badge { display: flex; align-items: center; gap: 4px; color: #1a237e; font-weight: 600; }
    .full-width { width: 100%; }
    .empty-row { text-align: center; padding: 32px; color: #999; }
    mat-select { min-width: 130px; }
  `]
})
export class SessionAttendanceComponent implements OnInit {
  private readonly attendanceService = inject(AttendanceService);
  private readonly sessionService = inject(SessionService);
  private readonly route = inject(ActivatedRoute);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  saving = false;
  readonly session = signal<Session | null>(null);
  readonly rows = signal<AttendanceRow[]>([]);
  readonly cols = ['name', 'status'];
  readonly sessionDate = () => this.session()?.session_date ?? '';

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({
      session: this.sessionService.get(id),
      attendance: this.attendanceService.getBySession(id),
    }).subscribe({
      next: ({ session, attendance }) => {
        this.session.set(session.data);
        this.rows.set(attendance.data.map(a => ({
          member_id: a.member_id,
          enrollment_id: a.enrollment_id,
          name: a.member ? `${a.member.first_name} ${a.member.last_name}` : `Member #${a.member_id}`,
          statusCtrl: new FormControl<AttendanceStatus>(a.status, { nonNullable: true }),
          notesCtrl: new FormControl<string>(a.notes ?? '', { nonNullable: true }),
        })));
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  saveAll(): void {
    this.saving = true;
    const sessionId = Number(this.route.snapshot.paramMap.get('id'));
    const records: AttendanceRecord[] = this.rows().map(r => ({
      enrollment_id: r.enrollment_id,
      member_id: r.member_id,
      status: r.statusCtrl.value,
      notes: r.notesCtrl.value || undefined,
    }));
    this.attendanceService.save(sessionId, records).subscribe({
      next: () => { this.saving = false; this.snackBar.open('Attendance saved', 'Close', { duration: 3000 }); },
      error: () => { this.saving = false; this.snackBar.open('Error saving attendance', 'Close', { duration: 4000 }); }
    });
  }

  lockSession(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.sessionService.lock(id).subscribe({
      next: (res) => { this.session.set(res.data); this.snackBar.open('Session locked', 'Close', { duration: 3000 }); },
      error: () => this.snackBar.open('Error locking session', 'Close', { duration: 3000 })
    });
  }

  goBack(): void { window.history.back(); }
}
