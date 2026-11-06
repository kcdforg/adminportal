import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { GroupService } from '../../../core/services/group.service';
import {
  Group,
  GroupCategory,
  GroupContent,
  GroupMember,
  GroupMemberCandidate,
  GroupStatus,
  GroupVisibility,
} from '../../../core/models';
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
  imports: [CommonModule, FormsModule, RouterModule, AppIconComponent, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  templateUrl: './group-detail.component.html',
  styleUrl: './group-detail.component.scss',
})
export class GroupDetailComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toastService = inject(ToastService);
  readonly loading = signal(false);
  readonly loadError = signal('');
  readonly group = signal<Group | null>(null);
  readonly categories = signal<GroupCategory[]>([]);
  readonly members = signal<GroupMember[]>([]);
  readonly memberCandidates = signal<GroupMemberCandidate[]>([]);
  readonly memberError = signal('');
  readonly memberSuccess = signal('');
  newMemberId = 0;
  newMemberRole: GroupMember['member_role'] = 'member';
  memberCandidateSearch = '';
  readonly content = signal<GroupContent[]>([]);
  readonly contentType = signal<'posts' | 'polls' | 'events'>('posts');
  readonly contentError = signal('');
  readonly contentSuccess = signal('');
  readonly editingContent = signal<GroupContent | null>(null);
  readonly settingsError = signal('');
  readonly settingsSuccess = signal('');
  readonly activeTab = signal<'members' | 'posts' | 'events' | 'polls'>('members');
  readonly activeDialog = signal<'settings' | 'member' | 'content' | null>(null);
  readonly saving = signal(false);
  readonly settingsForm = {
    group_name: '',
    display_name: '',
    description: '',
    visibility: 'public' as GroupVisibility,
    status: 'active' as GroupStatus,
    category_id: null as number | null,
  };
  readonly contentForm = {
    title: '',
    content: '',
    status: 'published',
    options: '',
    allow_multiple: false,
    allow_vote_change: false,
    show_results_before_voting: false,
    closes_at: '',
    start_at: '',
    end_at: '',
    location: '',
    capacity: null as number | null,
  };
  readonly cols = ['name', 'joined', 'banned', 'actions'];

  ngOnInit(): void { this.load(); }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.loadError.set('');
    forkJoin({
      group: this.groupService.get(id),
      members: this.groupService.getMembers(id, true),
      candidates: this.groupService.getMemberCandidates(this.memberCandidateSearch.trim() || undefined),
      categories: this.groupService.getCategories(),
      content: this.groupService.listContent(id, this.contentType()),
    }).subscribe({
      next: ({ group, members, candidates, categories, content }) => {
        this.group.set(group.data);
        this.categories.set(categories.data.filter((category) => category.status === 'active'));
        this.settingsForm.group_name = group.data.group_name;
        this.settingsForm.display_name = group.data.display_name ?? '';
        this.settingsForm.description = group.data.description ?? '';
        this.settingsForm.visibility = group.data.visibility;
        this.settingsForm.status = group.data.status;
        this.settingsForm.category_id = group.data.category_id ?? null;
        this.members.set(members.data);
        this.memberCandidates.set(candidates.data);
        this.content.set(content.data);
        this.loading.set(false);
      },
      error: (error) => {
        this.loadError.set(this.getApiError(error, 'Group details could not be loaded.'));
        this.loading.set(false);
      }
    });
  }

  saveSettings(): void {
    const group = this.group();
    if (!group) return;
    this.settingsError.set('');
    this.settingsSuccess.set('');
    this.groupService.update(group.id, {
      group_name: this.settingsForm.group_name,
      display_name: this.settingsForm.display_name || null,
      description: this.settingsForm.description || null,
      visibility: this.settingsForm.visibility,
      status: this.settingsForm.status,
      category_id: this.settingsForm.category_id,
    }).subscribe({
      next: (response) => {
        this.group.set(response.data);
        this.settingsSuccess.set('Group settings updated.');
        this.activeDialog.set(null);
        this.toastService.show('Group details updated.', { variant: 'success', durationMs: 2500, actionLabel: 'Close' });
      },
      error: (error) => this.settingsError.set(this.getApiError(error, 'Group settings could not be saved.')),
    });
  }

  openSettingsDialog(): void {
    const group = this.group();
    if (!group) return;
    this.settingsForm.group_name = group.group_name;
    this.settingsForm.display_name = group.display_name ?? '';
    this.settingsForm.description = group.description ?? '';
    this.settingsForm.visibility = group.visibility;
    this.settingsForm.status = group.status;
    this.settingsForm.category_id = group.category_id ?? null;
    this.settingsError.set('');
    this.activeDialog.set('settings');
  }

  openMemberDialog(): void {
    this.memberError.set('');
    this.memberSuccess.set('');
    this.newMemberId = 0;
    this.newMemberRole = 'member';
    this.memberCandidateSearch = '';
    this.activeDialog.set('member');
    this.searchMemberCandidates();
  }

  openContentDialog(type: 'posts' | 'polls' | 'events'): void {
    this.selectContentType(type);
    this.editingContent.set(null);
    this.resetContentForm();
    this.contentError.set('');
    this.contentSuccess.set('');
    this.activeDialog.set('content');
  }

  closeDialog(): void {
    this.activeDialog.set(null);
    this.editingContent.set(null);
  }

  selectTab(tab: 'members' | 'posts' | 'events' | 'polls'): void {
    this.activeTab.set(tab);
    if (tab !== 'members') {
      this.selectContentType(tab);
    }
  }

  selectContentType(type: 'posts' | 'polls' | 'events'): void {
    this.cancelContentEdit();
    this.contentType.set(type);
    this.loadContent();
  }

  loadContent(): void {
    const groupId = this.group()?.id;
    if (!groupId) return;
    this.groupService.listContent(groupId, this.contentType()).subscribe({
      next: (response) => this.content.set(response.data),
      error: () => this.contentError.set('Group content could not be loaded.'),
    });
  }

  createContent(): void {
    const groupId = this.group()?.id;
    if (!groupId) return;
    this.contentError.set('');
    this.contentSuccess.set('');
    const payload: Record<string, unknown> = {
      title: this.contentForm.title,
      content: this.contentForm.content,
      status: this.contentForm.status,
    };
    if (this.contentType() === 'polls') {
      payload['options'] = this.contentForm.options.split('\n').map((option) => option.trim()).filter(Boolean);
      payload['allow_multiple'] = this.contentForm.allow_multiple;
      payload['allow_vote_change'] = this.contentForm.allow_vote_change;
      payload['show_results_before_voting'] = this.contentForm.show_results_before_voting;
      payload['closes_at'] = this.contentForm.closes_at || null;
    } else if (this.contentType() === 'events') {
      payload['start_at'] = this.contentForm.start_at;
      payload['end_at'] = this.contentForm.end_at;
      payload['location'] = this.contentForm.location;
      payload['capacity'] = this.contentForm.capacity;
    }
    const editing = this.editingContent();
    const request = editing
      ? this.groupService.updateContent(groupId, this.contentType(), editing.id, payload)
      : this.groupService.createContent(groupId, this.contentType(), payload);
    request.subscribe({
      next: () => {
        this.cancelContentEdit();
        this.contentSuccess.set(editing ? 'Group content updated.' : 'Group content created.');
        this.activeDialog.set(null);
        this.toastService.show(editing ? 'Group content updated.' : 'Group content created.', { variant: 'success', durationMs: 2500, actionLabel: 'Close' });
        this.loadContent();
      },
      error: (error) => this.contentError.set(this.getApiError(error, editing
        ? 'Content could not be updated. Check required fields and try again.'
        : 'Content could not be created. Check required fields and try again.')),
    });
  }

  archiveContent(item: GroupContent): void {
    const groupId = this.group()?.id;
    if (!groupId) return;
    this.groupService.archiveContent(groupId, this.contentType(), item.id).subscribe({
      next: () => this.loadContent(),
      error: (error) => this.contentError.set(this.getApiError(error, 'Content could not be archived.')),
    });
  }

  editContent(item: GroupContent): void {
    this.editingContent.set(item);
    this.contentError.set('');
    this.contentForm.title = item.title;
    this.contentForm.content = item.content ?? '';
    this.contentForm.status = item.status === 'archived' ? 'draft' : item.status;
    this.contentForm.options = item.content_data.options?.join('\n') ?? '';
    this.contentForm.allow_multiple = item.content_data.allow_multiple ?? false;
    this.contentForm.allow_vote_change = item.content_data.allow_vote_change ?? false;
    this.contentForm.show_results_before_voting = item.content_data.show_results_before_voting ?? false;
    this.contentForm.closes_at = this.toDateTimeInput(item.content_data.closes_at);
    this.contentForm.start_at = this.toDateTimeInput(item.content_data.start_at);
    this.contentForm.end_at = this.toDateTimeInput(item.content_data.end_at);
    this.contentForm.location = item.content_data.location ?? '';
    this.contentForm.capacity = item.content_data.capacity ?? null;
    this.activeDialog.set('content');
  }

  cancelContentEdit(): void {
    this.editingContent.set(null);
    this.resetContentForm();
  }

  private resetContentForm(): void {
    this.contentForm.title = '';
    this.contentForm.content = '';
    this.contentForm.options = '';
    this.contentForm.status = 'published';
    this.contentForm.allow_multiple = false;
    this.contentForm.allow_vote_change = false;
    this.contentForm.show_results_before_voting = false;
    this.contentForm.closes_at = '';
    this.contentForm.start_at = '';
    this.contentForm.end_at = '';
    this.contentForm.location = '';
    this.contentForm.capacity = null;
  }

  private toDateTimeInput(value: string | null | undefined): string {
    if (!value) return '';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    const local = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
    return local.toISOString().slice(0, 16);
  }

  availableMemberCandidates(): GroupMemberCandidate[] {
    const membershipById = new Map(this.members().map((member) => [member.member_id, member.status]));
    return this.memberCandidates().filter((candidate) => {
      const status = membershipById.get(candidate.id);
      return status === undefined || status === 'left';
    });
  }

  searchMemberCandidates(): void {
    this.memberError.set('');
    this.newMemberId = 0;
    this.groupService.getMemberCandidates(this.memberCandidateSearch.trim() || undefined).subscribe({
      next: (response) => this.memberCandidates.set(response.data),
      error: (error) => this.memberError.set(this.getApiError(error, 'Eligible member accounts could not be loaded.')),
    });
  }

  activeMemberCount(): number {
    return this.members().filter((member) => member.status === 'active').length;
  }

  addMember(): void {
    const groupId = this.group()?.id;
    if (!groupId || this.newMemberId < 1) {
      this.memberError.set('Choose an eligible member account.');
      return;
    }
    this.memberError.set('');
    this.memberSuccess.set('');
    this.groupService.addMember(groupId, this.newMemberId, this.newMemberRole).subscribe({
      next: () => {
        this.newMemberId = 0;
        this.newMemberRole = 'member';
        this.memberSuccess.set('Member added to the group.');
        this.activeDialog.set(null);
        this.load();
      },
      error: (error) => this.memberError.set(this.getApiError(error, 'The member could not be added.')),
    });
  }

  changeMemberRole(member: GroupMember, event: Event): void {
    const role = (event.target as HTMLSelectElement).value as GroupMember['member_role'];
    const groupId = this.group()?.id;
    if (!groupId || member.member_role === role) return;
    this.groupService.updateMemberRole(groupId, member.member_id, role).subscribe({
      next: () => {
        this.memberSuccess.set('Member role updated.');
        this.load();
      },
      error: (error) => {
        this.memberError.set(this.getApiError(error, 'Member role could not be updated.'));
        this.load();
      },
    });
  }

  private getApiError(error: unknown, fallback: string): string {
    if (typeof error === 'object' && error !== null && 'error' in error) {
      const response = (error as { error?: { error?: { message?: string } } }).error;
      if (response?.error?.message) return response.error.message;
    }
    return fallback;
  }

  banMember(member: GroupMember): void {
    const ref = this.dialog.open<ConfirmDialogComponent, { title: string; message: string; danger: boolean }, boolean>(ConfirmDialogComponent, { data: { title: 'Ban Member', message: 'Ban this member from the group?', danger: true }, ariaLabel: 'Confirm banning group member' });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.banMember(this.group()!.id, member.member_id).subscribe({
        next: () => { this.toastService.show('Member banned', { variant: 'success', durationMs: 2000, actionLabel: 'Close' }); this.load(); },
        error: (error) => this.toastService.show(this.getApiError(error, 'Member could not be banned.'), { variant: 'error', durationMs: 4000, actionLabel: 'Close' }),
      });
    });
  }

  removeMember(member: GroupMember): void {
    const ref = this.dialog.open<ConfirmDialogComponent, { title: string; message: string; danger: boolean }, boolean>(ConfirmDialogComponent, { data: { title: 'Remove Member', message: 'Remove this member from the group?', danger: true }, ariaLabel: 'Confirm removing group member' });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.removeMember(this.group()!.id, member.member_id).subscribe({
        next: () => { this.toastService.show('Member removed', { variant: 'success', durationMs: 2000, actionLabel: 'Close' }); this.load(); },
        error: (error) => this.toastService.show(this.getApiError(error, 'Member could not be removed.'), { variant: 'error', durationMs: 4000, actionLabel: 'Close' }),
      });
    });
  }
}
