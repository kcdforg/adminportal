import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { NotificationService } from '../../../core/services/notification.service';
import { BatchService } from '../../../core/services/batch.service';
import { GroupService } from '../../../core/services/group.service';
import { MemberService } from '../../../core/services/member.service';
import { Batch, Group, MemberProfile, NotificationChannel } from '../../../core/models';
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
  private readonly memberService = inject(MemberService);
  private readonly toastService = inject(ToastService);
  private readonly router = inject(Router);

  readonly batches = signal<Batch[]>([]);
  readonly groups = signal<Group[]>([]);
  readonly members = signal<MemberProfile[]>([]);
  readonly selectedMemberIds = signal<number[]>([]);
  sending = false;

  readonly form = new FormGroup({
    target_type: new FormControl<string>('all_families', { nonNullable: true }),
    target_id: new FormControl<number | null>(null),
    title: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    body: new FormControl('', { nonNullable: true, validators: [Validators.required] }),
    channel: new FormControl<NotificationChannel>('in_app', { nonNullable: true }),
  });

  ngOnInit(): void {
    this.batchService.list({ per_page: 100, status: 'active' }).subscribe(res => this.batches.set(res.data));
    this.groupService.list({ per_page: 100 }).subscribe(res => this.groups.set(res.data));
    this.memberService.list({ per_page: 100, status: 'active' }).subscribe({
      next: res => this.members.set(res.data),
      error: () => this.toastService.show('Unable to load members for notification delivery', {
        variant: 'error',
        durationMs: 4000,
        actionLabel: 'Close',
      }),
    });
  }

  toggleMember(memberId: number, event: Event): void {
    const checked = (event.target as HTMLInputElement).checked;
    const selected = new Set(this.selectedMemberIds());
    if (checked) {
      selected.add(memberId);
    } else {
      selected.delete(memberId);
    }
    this.selectedMemberIds.set([...selected]);
  }

  send(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    const val = this.form.getRawValue();
    if (val.target_type === 'specific_members' && this.selectedMemberIds().length === 0) {
      this.toastService.show('Select at least one member', {
        variant: 'error',
        durationMs: 3000,
        actionLabel: 'Close',
      });
      return;
    }
    if ((val.target_type === 'batch' || val.target_type === 'group') && !val.target_id) {
      this.toastService.show('Select a batch or group', {
        variant: 'error',
        durationMs: 3000,
        actionLabel: 'Close',
      });
      return;
    }

    this.sending = true;
    const payload = {
      title: val.title.trim(),
      message: val.body.trim(),
      type: val.channel,
    };
    const request = val.target_type === 'specific_members'
      ? this.notificationService.send({ ...payload, member_ids: this.selectedMemberIds() })
      : this.notificationService.broadcast({
        ...payload,
        target_type: val.target_type as 'batch' | 'group' | 'all_families',
        ...(val.target_id ? { target_id: val.target_id } : {}),
      });

    request.subscribe({
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
