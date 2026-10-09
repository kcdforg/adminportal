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
  template: `
    <h2 class="!mb-1 !text-xl !font-semibold !tracking-tight !text-gray-900 dark:!text-white">{{ program ? 'Edit Program' : 'Add Program' }}</h2>
    <div class="!max-h-[75vh] overflow-y-auto !text-gray-700 dark:!text-gray-200">
      <form [formGroup]="form" class="form-col">
        <div class="full-width">
          <label for="program-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Program Name</label>
          <input id="program-name" type="text" formControlName="name" required
            [attr.aria-invalid]="form.controls.name.touched && form.controls.name.invalid"
            [attr.aria-describedby]="form.controls.name.touched && form.controls.name.hasError('required') ? 'program-name-error' : null"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
            [class.border-rose-500]="form.controls.name.touched && form.controls.name.invalid" />
          <p *ngIf="form.controls.name.touched && form.controls.name.hasError('required')" id="program-name-error" class="mt-1 text-sm text-rose-600 dark:text-rose-400">Required</p>
        </div>
        <div class="full-width">
          <label for="program-description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
          <textarea id="program-description" formControlName="description" rows="2" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"></textarea>
        </div>
        <div class="form-row">
          <div>
            <label for="program-type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Type</label>
            <select id="program-type" formControlName="program_type" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
              <option value="quran">Quran</option>
              <option value="arabic">Arabic</option>
              <option value="islamic_studies">Islamic Studies</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div>
            <label for="program-fee-amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Fee Amount (₹)</label>
            <input id="program-fee-amount" type="number" formControlName="fee_amount" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
        </div>
        <div class="full-width">
          <label for="program-fee-frequency" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Fee Frequency</label>
          <select id="program-fee-frequency" formControlName="fee_frequency" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="annually">Annually</option>
            <option value="one_time">One Time</option>
          </select>
        </div>
        <div class="full-width" *ngIf="program">
          <label for="program-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
          <select id="program-status" formControlName="status" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="archived">Archived</option>
          </select>
        </div>
      </form>
    </div>
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" (click)="dialogRef.close()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
      <button type="button" (click)="save()" [disabled]="saving" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60">{{ saving ? 'Saving...' : 'Save' }}</button>
    </div>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;width:min(460px,calc(100vw - 64px))}.full-width{width:100%}.form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 16px}@media(max-width:520px){.form-row{grid-template-columns:1fr}}`]
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
