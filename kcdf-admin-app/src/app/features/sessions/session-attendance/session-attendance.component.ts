import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { AttendanceService, AttendanceRecord } from '../../../core/services/attendance.service';
import { SessionService } from '../../../core/services/session.service';
import { Attendance, Session, AttendanceStatus } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { forkJoin } from 'rxjs';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';

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
    AppIconComponent,
    PageHeaderComponent, LoadingOverlayComponent, StatusBadgeComponent,
  ],
  templateUrl: './session-attendance.component.html',
  styleUrl: './session-attendance.component.scss'
})
export class SessionAttendanceComponent implements OnInit {
  private readonly attendanceService = inject(AttendanceService);
  private readonly sessionService = inject(SessionService);
  private readonly route = inject(ActivatedRoute);
  private readonly toast = inject(ToastService);

  readonly loading = signal(false);
  saving = false;
  readonly session = signal<Session | null>(null);
  readonly rows = signal<AttendanceRow[]>([]);
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
      next: () => { this.saving = false; this.toast.show('Attendance saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); },
      error: () => { this.saving = false; this.toast.show('Error saving attendance', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }

  lockSession(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.sessionService.lock(id).subscribe({
      next: (res) => { this.session.set(res.data); this.toast.show('Session locked', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); },
      error: () => this.toast.show('Error locking session', { variant: 'error', durationMs: 3000, actionLabel: 'Close' })
    });
  }

  goBack(): void { window.history.back(); }
}
