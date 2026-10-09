import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { PaymentService } from '../../../core/services/payment.service';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef } from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-payment-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <div class="px-6 pt-6 text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ data.isRefund ? 'Record Refund' : 'Record Payment' }}</div>
    <div class="px-6 py-4">
      <form [formGroup]="form" class="flex min-w-0 flex-col gap-4 sm:min-w-[28rem]">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Family
          <select formControlName="family_id" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option [ngValue]="null" disabled>Select a family</option>
            <option *ngFor="let f of families()" [ngValue]="f.id">{{ f.family_name }}</option>
          </select>
          <span *ngIf="form.controls.family_id.touched && form.controls.family_id.invalid" class="mt-1 block text-sm text-red-600">Required</span>
        </label>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ data.isRefund ? 'Refund Type' : 'Payment Type' }}
            <select formControlName="payment_type" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option *ngIf="!data.isRefund" value="class_fee">Class Fee</option>
              <option *ngIf="!data.isRefund" value="donation">Donation</option>
              <option *ngIf="!data.isRefund" value="event_fee">Event Fee</option>
              <option *ngIf="data.isRefund" value="refund">Refund</option>
            </select>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Amount (₹)
            <input formControlName="amount" type="number" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.amount.touched && form.controls.amount.invalid" class="mt-1 block text-sm text-red-600">Required</span>
          </label>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment Method
            <select formControlName="payment_method" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="cash">Cash</option><option value="bank_transfer">Bank Transfer</option><option value="upi">UPI</option><option value="cheque">Cheque</option><option value="other">Other</option>
            </select>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment Date
            <input formControlName="payment_date" type="date" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.payment_date.touched && form.controls.payment_date.invalid" class="mt-1 block text-sm text-red-600">Required</span>
          </label>
        </div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Reference Number
          <input formControlName="reference_number" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
        </label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notes
          <textarea formControlName="notes" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
        </label>
      </form>
    </div>
    <div class="flex justify-end gap-2 px-6 pb-5">
      <button type="button" (click)="dialogRef.close()" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
      <button (click)="save()" [disabled]="saving" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">{{ saving ? 'Saving...' : 'Save' }}</button>
    </div>
  `,
})
export class PaymentFormComponent implements OnInit {
  readonly data = inject(TAILWIND_DIALOG_DATA) as { isRefund: boolean };
  private readonly paymentService = inject(PaymentService);
  private readonly familyService = inject(FamilyService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toastService = inject(ToastService);
  readonly families = signal<Family[]>([]);
  saving = false;

  readonly form = new FormGroup({
    family_id: new FormControl<number | null>(null, [Validators.required]),
    payment_type: new FormControl(this.data.isRefund ? 'refund' : 'class_fee', { nonNullable: true }),
    amount: new FormControl(0, { nonNullable: true, validators: [Validators.required, Validators.min(0)] }),
    payment_method: new FormControl('cash', { nonNullable: true }),
    payment_date: new FormControl(new Date().toISOString().split('T')[0], { nonNullable: true, validators: [Validators.required] }),
    reference_number: new FormControl(''),
    notes: new FormControl(''),
  });

  ngOnInit(): void {
    this.familyService.list({ per_page: 100 }).subscribe(res => this.families.set(res.data));
  }

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    const obs = this.data.isRefund
      ? this.paymentService.refund(val as never)
      : this.paymentService.create(val as never);
    obs.subscribe({
      next: () => { this.toastService.show('Payment recorded', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toastService.show(err?.error?.error?.message ?? 'Error', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}
