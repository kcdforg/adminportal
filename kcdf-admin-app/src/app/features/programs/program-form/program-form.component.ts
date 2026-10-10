import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef } from '../../../shared/components/modal/tailwind-dialog.service';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { ProgramService } from '../../../core/services/program.service';
import { Program } from '../../../core/models';

@Component({
  selector: 'app-program-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './program-form.component.html',
  styleUrl: './program-form.component.scss'
})
export class ProgramFormComponent {
  readonly program: Program | null = inject(TAILWIND_DIALOG_DATA) as Program | null;
  private readonly programService = inject(ProgramService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);
  saving = false;

  readonly form = new FormGroup({
    name: new FormControl(this.program?.name ?? '', { nonNullable: true, validators: [Validators.required] }),
    description: new FormControl(this.program?.description ?? ''),
    program_type: new FormControl(this.program?.program_type ?? 'quran', { nonNullable: true }),
    fee_amount: new FormControl(this.program?.fee_amount ?? 0, { nonNullable: true }),
    fee_frequency: new FormControl(this.program?.fee_frequency ?? 'monthly', { nonNullable: true }),
    status: new FormControl(this.program?.status ?? 'active'),
  });

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.program ? this.programService.update(this.program.id, val as never) : this.programService.create(val as never);
    obs.subscribe({
      next: () => { this.toast.show('Program saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toast.show(err?.error?.error?.message ?? 'Error', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}
