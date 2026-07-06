import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ProgramService } from '../../../core/services/program.service';
import { Program } from '../../../core/models';

@Component({
  selector: 'app-program-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>{{ program ? 'Edit Program' : 'Add Program' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Program Name</mat-label>
          <input matInput formControlName="name" /><mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Description</mat-label>
          <textarea matInput formControlName="description" rows="2"></textarea>
        </mat-form-field>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>Type</mat-label>
            <mat-select formControlName="program_type">
              <mat-option value="quran">Quran</mat-option>
              <mat-option value="arabic">Arabic</mat-option>
              <mat-option value="islamic_studies">Islamic Studies</mat-option>
              <mat-option value="other">Other</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Fee Amount (₹)</mat-label>
            <input matInput formControlName="fee_amount" type="number" />
          </mat-form-field>
        </div>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Fee Frequency</mat-label>
          <mat-select formControlName="fee_frequency">
            <mat-option value="monthly">Monthly</mat-option>
            <mat-option value="quarterly">Quarterly</mat-option>
            <mat-option value="annually">Annually</mat-option>
            <mat-option value="one_time">One Time</mat-option>
          </mat-select>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width" *ngIf="program">
          <mat-label>Status</mat-label>
          <mat-select formControlName="status">
            <mat-option value="active">Active</mat-option>
            <mat-option value="inactive">Inactive</mat-option>
            <mat-option value="archived">Archived</mat-option>
          </mat-select>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
    </mat-dialog-actions>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;min-width:460px}.full-width{width:100%}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}`]
})
export class ProgramFormComponent {
  readonly program: Program | null = inject(MAT_DIALOG_DATA);
  private readonly programService = inject(ProgramService);
  private readonly dialogRef = inject(MatDialogRef<ProgramFormComponent>);
  private readonly snackBar = inject(MatSnackBar);
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
      next: () => { this.snackBar.open('Program saved', 'Close', { duration: 3000 }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.snackBar.open(err?.error?.error?.message ?? 'Error', 'Close', { duration: 4000 }); }
    });
  }
}
