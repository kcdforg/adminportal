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
  template: `
    <h2 class="!mb-1 !text-xl !font-semibold !tracking-tight !text-gray-900 dark:!text-white">{{ trainer ? 'Edit Trainer' : 'Add Trainer' }}</h2>
    <div class="!max-h-[75vh] overflow-y-auto !text-gray-700 dark:!text-gray-200">
      <form [formGroup]="form" class="form-col">
        <div class="full-width" *ngIf="!trainer">
          <label for="trainer-member-id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Member ID</label>
          <input id="trainer-member-id" type="number" formControlName="member_id" placeholder="Enter member ID" required
            [attr.aria-invalid]="form.controls.member_id.touched && form.controls.member_id.invalid"
            [attr.aria-describedby]="form.controls.member_id.touched && form.controls.member_id.hasError('required') ? 'trainer-member-id-error' : null"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
            [class.border-rose-500]="form.controls.member_id.touched && form.controls.member_id.invalid" />
          <p *ngIf="form.controls.member_id.touched && form.controls.member_id.hasError('required')" id="trainer-member-id-error" class="mt-1 text-sm text-rose-600 dark:text-rose-400">Required</p>
        </div>
        <div class="full-width">
          <label for="trainer-specialization" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Specialization</label>
          <input id="trainer-specialization" type="text" formControlName="specialization" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
        </div>
        <div class="full-width">
          <label for="trainer-bio" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Bio</label>
          <textarea id="trainer-bio" formControlName="bio" rows="3" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"></textarea>
        </div>
        <div class="full-width" *ngIf="trainer">
          <label for="trainer-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
          <select id="trainer-status" formControlName="status" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </form>
    </div>
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" (click)="dialogRef.close()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
      <button type="button" (click)="save()" [disabled]="saving" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60">{{ saving ? 'Saving...' : 'Save' }}</button>
    </div>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;width:min(440px,calc(100vw - 64px))}.full-width{width:100%}`]
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
