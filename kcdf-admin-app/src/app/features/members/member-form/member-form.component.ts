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
  template: `
    <h2 class="border-b border-gray-200 px-6 py-5 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white">{{ member ? 'Edit Member' : 'Add Member' }}</h2>
    <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
      <form [formGroup]="form" class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
        <div>
          <label for="member-first-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">First Name</label>
          <input id="member-first-name" type="text" formControlName="first_name" required
            [attr.aria-invalid]="form.controls.first_name.touched && form.controls.first_name.invalid"
            [attr.aria-describedby]="form.controls.first_name.touched && form.controls.first_name.hasError('required') ? 'member-first-name-error' : null"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
            [class.border-rose-500]="form.controls.first_name.touched && form.controls.first_name.invalid" />
          <p *ngIf="form.controls.first_name.touched && form.controls.first_name.hasError('required')" id="member-first-name-error" class="mt-1 text-sm text-rose-600 dark:text-rose-400">Required</p>
        </div>
        <div>
          <label for="member-last-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Last Name</label>
          <input id="member-last-name" type="text" formControlName="last_name" required
            [attr.aria-invalid]="form.controls.last_name.touched && form.controls.last_name.invalid"
            [attr.aria-describedby]="form.controls.last_name.touched && form.controls.last_name.hasError('required') ? 'member-last-name-error' : null"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
            [class.border-rose-500]="form.controls.last_name.touched && form.controls.last_name.invalid" />
          <p *ngIf="form.controls.last_name.touched && form.controls.last_name.hasError('required')" id="member-last-name-error" class="mt-1 text-sm text-rose-600 dark:text-rose-400">Required</p>
        </div>
        <div>
          <label for="member-email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
          <input id="member-email" type="email" formControlName="email"
            [attr.aria-invalid]="form.controls.email.touched && form.controls.email.invalid"
            [attr.aria-describedby]="form.controls.email.touched && form.controls.email.hasError('email') ? 'member-email-error' : null"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
            [class.border-rose-500]="form.controls.email.touched && form.controls.email.invalid" />
          <p *ngIf="form.controls.email.touched && form.controls.email.hasError('email')" id="member-email-error" class="mt-1 text-sm text-rose-600 dark:text-rose-400">Valid email required</p>
        </div>
        <div>
          <label for="member-mobile" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Mobile</label>
          <input id="member-mobile" type="text" formControlName="mobile"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
        </div>
        <div>
          <label for="member-gender" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Gender</label>
          <select id="member-gender" formControlName="gender"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
            <option value="male">Male</option><option value="female">Female</option><option value="other">Other</option>
          </select>
        </div>
        <div>
          <label for="member-date-of-birth" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Date of Birth</label>
          <input id="member-date-of-birth" type="date" formControlName="date_of_birth"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white" />
        </div>
        <div *ngIf="member">
          <label for="member-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
          <select id="member-status" formControlName="status"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
            <option value="active">Active</option><option value="inactive">Inactive</option><option value="suspended">Suspended</option>
          </select>
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
export class MemberFormComponent {
  readonly member = inject(TAILWIND_DIALOG_DATA) as MemberProfile | null;
  private readonly memberService = inject(MemberService);
  private readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
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
