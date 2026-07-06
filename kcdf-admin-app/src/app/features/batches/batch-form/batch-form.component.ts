import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatIconModule } from '@angular/material/icon';
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
    MatCardModule, MatFormFieldModule, MatInputModule, MatSelectModule,
    MatButtonModule, MatDatepickerModule, MatNativeDateModule, MatIconModule,
    PageHeaderComponent,
  ],
  template: `
    <app-page-header title="Create Batch">
      <button mat-stroked-button routerLink="/batches"><mat-icon>arrow_back</mat-icon> Back</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <form [formGroup]="form" class="form-grid">
          <mat-form-field appearance="outline">
            <mat-label>Batch Name</mat-label>
            <input matInput formControlName="batch_name" /><mat-error>Required</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Program</mat-label>
            <mat-select formControlName="program_id">
              <mat-option *ngFor="let p of programs()" [value]="p.id">{{ p.name }}</mat-option>
            </mat-select>
            <mat-error>Required</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Trainer</mat-label>
            <mat-select formControlName="trainer_id">
              <mat-option *ngFor="let t of trainers()" [value]="t.id">{{ t.member?.first_name }} {{ t.member?.last_name }}</mat-option>
            </mat-select>
            <mat-error>Required</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Capacity</mat-label>
            <input matInput formControlName="capacity" type="number" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Start Date</mat-label>
            <input matInput [matDatepicker]="sd" formControlName="start_date" />
            <mat-datepicker-toggle matSuffix [for]="sd"></mat-datepicker-toggle>
            <mat-datepicker #sd></mat-datepicker>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>End Date</mat-label>
            <input matInput [matDatepicker]="ed" formControlName="end_date" />
            <mat-datepicker-toggle matSuffix [for]="ed"></mat-datepicker-toggle>
            <mat-datepicker #ed></mat-datepicker>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Start Time</mat-label>
            <input matInput formControlName="start_time" type="time" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>End Time</mat-label>
            <input matInput formControlName="end_time" type="time" />
          </mat-form-field>
          <mat-form-field appearance="outline" style="grid-column:1/-1">
            <mat-label>Schedule Days (e.g. Mon, Wed, Fri)</mat-label>
            <input matInput formControlName="schedule_days" />
          </mat-form-field>
        </form>
      </mat-card-content>
      <mat-card-actions align="end">
        <button mat-button routerLink="/batches">Cancel</button>
        <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">
          {{ saving ? 'Saving...' : 'Create Batch' }}
        </button>
      </mat-card-actions>
    </mat-card>
  `,
  styles: [`.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}`]
})
export class BatchFormComponent implements OnInit {
  private readonly batchService = inject(BatchService);
  private readonly programService = inject(ProgramService);
  private readonly trainerService = inject(TrainerService);
  private readonly router = inject(Router);
  private readonly snackBar = inject(MatSnackBar);

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
        this.snackBar.open('Batch created', 'Close', { duration: 3000 });
        this.router.navigate(['/batches', res.data.id]);
      },
      error: (err) => {
        this.saving = false;
        this.snackBar.open(err?.error?.error?.message ?? 'Error creating batch', 'Close', { duration: 4000 });
      }
    });
  }
}
