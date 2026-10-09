import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { forkJoin } from 'rxjs';
import { BatchService } from '../../../core/services/batch.service';
import { ProgramService } from '../../../core/services/program.service';
import { TrainerService } from '../../../core/services/trainer.service';
import { Program, Trainer } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';

@Component({
  selector: 'app-batch-form',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    AppIconComponent,
    PageHeaderComponent,
  ],
  template: `
    <app-page-header title="Create Batch">
      <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800" routerLink="/batches"><app-icon name="arrow_back" class="h-6 w-6"></app-icon> Back</button>
    </app-page-header>
    <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="p-4 sm:p-6">
        <form [formGroup]="form" class="form-grid">
          <label for="batch-name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Batch Name
            <input id="batch-name" formControlName="batch_name" required [attr.aria-invalid]="form.controls.batch_name.touched && form.controls.batch_name.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.batch_name.touched && form.controls.batch_name.hasError('required') ? 'batch-name-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.batch_name.touched && form.controls.batch_name.hasError('required')" id="batch-name-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
          </label>
          <label for="batch-program" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Program
            <select id="batch-program" formControlName="program_id" required [attr.aria-invalid]="form.controls.program_id.touched && form.controls.program_id.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.program_id.touched && form.controls.program_id.hasError('required') ? 'batch-program-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option [ngValue]="null"></option>
              <option *ngFor="let p of programs()" [ngValue]="p.id">{{ p.name }}</option>
            </select>
            <span *ngIf="form.controls.program_id.touched && form.controls.program_id.hasError('required')" id="batch-program-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
          </label>
          <label for="batch-trainer" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Trainer
            <select id="batch-trainer" formControlName="trainer_id" required [attr.aria-invalid]="form.controls.trainer_id.touched && form.controls.trainer_id.invalid ? 'true' : null" [attr.aria-describedby]="form.controls.trainer_id.touched && form.controls.trainer_id.hasError('required') ? 'batch-trainer-error' : null" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option [ngValue]="null"></option>
              <option *ngFor="let t of trainers()" [ngValue]="t.id">{{ t.member?.first_name }} {{ t.member?.last_name }}</option>
            </select>
            <span *ngIf="form.controls.trainer_id.touched && form.controls.trainer_id.hasError('required')" id="batch-trainer-error" class="mt-1 block text-sm text-red-600" role="alert">Required</span>
          </label>
          <label for="batch-capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Capacity
            <input id="batch-capacity" formControlName="capacity" type="number" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </label>
          <label for="batch-start-date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Date
            <input id="batch-start-date" formControlName="start_date" type="date" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </label>
          <label for="batch-end-date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date
            <input id="batch-end-date" formControlName="end_date" type="date" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </label>
          <label for="batch-start-time" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Time
            <input id="batch-start-time" formControlName="start_time" type="time" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </label>
          <label for="batch-end-time" class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Time
            <input id="batch-end-time" formControlName="end_time" type="time" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </label>
          <label for="batch-schedule-days" class="col-span-full block text-sm font-medium text-gray-700 dark:text-gray-200">Schedule Days (e.g. Mon, Wed, Fri)
            <input id="batch-schedule-days" formControlName="schedule_days" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </label>
        </form>
      </div>
      <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-4 dark:border-gray-800 sm:px-6">
        <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-200 dark:hover:bg-gray-800" routerLink="/batches">Cancel</button>
        <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60" (click)="save()" [disabled]="saving">
          {{ saving ? 'Saving...' : 'Create Batch' }}
        </button>
      </div>
    </section>
  `,
  styles: [`.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 16px}@media(max-width:640px){.form-grid{grid-template-columns:1fr}}`]
})
export class BatchFormComponent implements OnInit {
  private readonly batchService = inject(BatchService);
  private readonly programService = inject(ProgramService);
  private readonly trainerService = inject(TrainerService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  readonly programs = signal<Program[]>([]);
  readonly trainers = signal<Trainer[]>([]);
  saving = false;

  readonly form = new FormGroup({
    batch_name: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    program_id: new FormControl<number | null>(null, [Validators.required]),
    trainer_id: new FormControl<number | null>(null, [Validators.required]),
    capacity: new FormControl(20, { nonNullable: true }),
    start_date: new FormControl('', { nonNullable: true }),
    end_date: new FormControl(''),
    start_time: new FormControl(''),
    end_time: new FormControl(''),
    schedule_days: new FormControl(''),
  });

  ngOnInit(): void {
    forkJoin({
      programs: this.programService.list({ per_page: 100 }),
      trainers: this.trainerService.list({ per_page: 100, status: 'active' }),
    }).subscribe(({ programs, trainers }) => {
      this.programs.set(programs.data);
      this.trainers.set(trainers.data);
    });
  }

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const val = this.form.getRawValue();
    this.batchService.create(val as never).subscribe({
      next: (res) => {
        this.toast.show('Batch created', { variant: 'success', durationMs: 3000, actionLabel: 'Close' });
        this.router.navigate(['/batches', res.data.id]);
      },
      error: (err) => {
        this.saving = false;
        this.toast.show(err?.error?.error?.message ?? 'Error creating batch', { variant: 'error', durationMs: 4000, actionLabel: 'Close' });
      }
    });
  }
}
