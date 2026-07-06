import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar } from '@angular/material/snack-bar';
import { forkJoin } from 'rxjs';
import { EnrollmentService } from '../../../core/services/enrollment.service';
import { FamilyService } from '../../../core/services/family.service';
import { BatchService } from '../../../core/services/batch.service';
import { Family, Batch, FamilyMember } from '../../../core/models';

@Component({
  selector: 'app-enrollment-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>Enroll Member</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Family</mat-label>
          <mat-select formControlName="family_id" (selectionChange)="onFamilyChange($event.value)">
            <mat-option *ngFor="let f of families()" [value]="f.id">{{ f.family_name }} ({{ f.family_code }})</mat-option>
          </mat-select>
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Member</mat-label>
          <mat-select formControlName="member_id" [disabled]="!familyMembers().length">
            <mat-option *ngFor="let m of familyMembers()" [value]="m.member_id">
              {{ m.member?.first_name }} {{ m.member?.last_name }}
            </mat-option>
          </mat-select>
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Batch</mat-label>
          <mat-select formControlName="batch_id">
            <mat-option *ngFor="let b of batches()" [value]="b.id">{{ b.batch_name }} — {{ b.program?.name }}</mat-option>
          </mat-select>
          <mat-error>Required</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Notes (optional)</mat-label>
          <textarea matInput formControlName="notes" rows="2"></textarea>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">{{ saving ? 'Enrolling...' : 'Enroll' }}</button>
    </mat-dialog-actions>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;min-width:460px}.full-width{width:100%}`]
})
export class EnrollmentFormComponent implements OnInit {
  private readonly _data = inject(MAT_DIALOG_DATA);
  private readonly enrollmentService = inject(EnrollmentService);
  private readonly familyService = inject(FamilyService);
  private readonly batchService = inject(BatchService);
  private readonly dialogRef = inject(MatDialogRef<EnrollmentFormComponent>);
  private readonly snackBar = inject(MatSnackBar);

  readonly families = signal<Family[]>([]);
  readonly batches = signal<Batch[]>([]);
  readonly familyMembers = signal<FamilyMember[]>([]);
  saving = false;

  readonly form = new FormGroup({
    family_id: new FormControl<number | null>(null, [Validators.required]),
    member_id: new FormControl<number | null>(null, [Validators.required]),
    batch_id: new FormControl<number | null>(null, [Validators.required]),
    notes: new FormControl(''),
  });

  ngOnInit(): void {
    forkJoin({
      families: this.familyService.list({ per_page: 100 }),
      batches: this.batchService.list({ per_page: 100, status: 'active' }),
    }).subscribe(({ families, batches }) => {
      this.families.set(families.data);
      this.batches.set(batches.data);
    });
  }

  onFamilyChange(familyId: number): void {
    this.form.controls.member_id.setValue(null);
    this.familyService.getMembers(familyId).subscribe(res => this.familyMembers.set(res.data));
  }

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    this.enrollmentService.create({ family_id: val.family_id!, member_id: val.member_id!, batch_id: val.batch_id!, notes: val.notes ?? undefined }).subscribe({
      next: () => { this.snackBar.open('Enrolled successfully', 'Close', { duration: 3000 }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.snackBar.open(err?.error?.error?.message ?? 'Enrollment failed', 'Close', { duration: 4000 }); }
    });
  }
}
