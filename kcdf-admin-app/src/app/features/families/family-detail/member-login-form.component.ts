import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { FamilyMember, MemberLogin } from '../../../core/models';
import { MemberService } from '../../../core/services/member.service';
import { ToastService } from '../../../shared/components/toast/toast.service';
import {
  TAILWIND_DIALOG_DATA,
  TAILWIND_DIALOG_REF,
  TailwindDialogRef,
} from '../../../shared/components/modal/tailwind-dialog.service';

interface MemberLoginFormData {
  profile: Pick<FamilyMember['profile'], 'id' | 'first_name' | 'last_name'>;
  login: MemberLogin | null;
  familyCode: string;
}

@Component({
  selector: 'app-member-login-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './member-login-form.component.html',
})
export class MemberLoginFormComponent {
  readonly data = inject(TAILWIND_DIALOG_DATA) as MemberLoginFormData;
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly memberService = inject(MemberService);
  private readonly toast = inject(ToastService);
  saving = false;
  readonly isEditing = this.data.login !== null;

  readonly form = new FormGroup({
    username: new FormControl(this.data.login?.username ?? this.data.familyCode, { nonNullable: true, validators: [Validators.required] }),
    display_name: new FormControl(
      this.data.login?.display_name ?? `${this.data.profile.first_name} ${this.data.profile.last_name}`.trim(),
      { nonNullable: true, validators: [Validators.required] },
    ),
    password: new FormControl('', { nonNullable: true }),
    is_active: new FormControl(this.data.login ? Boolean(Number(this.data.login.is_active)) : true, { nonNullable: true }),
  });

  constructor() {
    if (!this.isEditing) {
      this.form.controls.password.addValidators([Validators.required]);
    }
  }

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    const value = this.form.getRawValue();
    this.saving = true;
    const request = {
      username: value.username.trim(),
      display_name: value.display_name.trim(),
      ...(value.password ? { password: value.password } : {}),
      is_active: value.is_active,
    };
    const operation = this.data.login
      ? this.memberService.updateLogin(this.data.profile.id, request)
      : this.memberService.createLogin(this.data.profile.id, {
        username: request.username,
        display_name: request.display_name,
        password: request.password ?? '',
      });
    operation.subscribe({
      next: () => {
        this.toast.show(`Member login ${this.data.login ? 'updated' : 'created'} successfully`, {
          variant: 'success',
          durationMs: 3000,
          actionLabel: 'Close',
        });
        this.dialogRef.close(true);
      },
      error: (error) => {
        this.saving = false;
        this.toast.show(error?.error?.error?.message ?? 'Failed to save member login', {
          variant: 'error',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      },
    });
  }
}
