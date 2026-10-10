import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { FamilyService } from '../../../core/services/family.service';
import { CreateFamilyRequest, Family, FamilyStatus } from '../../../core/models';
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
  templateUrl: './family-form.component.html',
  styleUrl: './family-form.component.scss',
})
export class FamilyFormComponent {
  readonly family = inject(TAILWIND_DIALOG_DATA) as Family | null;
  private readonly familyService = inject(FamilyService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);

  saving = false;
  readonly saveError = signal<string | null>(null);
  readonly saveErrorDetails = signal<string[]>([]);

  readonly form = new FormGroup({
    family_code: new FormControl(this.family?.family_code ?? '', {
      nonNullable: true,
      validators: this.family ? [] : [Validators.required, Validators.pattern(/\S/), Validators.maxLength(50)],
    }),
    family_name: new FormControl(this.family?.family_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    status: new FormControl<FamilyStatus>(this.family?.status ?? 'active', { nonNullable: true }),
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
    this.saveError.set(null);
    this.saveErrorDetails.set([]);
    const val = this.form.getRawValue();
    const address = val.address;
    const hasAddress = this.family && [address.line1, address.line2, address.city, address.state, address.pincode]
      .some(value => value?.trim());

    if (hasAddress && (!address.line1?.trim() || !address.city?.trim())) {
      this.form.controls.address.markAllAsTouched();
      this.toast.show('Address line 1 and city are required when providing an address.', {
        variant: 'error',
        durationMs: 4000,
        actionLabel: 'Close',
      });
      return;
    }

    const familyData = { family_name: val.family_name.trim() };
    const updateData = {
      ...familyData,
      status: val.status,
      ...(hasAddress ? {
        address: {
          address_line_1: address.line1!.trim(),
          city: address.city!.trim(),
          country: address.country?.trim() || 'India',
          ...(address.line2?.trim() ? { address_line_2: address.line2.trim() } : {}),
          ...(address.state?.trim() ? { state: address.state.trim() } : {}),
          ...(address.pincode?.trim() ? { postal_code: address.pincode.trim() } : {}),
        }
      } : {}),
    };

    this.saving = true;
    const obs = this.family
      ? this.familyService.update(this.family.id, updateData)
      : this.familyService.create({ family_code: val.family_code.trim(), ...familyData });

    obs.subscribe({
      next: () => {
        this.toast.show(`Family ${this.family ? 'updated' : 'created'}`, {
          variant: 'success',
          durationMs: 3000,
          actionLabel: 'Close',
        });
        this.dialogRef.close(true);
      },
      error: (error: unknown) => {
        this.saving = false;
        const failure = this.getFailureMessage(error);
        this.saveError.set(failure.message);
        this.saveErrorDetails.set(failure.details);
        this.toast.show(failure.message, {
          variant: 'error',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      }
    });
  }

  private getFailureMessage(error: unknown): { message: string; details: string[] } {
    const response = this.asRecord(error);
    const payload = this.asRecord(response?.['error']);
    const body = this.asRecord(payload?.['error']) ?? payload;
    const message = typeof body?.['message'] === 'string' && body['message'].trim()
      ? body['message']
      : 'Unable to save family. Please try again.';
    const detailsRecord = this.asRecord(body?.['details']);
    const details = detailsRecord
      ? Object.values(detailsRecord).flatMap(value => Array.isArray(value) ? value : [])
        .filter((value): value is string => typeof value === 'string')
      : [];

    return { message, details };
  }

  private asRecord(value: unknown): Record<string, unknown> | null {
    return typeof value === 'object' && value !== null && !Array.isArray(value)
      ? value as Record<string, unknown>
      : null;
  }
}
