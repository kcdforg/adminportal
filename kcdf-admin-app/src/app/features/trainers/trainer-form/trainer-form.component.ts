import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef } from '../../../shared/components/modal/tailwind-dialog.service';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';

@Component({
  selector: 'app-trainer-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './trainer-form.component.html',
  styleUrl: './trainer-form.component.scss'
})
export class TrainerFormComponent {
  readonly trainer: Trainer | null = inject(TAILWIND_DIALOG_DATA) as Trainer | null;
  private readonly trainerService = inject(TrainerService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);
  saving = false;

  readonly form = new FormGroup({
    member_id: new FormControl<number | null>(null, !this.trainer ? [Validators.required] : []),
    specialization: new FormControl(this.trainer?.specialization ?? ''),
    bio: new FormControl(this.trainer?.bio ?? ''),
    status: new FormControl(this.trainer?.status ?? 'active'),
  });

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.trainer
      ? this.trainerService.update(this.trainer.id, val as never)
      : this.trainerService.create({ member_id: val.member_id!, specialization: val.specialization ?? undefined, bio: val.bio ?? undefined });

    obs.subscribe({
      next: () => { this.toast.show('Trainer saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toast.show(err?.error?.error?.message ?? 'Error', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}
