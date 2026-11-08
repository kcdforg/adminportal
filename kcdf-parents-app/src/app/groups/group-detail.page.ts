import { Component, inject, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { forkJoin } from 'rxjs';
import {
  IonContent,
  IonIcon,
  IonLabel,
  IonModal,
  IonSegment,
  IonSegmentButton,
} from '@ionic/angular/standalone';
import {
  GroupContent,
  GroupMember,
  GroupMemberCandidate,
  MemberApiService,
  ParentGroup,
  PollResults,
} from '../core/member-auth.service';

@Component({
  selector: 'app-group-detail',
  templateUrl: './group-detail.page.html',
  styleUrl: './group-detail.page.scss',
  imports: [FormsModule, RouterLink, IonContent, IonIcon, IonLabel, IonModal, IonSegment, IonSegmentButton],
})
export class GroupDetailPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly api = inject(MemberApiService);
  readonly groupId = Number(this.route.snapshot.paramMap.get('id'));
  group: ParentGroup | null = null;
  members: GroupMember[] = [];
  announcements: GroupContent[] = [];
  polls: GroupContent[] = [];
  events: GroupContent[] = [];
  memberCandidates: GroupMemberCandidate[] = [];
  pollResults: Record<number, PollResults> = {};
  selectedPollOptions: Record<number, number[]> = {};
  activeSection = 'posts';
  activeModal: 'member' | 'posts' | 'events' | 'polls' | null = null;
  memberCandidateSearch = '';
  selectedCandidateId = 0;
  contentError = '';
  memberError = '';
  saving = false;
  contentForm = {
    title: '',
    content: '',
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
  loading = true;
  error = '';

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.error = '';
    this.api.getGroup(this.groupId).subscribe({
      next: (group) => {
        this.group = group;
        forkJoin({
          announcements: this.api.getGroupContent(this.groupId, 'posts'),
          polls: this.api.getGroupContent(this.groupId, 'polls'),
          events: this.api.getGroupContent(this.groupId, 'events'),
        }).subscribe({
          next: (content) => {
            this.announcements = content.announcements;
            this.polls = content.polls;
            this.events = content.events;
            if (group.my_role) {
              this.api.getGroupMembers(this.groupId).subscribe({
                next: (members) => {
                  this.members = members;
                  this.loading = false;
                },
                error: () => {
                  this.error = 'Group members could not be loaded.';
                  this.loading = false;
                },
              });
            } else {
              this.loading = false;
            }
          },
          error: () => {
            this.error = 'Group content could not be loaded. Please try again.';
            this.loading = false;
          },
        });
      },
      error: () => {
        this.error = 'This group could not be loaded.';
        this.loading = false;
      },
    });
  }

  setSection(section: string | number | undefined): void {
    if (typeof section === 'string') {
      this.activeSection = section;
    }
  }

  isGroupManager(): boolean {
    return ['owner', 'admin', 'moderator'].includes(this.group?.my_role ?? '');
  }

  canManageMembers(): boolean {
    return ['owner', 'admin'].includes(this.group?.my_role ?? '');
  }

  openMemberModal(): void {
    this.activeModal = 'member';
    this.memberError = '';
    this.memberCandidateSearch = '';
    this.selectedCandidateId = 0;
    this.loadMemberCandidates();
  }

  loadMemberCandidates(): void {
    this.memberError = '';
    this.api.getGroupMemberCandidates(this.groupId, this.memberCandidateSearch).subscribe({
      next: (candidates) => {
        const existingIds = new Set(this.members.map((member) => member.member_id));
        this.memberCandidates = candidates.filter((candidate) => !existingIds.has(candidate.id));
      },
      error: () => this.memberError = 'Eligible members could not be loaded.',
    });
  }

  addMember(): void {
    if (!this.selectedCandidateId) {
      this.memberError = 'Choose a member to add.';
      return;
    }
    this.saving = true;
    this.api.addGroupMember(this.groupId, this.selectedCandidateId).subscribe({
      next: () => {
        this.saving = false;
        this.closeModal();
        this.api.getGroupMembers(this.groupId).subscribe({
          next: (members) => this.members = members,
          error: () => this.error = 'The member was added, but the member list could not be refreshed.',
        });
      },
      error: () => {
        this.saving = false;
        this.memberError = 'The member could not be added.';
      },
    });
  }

  changeMemberRole(member: GroupMember, event: Event): void {
    const memberRole = (event.target as HTMLSelectElement).value as GroupMember['member_role'];
    if (memberRole === member.member_role) return;
    this.api.updateGroupMemberRole(this.groupId, member.member_id, memberRole).subscribe({
      next: () => this.api.getGroupMembers(this.groupId).subscribe({
        next: (members) => this.members = members,
        error: () => this.error = 'The role was updated, but the member list could not be refreshed.',
      }),
      error: () => this.error = 'The member role could not be updated.',
    });
  }

  removeMember(member: GroupMember): void {
    if (!window.confirm(`Remove ${member.member_name} from this group?`)) return;
    this.api.removeGroupMember(this.groupId, member.member_id).subscribe({
      next: () => this.api.getGroupMembers(this.groupId).subscribe({
        next: (members) => this.members = members,
        error: () => this.error = 'The member was removed, but the member list could not be refreshed.',
      }),
      error: () => this.error = 'The member could not be removed.',
    });
  }

  openContentModal(resource: 'posts' | 'events' | 'polls'): void {
    this.activeModal = resource;
    this.contentError = '';
    this.contentForm = {
      title: '',
      content: '',
      options: '',
      allow_multiple: false,
      allow_vote_change: false,
      show_results_before_voting: false,
      closes_at: '',
      start_at: '',
      end_at: '',
      location: '',
      capacity: null,
    };
  }

  saveContent(): void {
    if (!this.activeModal || this.activeModal === 'member') return;
    const resource = this.activeModal;
    const payload: Record<string, unknown> = {
      title: this.contentForm.title.trim(),
      content: this.contentForm.content.trim() || null,
      status: 'published',
    };
    if (resource === 'polls') {
      const options = this.contentForm.options.split('\n').map((option) => option.trim()).filter(Boolean);
      if (options.length < 2 || options.length > 10) {
        this.contentError = 'Enter between 2 and 10 poll options.';
        return;
      }
      Object.assign(payload, {
        options,
        allow_multiple: this.contentForm.allow_multiple,
        allow_vote_change: this.contentForm.allow_vote_change,
        show_results_before_voting: this.contentForm.show_results_before_voting,
        closes_at: this.contentForm.closes_at || null,
      });
    } else if (resource === 'events') {
      Object.assign(payload, {
        start_at: this.contentForm.start_at,
        end_at: this.contentForm.end_at,
        location: this.contentForm.location.trim() || null,
        capacity: this.contentForm.capacity,
      });
    }
    this.contentError = '';
    this.saving = true;
    this.api.createGroupContent(this.groupId, resource, payload).subscribe({
      next: () => {
        this.saving = false;
        this.closeModal();
        this.refreshContent(resource);
      },
      error: () => {
        this.saving = false;
        this.contentError = 'The content could not be created. Check the required fields and try again.';
      },
    });
  }

  closeModal(): void {
    this.activeModal = null;
  }

  modalTitle(): string {
    if (this.activeModal === 'member') return 'Add group member';
    if (this.activeModal === 'posts') return 'Create post';
    if (this.activeModal === 'events') return 'Create event';
    if (this.activeModal === 'polls') return 'Create poll';
    return '';
  }

  private refreshContent(resource: 'posts' | 'events' | 'polls'): void {
    this.api.getGroupContent(this.groupId, resource).subscribe({
      next: (content) => {
        if (resource === 'posts') this.announcements = content;
        if (resource === 'events') this.events = content;
        if (resource === 'polls') this.polls = content;
      },
      error: () => this.error = 'The content was created, but the list could not be refreshed.',
    });
  }

  joinGroup(): void {
    this.api.joinGroup(this.groupId).subscribe({
      next: () => this.load(),
      error: () => {
        this.error = 'You could not join this group. Please try again.';
      },
    });
  }

  leaveGroup(): void {
    if (!window.confirm('Leave this group? You may lose access to its members-only content.')) return;
    this.api.leaveGroup(this.groupId).subscribe({
      next: () => void this.router.navigateByUrl('/groups'),
      error: () => {
        this.error = 'You could not leave this group. Please try again.';
      },
    });
  }

  togglePollOption(poll: GroupContent, optionIndex: number, event: Event): void {
    const checked = (event.target as HTMLInputElement).checked;
    const current = this.selectedPollOptions[poll.id] ?? [];
    const next = poll.content_data.allow_multiple
      ? checked ? [...current, optionIndex] : current.filter((index) => index !== optionIndex)
      : checked ? [optionIndex] : [];
    this.selectedPollOptions = { ...this.selectedPollOptions, [poll.id]: next };
  }

  vote(poll: GroupContent): void {
    const choices = this.selectedPollOptions[poll.id] ?? [];
    this.api.voteOnPoll(this.groupId, poll.id, choices).subscribe({
      next: () => {
        this.polls = this.polls.map((item) => item.id === poll.id ? { ...item, has_voted: true } : item);
        this.showResults(poll);
      },
      error: () => {
        this.error = 'Your vote could not be submitted.';
      },
    });
  }

  showResults(poll: GroupContent): void {
    this.api.getPollResults(this.groupId, poll.id).subscribe({
      next: (results) => {
        this.pollResults = { ...this.pollResults, [poll.id]: results };
      },
      error: () => {
        this.error = 'Poll results are not available yet.';
      },
    });
  }

  isPollClosed(poll: GroupContent): boolean {
    const closesAt = poll.content_data.closes_at;
    return closesAt !== null && closesAt !== undefined && Date.parse(closesAt) <= Date.now();
  }

  register(event: GroupContent): void {
    this.api.registerForEvent(this.groupId, event.id).subscribe({
      next: () => {
        this.events = this.events.map((item) => item.id === event.id
          ? { ...item, registered_count: (item.registered_count ?? 0) + 1, my_registration: { id: 1 } }
          : item);
      },
      error: () => {
        this.error = 'You could not register for this event.';
      },
    });
  }

  cancelRegistration(event: GroupContent): void {
    this.api.cancelEventRegistration(this.groupId, event.id).subscribe({
      next: () => {
        this.events = this.events.map((item) => item.id === event.id
          ? { ...item, registered_count: Math.max(0, (item.registered_count ?? 1) - 1), my_registration: null }
          : item);
      },
      error: () => {
        this.error = 'Your event registration could not be cancelled.';
      },
    });
  }
}
