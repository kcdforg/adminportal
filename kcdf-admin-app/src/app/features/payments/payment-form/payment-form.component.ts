import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar } from '@angular/material/snack-bar';
import { PaymentService } from '../../../core/services/payment.service';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';

@Component({
  selector: 'app-payment-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>{{ data.isRefund ? 'Record Refund' : 'Record Payment' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Family</mat-label>
          <mat-select formControlName="family_id">
            <mat-option *ngFor="let f of families()" [value]="f.id">{{ f.family_name }}</mat-option>
          </mat-select>
          <mat-error>Required</mat-error>
        </mat-form-field>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>{{ data.isRefund ? 'Refund Type' : 'Payment Type' }}</mat-label>
            <mat-select formControlName="payment_type">
              <mat-option *ngIf="!data.isRefund" value="class_fee">Class Fee</mat-option>
              <mat-option *ngIf="!data.isRefund" value="donation">Donation</mat-option>
              <mat-option *ngIf="!data.isRefund" value="event_fee">Event Fee</mat-option>
              <mat-option *ngIf="data.isRefund" value="refund">Refund</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Amount (₹)</mat-label>
            <input matInput formControlName="amount" type="number" /><mat-error>Required</mat-error>
          </mat-form-field>
        </div>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>Payment Method</mat-label>
            <mat-select formControlName="payment_method">
              <mat-option value="cash">Cash</mat-option>
              <mat-option value="bank_transfer">Bank Transfer</mat-option>
              <mat-option value="upi">UPI</mat-option>
              <mat-option value="cheque">Cheque</mat-option>
              <mat-option value="other">Other</mat-option>
            </mat-select>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Payment Date</mat-label>
            <input matInput formControlName="payment_date" type="date" /><mat-error>Required</mat-error>
          </mat-form-field>
        </div>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Reference Number</mat-label>
          <input matInput formControlName="reference_number" />
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Notes</mat-label>
          <textarea matInput formControlName="notes" rows="2"></textarea>
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
export class PaymentFormComponent implements OnInit {
  readonly data: { isRefund: boolean } = inject(MAT_DIALOG_DATA);
  private readonly paymentService = inject(PaymentService);
  private readonly familyService = inject(FamilyService);
  private readonly dialogRef = inject(MatDialogRef<PaymentFormComponent>);
  private readonly snackBar = inject(MatSnackBar);
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
      next: () => { this.snackBar.open('Payment recorded', 'Close', { duration: 3000 }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.snackBar.open(err?.error?.error?.message ?? 'Error', 'Close', { duration: 4000 }); }
    });
  }
}
