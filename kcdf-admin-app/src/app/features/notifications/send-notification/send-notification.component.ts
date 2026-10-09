import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { NotificationService } from '../../../core/services/notification.service';
import { BatchService } from '../../../core/services/batch.service';
import { GroupService } from '../../../core/services/group.service';
import { Batch, Group } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';

@Component({
  selector: 'app-send-notification',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    AppIconComponent,
    PageHeaderComponent,
  ],
  template: `
    <app-page-header title="Send Notification">
      <button type="button" routerLink="/notifications" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="arrow_back" aria-hidden="true" class="h-6 w-6"></app-icon> Back</button>
    </app-page-header>
    <section class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form [formGroup]="form" class="flex flex-col gap-6">
          <fieldset>
            <legend class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Send To</legend>
            <div class="flex flex-wrap gap-x-5 gap-y-3">
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200"><input type="radio" formControlName="target_type" value="specific_members" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" /> Specific Members</label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200"><input type="radio" formControlName="target_type" value="batch" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" /> Batch</label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200"><input type="radio" formControlName="target_type" value="group" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" /> Group</label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200"><input type="radio" formControlName="target_type" value="all_families" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" /> All Families</label>
            </div>
          </fieldset>
          <label *ngIf="form.controls.target_type.value === 'batch'" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Batch
            <select formControlName="target_id" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option [ngValue]="null">Select a batch</option><option *ngFor="let b of batches()" [ngValue]="b.id">{{ b.batch_name }}</option>
            </select>
          </label>
          <label *ngIf="form.controls.target_type.value === 'group'" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Group
            <select formControlName="target_id" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option [ngValue]="null">Select a group</option><option *ngFor="let g of groups()" [ngValue]="g.id">{{ g.group_name }}</option>
            </select>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Title
            <input formControlName="title" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <span *ngIf="form.controls.title.touched && form.controls.title.invalid" class="mt-1 block text-sm text-red-600">Required</span>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Message
            <textarea formControlName="body" rows="4" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
            <span *ngIf="form.controls.body.touched && form.controls.body.invalid" class="mt-1 block text-sm text-red-600">Required</span>
          </label>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Channel
            <select formControlName="channel" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="in_app">In-App</option><option value="email">Email</option><option value="sms">SMS</option>
            </select>
          </label>
          <div class="flex justify-end gap-2">
            <button type="button" routerLink="/notifications" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
            <button type="button" (click)="send()" [disabled]="sending" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
              <app-icon name="send" aria-hidden="true" class="h-6 w-6"></app-icon> {{ sending ? 'Sending...' : 'Send' }}
            </button>
          </div>
        </form>
    </section>
  `,
})
export class SendNotificationComponent implements OnInit {
  private readonly notificationService = inject(NotificationService);
  private readonly batchService = inject(BatchService);
  private readonly groupService = inject(GroupService);
  private readonly toastService = inject(ToastService);
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
        this.toastService.show('Notification sent successfully', { variant: 'success', durationMs: 3000, actionLabel: 'Close' });
        this.router.navigate(['/notifications']);
      },
      error: (err) => {
        this.sending = false;
        this.toastService.show(err?.error?.error?.message ?? 'Failed to send notification', { variant: 'error', durationMs: 4000, actionLabel: 'Close' });
      }
    });
  }
}
