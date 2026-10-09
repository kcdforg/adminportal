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
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="'Attendance: ' + (session()?.title ?? sessionDate())" subtitle="Mark session attendance">
      <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800" (click)="goBack()"><app-icon name="arrow_back" class="h-6 w-6"></app-icon> Back</button>
      <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-amber-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 disabled:cursor-not-allowed disabled:opacity-60" (click)="lockSession()" [disabled]="session()?.attendance_locked" *ngIf="session()">
        <app-icon name="lock" class="h-6 w-6"></app-icon> {{ session()!.attendance_locked ? 'Locked' : 'Lock Session' }}
      </button>
      <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60" (click)="saveAll()" [disabled]="saving || !!session()?.attendance_locked">
        <app-icon name="save" class="h-6 w-6"></app-icon> {{ saving ? 'Saving...' : 'Save All' }}
      </button>
    </app-page-header>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="flex flex-wrap items-center gap-x-6 gap-y-2 border-b border-gray-200 p-4 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300 sm:p-6" *ngIf="session()">
          <span>Date: <strong class="font-semibold text-gray-900 dark:text-gray-100">{{ session()!.session_date | date:'dd MMM yyyy' }}</strong></span>
          <span>Time: <strong class="font-semibold text-gray-900 dark:text-gray-100">{{ session()!.start_time }} – {{ session()!.end_time }}</strong></span>
          <span>Status: <app-status-badge [status]="session()!.status"></app-status-badge></span>
          <span *ngIf="session()!.attendance_locked" class="flex items-center gap-1 font-semibold text-indigo-700 dark:text-indigo-300"><app-icon name="lock" class="h-6 w-6"></app-icon> Locked</span>
      </div>

      <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[600px]">
          <thead>
            <tr>
              <th scope="col" class="!bg-gray-50 !px-4 !py-3 !text-left !text-xs !font-semibold !uppercase !tracking-wide !text-gray-500 dark:!bg-gray-800/60 dark:!text-gray-300">Member</th>
              <th scope="col" class="!bg-gray-50 !px-4 !py-3 !text-left !text-xs !font-semibold !uppercase !tracking-wide !text-gray-500 dark:!bg-gray-800/60 dark:!text-gray-300">Attendance</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let r of rows()">
              <td class="!px-4 !py-3 !text-sm !text-gray-700 dark:!text-gray-200">{{ r.name }}</td>
              <td class="!px-4 !py-2 !text-sm">
              <label class="sr-only" [for]="'attendance-status-' + r.member_id">Attendance for {{ r.name }}</label>
              <select [id]="'attendance-status-' + r.member_id" [formControl]="r.statusCtrl" [disabled]="!!session()?.attendance_locked" class="min-h-10 min-w-[130px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="excused">Excused</option>
              </select>
              </td>
            </tr>
            <tr *ngIf="rows().length === 0"><td colspan="2" class="!px-4 !py-12"><div class="text-center text-sm text-gray-500 dark:text-gray-400">No enrolled members</div></td></tr>
          </tbody>
        </table>
      </div>
    </section>
  `
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
