import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { switchMap } from 'rxjs';
import { CreateAddressRequest, CreateMemberRequest, Gender, Trainer, TrainerStatus } from '../../../core/models';
import { MemberService } from '../../../core/services/member.service';
import { TrainerService } from '../../../core/services/trainer.service';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef } from '../../../shared/components/modal/tailwind-dialog.service';
import { ToastService } from '../../../shared/components/toast/toast.service';

@Component({
  selector: 'app-trainer-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './trainer-form.component.html',
  styleUrl: './trainer-form.component.scss',
})
export class TrainerFormComponent {
  readonly trainer: Trainer | null = inject(TAILWIND_DIALOG_DATA) as Trainer | null;
  private readonly memberService = inject(MemberService);
  private readonly trainerService = inject(TrainerService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);
  saving = false;

  readonly form = new FormGroup({
    first_name: new FormControl(this.trainer?.profile.first_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    last_name: new FormControl(this.trainer?.profile.last_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    date_of_birth: new FormControl(this.trainer?.profile.date_of_birth ?? ''),
    gender: new FormControl<Gender | ''>(this.trainer?.profile.gender ?? ''),
    mobile: new FormControl(this.trainer?.profile.mobile ?? ''),
    email: new FormControl(this.trainer?.profile.email ?? ''),
    address: new FormGroup({
      line1: new FormControl(this.trainer?.address?.address_line_1 ?? ''),
      line2: new FormControl(this.trainer?.address?.address_line_2 ?? ''),
      taluk: new FormControl(this.trainer?.address?.taluk ?? ''),
      village: new FormControl(this.trainer?.address?.village ?? ''),
      city: new FormControl(this.trainer?.address?.city ?? ''),
      state: new FormControl(this.trainer?.address?.state ?? ''),
      postal_code: new FormControl(this.trainer?.address?.postal_code ?? ''),
      country: new FormControl(this.trainer?.address?.country ?? 'India'),
    }),
    specialization: new FormControl(this.trainer?.specialization ?? ''),
    experience_years: new FormControl<number | null>(this.trainer?.experience_years ?? null, [Validators.min(0), Validators.max(60)]),
    joined_at: new FormControl(this.trainer?.joined_at?.slice(0, 10) ?? ''),
    bio: new FormControl(this.trainer?.bio ?? ''),
    status: new FormControl<TrainerStatus>(this.trainer?.status ?? 'active', { nonNullable: true }),
  });

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const value = this.form.getRawValue();
    const address = value.address;
    const hasAddress = [address.line1, address.line2, address.taluk, address.village, address.city, address.state, address.postal_code]
      .some(field => field?.trim());
    if (hasAddress && (!address.line1?.trim() || !address.city?.trim())) {
      if (!address.line1?.trim()) this.form.controls.address.controls.line1.markAsTouched();
      if (!address.city?.trim()) this.form.controls.address.controls.city.markAsTouched();
      this.toast.show('Address line 1 and city are required when providing an address.', {
        variant: 'error',
        durationMs: 4000,
        actionLabel: 'Close',
      });
      return;
    }

    const profileData: CreateMemberRequest = {
      first_name: value.first_name.trim(),
      last_name: value.last_name.trim(),
      date_of_birth: value.date_of_birth || null,
      gender: value.gender || null,
      mobile: value.mobile?.trim() || null,
      email: value.email?.trim() || null,
    };
    const addressData: CreateAddressRequest | undefined = hasAddress ? {
      address_line_1: address.line1!.trim(),
      city: address.city!.trim(),
      country: address.country?.trim() || 'India',
      ...(address.line2?.trim() ? { address_line_2: address.line2.trim() } : {}),
      ...(address.taluk?.trim() ? { taluk: address.taluk.trim() } : {}),
      ...(address.village?.trim() ? { village: address.village.trim() } : {}),
      ...(address.state?.trim() ? { state: address.state.trim() } : {}),
      ...(address.postal_code?.trim() ? { postal_code: address.postal_code.trim() } : {}),
    } : undefined;
    const trainerData = {
      specialization: value.specialization?.trim() || undefined,
      experience_years: value.experience_years,
      bio: value.bio?.trim() || undefined,
      joined_at: value.joined_at || null,
      ...(addressData ? { address: addressData } : {}),
      ...(this.trainer ? { status: value.status } : {}),
    };

    this.saving = true;
    const trainer = this.trainer;
    const operation = trainer
      ? this.memberService.update(trainer.profile_id, profileData).pipe(
        switchMap(() => this.trainerService.update(trainer.id, trainerData)),
      )
      : this.memberService.create(profileData).pipe(
        switchMap(response => this.trainerService.create({ profile_id: response.data.id, ...trainerData })),
      );

    operation.subscribe({
      next: () => {
        this.toast.show(`Trainer ${this.trainer ? 'updated' : 'created'} successfully`, {
          variant: 'success',
          durationMs: 3000,
          actionLabel: 'Close',
        });
        this.dialogRef.close(true);
      },
      error: error => {
        this.saving = false;
        this.toast.show(error?.error?.error?.message ?? 'Failed to save trainer', {
          variant: 'error',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      },
    });
  }
}
