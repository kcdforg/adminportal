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
  templateUrl: './payment-form.component.html',
  styleUrl: './payment-form.component.scss',
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
    this.familyService.list({ per_page: 100 }).subscribe({
      next: res => {
        if (!Array.isArray(res.data)) {
          console.error('Failed to load payment form families: API response data is not an array.');
          this.toastService.show('Unable to load families. Please try again.', { variant: 'error', durationMs: 4000 });
          return;
        }
        this.families.set(res.data);
      },
      error: () => this.toastService.show('Unable to load families. Please try again.', { variant: 'error', durationMs: 4000 }),
    });
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
