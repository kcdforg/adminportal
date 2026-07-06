import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';

@Component({
  selector: 'app-member-form',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatDatepickerModule,
    MatNativeDateModule,
  ],
  template: `
    <h2 mat-dialog-title>{{ member ? 'Edit Member' : 'Add Member' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-grid">
        <mat-form-field appearance="outline">
          <mat-label>First Name</mat-label>
          <input matInput formControlName="first_name" />
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline">
          <mat-label>Last Name</mat-label>
          <input matInput formControlName="last_name" />
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline">
          <mat-label>Email</mat-label>
          <input matInput formControlName="email" type="email" />
          <mat-error>Valid email required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline">
          <mat-label>Mobile</mat-label>
          <input matInput formControlName="mobile" />
        </mat-form-field>
        <mat-form-field appearance="outline">
          <mat-label>Gender</mat-label>
          <mat-select formControlName="gender">
            <mat-option value="male">Male</mat-option>
            <mat-option value="female">Female</mat-option>
            <mat-option value="other">Other</mat-option>
          </mat-select>
        </mat-form-field>
        <mat-form-field appearance="outline">
          <mat-label>Date of Birth</mat-label>
          <input matInput [matDatepicker]="dob" formControlName="date_of_birth" />
          <mat-datepicker-toggle matSuffix [for]="dob"></mat-datepicker-toggle>
          <mat-datepicker #dob></mat-datepicker>
        </mat-form-field>
        <mat-form-field appearance="outline" *ngIf="member">
          <mat-label>Status</mat-label>
          <mat-select formControlName="status">
            <mat-option value="active">Active</mat-option>
            <mat-option value="inactive">Inactive</mat-option>
            <mat-option value="suspended">Suspended</mat-option>
          </mat-select>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">
        {{ saving ? 'Saving...' : 'Save' }}
      </button>
    </mat-dialog-actions>
  `,
  styles: [`.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; min-width: 460px; }`]
})
export class MemberFormComponent {
  readonly member: MemberProfile | null = inject(MAT_DIALOG_DATA);
  private readonly memberService = inject(MemberService);
  private readonly dialogRef = inject(MatDialogRef<MemberFormComponent>);
  private readonly snackBar = inject(MatSnackBar);

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
        this.snackBar.open(`Member ${this.member ? 'updated' : 'created'} successfully`, 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: (err) => {
        this.saving = false;
        const msg = err?.error?.error?.message ?? 'Failed to save member';
        this.snackBar.open(msg, 'Close', { duration: 4000 });
      }
    });
  }
}
