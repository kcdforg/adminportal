import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { GroupService } from '../../../core/services/group.service';
import { Group, GroupMember } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-group-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, AppIconComponent, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  templateUrl: './group-detail.component.html',
  styleUrl: './group-detail.component.scss',
})
export class GroupDetailComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toastService = inject(ToastService);
  readonly loading = signal(false);
  readonly group = signal<Group | null>(null);
  readonly members = signal<GroupMember[]>([]);
  readonly cols = ['name', 'joined', 'banned', 'actions'];

  ngOnInit(): void { this.load(); }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({ group: this.groupService.get(id), members: this.groupService.getMembers(id) }).subscribe({
      next: ({ group, members }) => { this.group.set(group.data); this.members.set(members.data); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  banMember(member: GroupMember): void {
    const ref = this.dialog.open<ConfirmDialogComponent, { title: string; message: string; danger: boolean }, boolean>(ConfirmDialogComponent, { data: { title: 'Ban Member', message: 'Ban this member from the group?', danger: true }, ariaLabel: 'Confirm banning group member' });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.banMember(this.group()!.id, member.member_id).subscribe({ next: () => { this.toastService.show('Member banned', { variant: 'success', durationMs: 2000, actionLabel: 'Close' }); this.load(); } });
    });
  }

  removeMember(member: GroupMember): void {
    const ref = this.dialog.open<ConfirmDialogComponent, { title: string; message: string; danger: boolean }, boolean>(ConfirmDialogComponent, { data: { title: 'Remove Member', message: 'Remove this member from the group?', danger: true }, ariaLabel: 'Confirm removing group member' });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.removeMember(this.group()!.id, member.member_id).subscribe({ next: () => { this.toastService.show('Member removed', { variant: 'success', durationMs: 2000, actionLabel: 'Close' }); this.load(); } });
    });
  }
}
