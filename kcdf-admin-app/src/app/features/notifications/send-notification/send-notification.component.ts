import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatRadioModule } from '@angular/material/radio';
import { MatSnackBar } from '@angular/material/snack-bar';
import { NotificationService } from '../../../core/services/notification.service';
import { BatchService } from '../../../core/services/batch.service';
import { GroupService } from '../../../core/services/group.service';
import { Batch, Group } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';

@Component({
  selector: 'app-send-notification',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    MatCardModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule, MatIconModule, MatRadioModule,
    PageHeaderComponent,
  ],
  template: `
    <app-page-header title="Send Notification">
      <button mat-stroked-button routerLink="/notifications"><mat-icon>arrow_back</mat-icon> Back</button>
    </app-page-header>
    <mat-card style="max-width:600px">
      <mat-card-content>
        <form [formGroup]="form" class="form-col">
          <div>
            <p class="section-label">Send To</p>
            <mat-radio-group formControlName="target_type" class="radio-group">
              <mat-radio-button value="specific_members">Specific Members</mat-radio-button>
              <mat-radio-button value="batch">Batch</mat-radio-button>
              <mat-radio-button value="group">Group</mat-radio-button>
              <mat-radio-button value="all_families">All Families</mat-radio-button>
            </mat-radio-group>
          </div>

          <mat-form-field appearance="outline" class="full-width" *ngIf="form.controls.target_type.value === 'batch'">
            <mat-label>Select Batch</mat-label>
            <mat-select formControlName="target_id">
              <mat-option *ngFor="let b of batches()" [value]="b.id">{{ b.batch_name }}</mat-option>
            </mat-select>
          </mat-form-field>

          <mat-form-field appearance="outline" class="full-width" *ngIf="form.controls.target_type.value === 'group'">
            <mat-label>Select Group</mat-label>
            <mat-select formControlName="target_id">
              <mat-option *ngFor="let g of groups()" [value]="g.id">{{ g.group_name }}</mat-option>
            </mat-select>
          </mat-form-field>

          <mat-form-field appearance="outline" class="full-width">
            <mat-label>Title</mat-label>
            <input matInput formControlName="title" /><mat-error>Required</mat-error>
          </mat-form-field>

          <mat-form-field appearance="outline" class="full-width">
            <mat-label>Message</mat-label>
            <textarea matInput formControlName="body" rows="4"></textarea><mat-error>Required</mat-error>
          </mat-form-field>

          <mat-form-field appearance="outline" class="full-width">
            <mat-label>Channel</mat-label>
            <mat-select formControlName="channel">
              <mat-option value="in_app">In-App</mat-option>
              <mat-option value="email">Email</mat-option>
              <mat-option value="sms">SMS</mat-option>
            </mat-select>
          </mat-form-field>

          <div class="actions">
            <button mat-button routerLink="/notifications">Cancel</button>
            <button mat-flat-button color="primary" (click)="send()" [disabled]="sending">
              <mat-icon>send</mat-icon> {{ sending ? 'Sending...' : 'Send' }}
            </button>
          </div>
        </form>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:16px}.full-width{width:100%}.section-label{font-size:12px;font-weight:600;color:#666;text-transform:uppercase;margin:0 0 8px}.radio-group{display:flex;gap:16px;flex-wrap:wrap}.actions{display:flex;gap:8px;justify-content:flex-end}`]
})
export class SendNotificationComponent implements OnInit {
  private readonly notificationService = inject(NotificationService);
  private readonly batchService = inject(BatchService);
  private readonly groupService = inject(GroupService);
  private readonly snackBar = inject(MatSnackBar);
  private readonly router = inject(Router);

  readonly batches = signal<Batch[]>([]);
  readonly groups = signal<Group[]>([]);
  sending = false;

  readonly form = new FormGroup({
    target_type: new FormControl<string>('all_families', { nonNullable: true }),
    target_id: new FormControl<number | null>(null),
    title: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    body: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    channel: new FormControl('in_app', { nonNullable: true }),
  });

  ngOnInit(): void {
    this.batchService.list({ per_page: 100, status: 'active' }).subscribe(res => this.batches.set(res.data));
    this.groupService.list({ per_page: 100 }).subscribe(res => this.groups.set(res.data));
  }

  send(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.sending = true;
    const val = this.form.getRawValue();
    const payload = val.target_type === 'specific_members'
      ? { title: val.title, body: val.body, channel: val.channel as never }
      : { title: val.title, body: val.body, channel: val.channel as never, target_type: val.target_type as never, target_id: val.target_id ?? undefined };

    this.notificationService.send(payload).subscribe({
      next: () => {
        this.sending = false;
        this.snackBar.open('Notification sent successfully', 'Close', { duration: 3000 });
        this.router.navigate(['/notifications']);
      },
      error: (err) => {
        this.sending = false;
        this.snackBar.open(err?.error?.error?.message ?? 'Failed to send notification', 'Close', { duration: 4000 });
      }
    });
  }
}
