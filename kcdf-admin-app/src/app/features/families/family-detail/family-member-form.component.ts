import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { FamilyMember, FamilyMemberRole, Gender } from '../../../core/models';
import { FamilyService } from '../../../core/services/family.service';
import { ToastService } from '../../../shared/components/toast/toast.service';
import {
  TAILWIND_DIALOG_DATA,
  TAILWIND_DIALOG_REF,
  TailwindDialogRef,
} from '../../../shared/components/modal/tailwind-dialog.service';

type RelationshipType = FamilyMember['relationship_type'];
type FamilyMemberFormData = {
  familyId: number;
  relationshipType: RelationshipType;
  title?: string;
  gender?: Gender;
  member?: FamilyMember;
};

function isMemberRole(value: FamilyMemberRole | ''): value is FamilyMemberRole {
  return value === 'primary' || value === 'normal' || value === 'student';
}

@Component({
  selector: 'app-family-member-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './family-member-form.component.html',
})
export class FamilyMemberFormComponent {
  readonly data = inject(TAILWIND_DIALOG_DATA) as FamilyMemberFormData;
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly familyService = inject(FamilyService);
  private readonly toast = inject(ToastService);
  saving = false;

  readonly form = new FormGroup({
    first_name: new FormControl(this.data.member?.profile.first_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    last_name: new FormControl(this.data.member?.profile.last_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    email: new FormControl(this.data.member?.profile.email ?? ''),
    mobile: new FormControl(this.data.member?.profile.mobile ?? ''),
    date_of_birth: new FormControl(this.data.member?.profile.date_of_birth ?? ''),
    member_role: new FormControl<FamilyMemberRole | ''>(this.data.member?.member_role ?? '', { nonNullable: true, validators: [Validators.required] }),
  });

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    const value = this.form.getRawValue();
    if (!isMemberRole(value.member_role)) {
      this.form.controls.member_role.markAsTouched();
      return;
    }
    this.saving = true;
    const request = {
      first_name: value.first_name.trim(),
      last_name: value.last_name.trim(),
      email: value.email?.trim() || null,
      mobile: value.mobile?.trim() || null,
      gender: this.data.member?.profile.gender ?? this.data.gender ?? null,
      date_of_birth: value.date_of_birth || null,
      relationship_type: this.data.member?.relationship_type ?? this.data.relationshipType,
      member_role: value.member_role,
    };
    const operation = this.data.member
      ? this.familyService.updateMember(this.data.familyId, this.data.member.profile_id, request)
      : this.familyService.addMember(this.data.familyId, request);
    operation.subscribe({
      next: () => {
        this.toast.show(`Family member ${this.data.member ? 'updated' : 'added'} successfully`, {
          variant: 'success',
          durationMs: 3000,
          actionLabel: 'Close',
        });
        this.dialogRef.close(true);
      },
      error: (error) => {
        this.saving = false;
        this.toast.show(error?.error?.error?.message ?? 'Failed to save family member', {
          variant: 'error',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      },
    });
  }

  genderLabel(gender: Gender | null): string {
    return gender ? `${gender.charAt(0).toUpperCase()}${gender.slice(1)}` : 'Not specified';
  }

  relationshipLabel(relationship: RelationshipType): string {
    return ({
      father: 'Husband / Father',
      mother: 'Wife / Mother',
      guardian: 'Guardian',
      child: 'Child',
    })[relationship];
  }
}
