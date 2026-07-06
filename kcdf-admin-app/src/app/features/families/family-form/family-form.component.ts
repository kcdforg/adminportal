import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDividerModule } from '@angular/material/divider';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';

@Component({
  selector: 'app-family-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule, MatDividerModule],
  template: `
    <h2 mat-dialog-title>{{ family ? 'Edit Family' : 'Create Family' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Family Name</mat-label>
          <input matInput formControlName="family_name" />
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width" *ngIf="family">
          <mat-label>Status</mat-label>
          <mat-select formControlName="status">
            <mat-option value="active">Active</mat-option>
            <mat-option value="inactive">Inactive</mat-option>
            <mat-option value="suspended">Suspended</mat-option>
          </mat-select>
        </mat-form-field>
        <mat-divider></mat-divider>
        <p class="section-label">Address</p>
        <div class="form-grid" formGroupName="address">
          <mat-form-field appearance="outline">
            <mat-label>Line 1</mat-label>
            <input matInput formControlName="line1" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Line 2</mat-label>
            <input matInput formControlName="line2" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>City</mat-label>
            <input matInput formControlName="city" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>State</mat-label>
            <input matInput formControlName="state" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Pincode</mat-label>
            <input matInput formControlName="pincode" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Country</mat-label>
            <input matInput formControlName="country" />
          </mat-form-field>
        </div>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">
        {{ saving ? 'Saving...' : 'Save' }}
      </button>
    </mat-dialog-actions>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;min-width:480px}.full-width{width:100%}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}.section-label{font-size:12px;font-weight:600;color:#666;text-transform:uppercase;margin:8px 0 0}`]
})
export class FamilyFormComponent {
  readonly family: Family | null = inject(MAT_DIALOG_DATA);
  private readonly familyService = inject(FamilyService);
  private readonly dialogRef = inject(MatDialogRef<FamilyFormComponent>);
  private readonly snackBar = inject(MatSnackBar);

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
        this.snackBar.open(`Family ${this.family ? 'updated' : 'created'}`, 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: (err) => {
        this.saving = false;
        this.snackBar.open(err?.error?.error?.message ?? 'Error saving family', 'Close', { duration: 4000 });
      }
    });
  }
}
