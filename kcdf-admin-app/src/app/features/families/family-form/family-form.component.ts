import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';
import { ToastService } from '../../../shared/components/toast/toast.service';
import {
  TAILWIND_DIALOG_DATA,
  TAILWIND_DIALOG_REF,
  TailwindDialogRef,
} from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-family-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <h2 class="border-b border-gray-200 px-6 py-5 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white">{{ family ? 'Edit Family' : 'Create Family' }}</h2>
    <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
      <form [formGroup]="form" class="space-y-4">
        <div>
          <label for="family-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Family Name</label>
          <input id="family-name" type="text" formControlName="family_name" required
            [attr.aria-invalid]="form.controls.family_name.touched && form.controls.family_name.invalid"
            [attr.aria-describedby]="form.controls.family_name.touched && form.controls.family_name.hasError('required') ? 'family-name-error' : null"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
            [class.border-rose-500]="form.controls.family_name.touched && form.controls.family_name.invalid" />
          <p *ngIf="form.controls.family_name.touched && form.controls.family_name.hasError('required')" id="family-name-error" class="mt-1 text-sm text-rose-600 dark:text-rose-400">Required</p>
        </div>
        <div *ngIf="family">
          <label for="family-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
          <select id="family-status" formControlName="status"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
            <option value="active">Active</option><option value="inactive">Inactive</option><option value="suspended">Suspended</option>
          </select>
        </div>
        <div class="border-t border-gray-200 pt-4 dark:border-gray-800"></div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Address</p>
        <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2" formGroupName="address">
          <div>
            <label for="family-address-line1" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Line 1</label>
            <input id="family-address-line1" type="text" formControlName="line1"
              class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
          <div>
            <label for="family-address-line2" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Line 2</label>
            <input id="family-address-line2" type="text" formControlName="line2"
              class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
          <div>
            <label for="family-city" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">City</label>
            <input id="family-city" type="text" formControlName="city"
              class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
          <div>
            <label for="family-state" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">State</label>
            <input id="family-state" type="text" formControlName="state"
              class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
          <div>
            <label for="family-pincode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Pincode</label>
            <input id="family-pincode" type="text" formControlName="pincode"
              class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
          <div>
            <label for="family-country" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Country</label>
            <input id="family-country" type="text" formControlName="country"
              class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
          </div>
        </div>
      </form>
    </div>
    <div class="flex justify-end gap-2 border-t border-gray-200 px-6 py-4 dark:border-gray-800">
      <button type="button" (click)="dialogRef.close()"
        class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
      <button type="button" (click)="save()" [disabled]="saving"
        class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60">
        {{ saving ? 'Saving...' : 'Save' }}
      </button>
    </div>
  `,
})
export class FamilyFormComponent {
  readonly family = inject(TAILWIND_DIALOG_DATA) as Family | null;
  private readonly familyService = inject(FamilyService);
  private readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);

  saving = false;

  readonly form = new FormGroup({
    family_name: new FormControl(this.family?.family_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    status: new FormControl(this.family?.status ?? 'active'),
    address: new FormGroup({
      line1: new FormControl(this.family?.address?.line1 ?? ''),
      line2: new FormControl(this.family?.address?.line2 ?? ''),
      city: new FormControl(this.family?.address?.city ?? ''),
      state: new FormControl(this.family?.address?.state ?? ''),
      pincode: new FormControl(this.family?.address?.pincode ?? ''),
      country: new FormControl(this.family?.address?.country ?? 'India'),
    })
  });

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.family
      ? this.familyService.update(this.family.id, val as never)
      : this.familyService.create(val as never);

    obs.subscribe({
      next: () => {
        this.toast.show(`Family ${this.family ? 'updated' : 'created'}`, {
          variant: 'success',
          durationMs: 3000,
          actionLabel: 'Close',
        });
        this.dialogRef.close(true);
      },
      error: (err) => {
        this.saving = false;
        this.toast.show(err?.error?.error?.message ?? 'Error saving family', {
          variant: 'error',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      }
    });
  }
}
