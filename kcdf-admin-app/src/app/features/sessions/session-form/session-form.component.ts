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
  templateUrl: './session-form.component.html',
  styleUrl: './session-form.component.scss'
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
