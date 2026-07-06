import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar } from '@angular/material/snack-bar';
import { SessionService } from '../../../core/services/session.service';
import { Session } from '../../../core/models';

interface SessionFormData {
  batch_id: number;
  session?: Session;
}

@Component({
  selector: 'app-session-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>{{ data.session ? 'Edit Session' : 'Add Session' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>Date</mat-label>
            <input matInput formControlName="session_date" type="date" /><mat-error>Required</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Session Type</mat-label>
            <mat-select formControlName="session_type">
              <mat-option value="regular">Regular</mat-option>
              <mat-option value="makeup">Makeup</mat-option>
              <mat-option value="assessment">Assessment</mat-option>
              <mat-option value="event">Event</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>Start Time</mat-label>
            <input matInput formControlName="start_time" type="time" /><mat-error>Required</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>End Time</mat-label>
            <input matInput formControlName="end_time" type="time" /><mat-error>Required</mat-error>
          </mat-form-field>
        </div>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Title</mat-label>
          <input matInput formControlName="title" />
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Topics Covered</mat-label>
          <textarea matInput formControlName="topics_covered" rows="2"></textarea>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Homework</mat-label>
          <textarea matInput formControlName="homework" rows="2"></textarea>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Notes</mat-label>
          <textarea matInput formControlName="notes" rows="2"></textarea>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width" *ngIf="data.session">
          <mat-label>Status</mat-label>
          <mat-select formControlName="status">
            <mat-option value="scheduled">Scheduled</mat-option>
            <mat-option value="completed">Completed</mat-option>
            <mat-option value="cancelled">Cancelled</mat-option>
          </mat-select>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
    </mat-dialog-actions>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;min-width:480px}.full-width{width:100%}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}`]
})
export class SessionFormComponent {
  readonly data: SessionFormData = inject(MAT_DIALOG_DATA);
  private readonly sessionService = inject(SessionService);
  private readonly dialogRef = inject(MatDialogRef<SessionFormComponent>);
  private readonly snackBar = inject(MatSnackBar);
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
      next: () => { this.snackBar.open('Session saved', 'Close', { duration: 3000 }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.snackBar.open(err?.error?.error?.message ?? 'Error', 'Close', { duration: 4000 }); }
    });
  }
}
