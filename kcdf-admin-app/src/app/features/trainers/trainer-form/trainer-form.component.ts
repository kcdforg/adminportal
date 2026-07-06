import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar } from '@angular/material/snack-bar';
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';

@Component({
  selector: 'app-trainer-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>{{ trainer ? 'Edit Trainer' : 'Add Trainer' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <mat-form-field appearance="outline" class="full-width" *ngIf="!trainer">
          <mat-label>Member ID</mat-label>
          <input matInput formControlName="member_id" type="number" placeholder="Enter member ID" />
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Specialization</mat-label>
          <input matInput formControlName="specialization" />
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Bio</mat-label>
          <textarea matInput formControlName="bio" rows="3"></textarea>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width" *ngIf="trainer">
          <mat-label>Status</mat-label>
          <mat-select formControlName="status">
            <mat-option value="active">Active</mat-option>
            <mat-option value="inactive">Inactive</mat-option>
          </mat-select>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
    </mat-dialog-actions>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;min-width:440px}.full-width{width:100%}`]
})
export class TrainerFormComponent {
  readonly trainer: Trainer | null = inject(MAT_DIALOG_DATA);
  private readonly trainerService = inject(TrainerService);
  private readonly dialogRef = inject(MatDialogRef<TrainerFormComponent>);
  private readonly snackBar = inject(MatSnackBar);
  saving = false;

  readonly form = new FormGroup({
    member_id: new FormControl<number | null>(null, !this.trainer ? [Validators.required] : []),
    specialization: new FormControl(this.trainer?.specialization ?? ''),
    bio: new FormControl(this.trainer?.bio ?? ''),
    status: new FormControl(this.trainer?.status ?? 'active'),
  });

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.trainer
      ? this.trainerService.update(this.trainer.id, val as never)
      : this.trainerService.create({ member_id: val.member_id!, specialization: val.specialization ?? undefined, bio: val.bio ?? undefined });

    obs.subscribe({
      next: () => { this.snackBar.open('Trainer saved', 'Close', { duration: 3000 }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.snackBar.open(err?.error?.error?.message ?? 'Error', 'Close', { duration: 4000 }); }
    });
  }
}
