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
  template: `
    <h2 class="!mb-1 !text-xl !font-semibold !tracking-tight !text-gray-900 dark:!text-white">Enroll Member</h2>
    <div class="!max-h-[75vh] overflow-y-auto !text-gray-700 dark:!text-gray-200">
      <form [formGroup]="form" class="form-col">
        <label for="enrollment-family" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Family
          <select id="enrollment-family" formControlName="family_id" required (change)="onFamilyChange(form.controls.family_id.value)" [attr.aria-invalid]="form.controls.family_id.touched && form.controls.family_id.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.family_id.touched && form.controls.family_id.hasError('required') ? 'enrollment-family-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option [ngValue]="null">Select Family</option>
            <option *ngFor="let f of families()" [ngValue]="f.id">{{ f.family_name }} ({{ f.family_code }})</option>
          </select>
          <span *ngIf="form.controls.family_id.touched && form.controls.family_id.hasError('required')" id="enrollment-family-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
        </label>
        <label for="enrollment-member" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Member
          <select id="enrollment-member" formControlName="member_id" required [disabled]="!familyMembers().length" [attr.aria-invalid]="form.controls.member_id.touched && form.controls.member_id.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.member_id.touched && form.controls.member_id.hasError('required') ? 'enrollment-member-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option [ngValue]="null">Select Member</option>
            <option *ngFor="let m of familyMembers()" [ngValue]="m.member_id">
              {{ m.member?.first_name }} {{ m.member?.last_name }}
            </option>
          </select>
          <span *ngIf="form.controls.member_id.touched && form.controls.member_id.hasError('required')" id="enrollment-member-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
        </label>
        <label for="enrollment-batch" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Batch
          <select id="enrollment-batch" formControlName="batch_id" required [attr.aria-invalid]="form.controls.batch_id.touched && form.controls.batch_id.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.batch_id.touched && form.controls.batch_id.hasError('required') ? 'enrollment-batch-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option [ngValue]="null">Select Batch</option>
            <option *ngFor="let b of batches()" [ngValue]="b.id">{{ b.batch_name }} — {{ b.program?.name }}</option>
          </select>
          <span *ngIf="form.controls.batch_id.touched && form.controls.batch_id.hasError('required')" id="enrollment-batch-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
        </label>
        <label for="enrollment-notes" class="block w-full text-sm font-medium text-gray-700 dark:text-gray-200">Notes (optional)
          <textarea id="enrollment-notes" formControlName="notes" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
        </label>
      </form>
    </div>
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-200 dark:hover:bg-gray-800" (click)="dialogRef.close()">Cancel</button>
      <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60" (click)="save()" [disabled]="saving">{{ saving ? 'Enrolling...' : 'Enroll' }}</button>
    </div>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;width:min(460px,calc(100vw - 64px))}.full-width{width:100%}`]
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
    }).subscribe(({ families, batches }) => {
      this.families.set(families.data);
      this.batches.set(batches.data);
    });
  }

  onFamilyChange(familyId: number | null): void {
    this.form.controls.member_id.setValue(null);
    if (familyId === null) {
      this.familyMembers.set([]);
      return;
    }
    this.familyService.getMembers(familyId).subscribe(res => this.familyMembers.set(res.data));
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
