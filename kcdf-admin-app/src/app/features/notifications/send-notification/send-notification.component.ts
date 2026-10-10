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
  templateUrl: './send-notification.component.html',
  styleUrl: './send-notification.component.scss',
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
