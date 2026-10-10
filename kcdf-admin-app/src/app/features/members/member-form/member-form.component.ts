import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';
import { ToastService } from '../../../shared/components/toast/toast.service';
import {
  TAILWIND_DIALOG_DATA,
  TAILWIND_DIALOG_REF,
  TailwindDialogRef,
} from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-member-form',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
  ],
  templateUrl: './member-form.component.html',
  styleUrl: './member-form.component.scss',
})
export class MemberFormComponent {
  readonly member = inject(TAILWIND_DIALOG_DATA) as MemberProfile | null;
  private readonly memberService = inject(MemberService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);

  saving = false;

  readonly form = new FormGroup({
    first_name: new FormControl(this.member?.first_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    last_name: new FormControl(this.member?.last_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    email: new FormControl(this.member?.email ?? '', { validators: [Validators.email] }),
    mobile: new FormControl(this.member?.mobile ?? ''),
    gender: new FormControl(this.member?.gender ?? ''),
    date_of_birth: new FormControl(this.member?.date_of_birth ?? ''),
    status: new FormControl(this.member?.status ?? 'active'),
  });

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.member
      ? this.memberService.update(this.member.id, val as never)
      : this.memberService.create(val as never);

    obs.subscribe({
      next: () => {
        this.toast.show(`Member ${this.member ? 'updated' : 'created'} successfully`, {
          variant: 'success',
          durationMs: 3000,
          actionLabel: 'Close',
        });
        this.dialogRef.close(true);
      },
      error: (err) => {
        this.saving = false;
        const msg = err?.error?.error?.message ?? 'Failed to save member';
        this.toast.show(msg, {
          variant: 'error',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      }
    });
  }
}
