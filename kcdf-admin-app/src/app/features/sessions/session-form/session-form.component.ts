import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef } from '../../../shared/components/modal/tailwind-dialog.service';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { SessionService } from '../../../core/services/session.service';
import { Session } from '../../../core/models';

interface SessionFormData {
  batch_id: number;
  session?: Session;
}

@Component({
  selector: 'app-session-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <h2 class="!mb-1 !text-xl !font-semibold !tracking-tight !text-gray-900 dark:!text-white">{{ data.session ? 'Edit Session' : 'Add Session' }}</h2>
    <div class="!max-h-[75vh] overflow-y-auto !text-gray-700 dark:!text-gray-200">
      <form [formGroup]="form" class="form-col">
        <div class="form-row">
          <label for="session-date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date
            <input id="session-date" formControlName="session_date" type="date" required [attr.aria-invalid]="form.controls.session_date.touched && form.controls.session_date.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.session_date.touched && form.controls.session_date.hasError('required') ? 'session-date-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.session_date.touched && form.controls.session_date.hasError('required')" id="session-date-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
          </label>
          <label for="session-type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Session Type
            <select id="session-type" formControlName="session_type" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="regular">Regular</option>
              <option value="makeup">Makeup</option>
              <option value="assessment">Assessment</option>
              <option value="event">Event</option>
            </select>
          </label>
        </div>
        <div class="form-row">
          <label for="session-start-time" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Time
            <input id="session-start-time" formControlName="start_time" type="time" required [attr.aria-invalid]="form.controls.start_time.touched && form.controls.start_time.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.start_time.touched && form.controls.start_time.hasError('required') ? 'session-start-time-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.start_time.touched && form.controls.start_time.hasError('required')" id="session-start-time-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
          </label>
          <label for="session-end-time" class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Time
            <input id="session-end-time" formControlName="end_time" type="time" required [attr.aria-invalid]="form.controls.end_time.touched && form.controls.end_time.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.end_time.touched && form.controls.end_time.hasError('required') ? 'session-end-time-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.end_time.touched && form.controls.end_time.hasError('required')" id="session-end-time-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
          </label>
        </div>
        <label for="session-title" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Title
          <input id="session-title" formControlName="title" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
        </label>
        <label for="session-topics" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Topics Covered
          <textarea id="session-topics" formControlName="topics_covered" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
        </label>
        <label for="session-homework" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Homework
          <textarea id="session-homework" formControlName="homework" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
        </label>
        <label for="session-notes" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Notes
          <textarea id="session-notes" formControlName="notes" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
        </label>
        <label for="session-status" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200" *ngIf="data.session">Status
          <select id="session-status" formControlName="status" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="scheduled">Scheduled</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </label>
      </form>
    </div>
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-200 dark:hover:bg-gray-800" (click)="dialogRef.close()">Cancel</button>
      <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60" (click)="save()" [disabled]="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
    </div>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;width:min(480px,calc(100vw - 64px))}.full-width{width:100%}.form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 16px}@media(max-width:520px){.form-row{grid-template-columns:1fr}}`]
})
export class SessionFormComponent {
  readonly data: SessionFormData = inject(TAILWIND_DIALOG_DATA) as SessionFormData;
  private readonly sessionService = inject(SessionService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);
  saving = false;

  readonly form = new FormGroup({
    session_date: new FormControl(this.data.session?.session_date ?? '', { nonNullable: true, validators: [Validators.required] }),
    start_time: new FormControl(this.data.session?.start_time ?? '', { nonNullable: true, validators: [Validators.required] }),
    end_time: new FormControl(this.data.session?.end_time ?? '', { nonNullable: true, validators: [Validators.required] }),
    session_type: new FormControl(this.data.session?.session_type ?? 'regular', { nonNullable: true }),
    title: new FormControl(this.data.session?.title ?? ''),
    topics_covered: new FormControl(this.data.session?.topics_covered ?? ''),
    homework: new FormControl(this.data.session?.homework ?? ''),
    notes: new FormControl(this.data.session?.notes ?? ''),
    status: new FormControl(this.data.session?.status ?? 'scheduled'),
  });

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.data.session
      ? this.sessionService.update(this.data.session.id, val as never)
      : this.sessionService.create({ ...val, batch_id: this.data.batch_id } as never);

    obs.subscribe({
      next: () => { this.toast.show('Session saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toast.show(err?.error?.error?.message ?? 'Error', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}
