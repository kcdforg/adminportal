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
  templateUrl: './batch-form.component.html',
  styleUrl: './batch-form.component.scss'
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
