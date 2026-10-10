import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef } from '../../../shared/components/modal/tailwind-dialog.service';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { forkJoin } from 'rxjs';
import { EnrollmentService } from '../../../core/services/enrollment.service';
import { FamilyService } from '../../../core/services/family.service';
import { BatchService } from '../../../core/services/batch.service';
import { Family, Batch, FamilyMember } from '../../../core/models';

@Component({
  selector: 'app-enrollment-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './enrollment-form.component.html',
  styleUrl: './enrollment-form.component.scss'
})
export class EnrollmentFormComponent implements OnInit {
  private readonly _data: null = inject(TAILWIND_DIALOG_DATA) as null;
  private readonly enrollmentService = inject(EnrollmentService);
  private readonly familyService = inject(FamilyService);
  private readonly batchService = inject(BatchService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toast = inject(ToastService);

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
    }).subscribe({
      next: ({ families, batches }) => {
        if (!Array.isArray(families.data) || !Array.isArray(batches.data)) {
          console.error('Failed to load enrollment form options: API response data must be arrays.');
          this.toast.show('Unable to load enrollment options. Please try again.', { variant: 'error', durationMs: 4000 });
          return;
        }
        this.families.set(families.data);
        this.batches.set(batches.data);
      },
      error: () => this.toast.show('Unable to load enrollment options. Please try again.', { variant: 'error', durationMs: 4000 }),
    });
  }

  onFamilyChange(familyId: number | null): void {
    this.form.controls.member_id.setValue(null);
    if (familyId === null) {
      this.familyMembers.set([]);
      return;
    }
    this.familyService.getMembers(familyId).subscribe({
      next: res => {
        if (!Array.isArray(res.data)) {
          console.error('Failed to load enrollment family members: API response data is not an array.');
          this.toast.show('Unable to load family members. Please try again.', { variant: 'error', durationMs: 4000 });
          return;
        }
        this.familyMembers.set(res.data);
      },
      error: () => this.toast.show('Unable to load family members. Please try again.', { variant: 'error', durationMs: 4000 }),
    });
  }

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    this.enrollmentService.create({ family_id: val.family_id!, member_id: val.member_id!, batch_id: val.batch_id!, notes: val.notes ?? undefined }).subscribe({
      next: () => { this.toast.show('Enrolled successfully', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toast.show(err?.error?.error?.message ?? 'Enrollment failed', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}
