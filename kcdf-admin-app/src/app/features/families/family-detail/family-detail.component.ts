import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { finalize } from 'rxjs';
import { FamilyService } from '../../../core/services/family.service';
import { Family, FamilyMember, MemberLogin } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { FamilyFormComponent } from '../family-form/family-form.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { FamilyMemberFormComponent } from './family-member-form.component';
import { MemberLoginFormComponent } from './member-login-form.component';
import { MemberService } from '../../../core/services/member.service';
import { authStore } from '../../../core/store/auth.store';

@Component({
  selector: 'app-family-detail',
  standalone: true,
  imports: [
    CommonModule, RouterModule,
    AppIconComponent, TooltipDirective,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  templateUrl: './family-detail.component.html',
  styleUrl: './family-detail.component.scss',
})
export class FamilyDetailComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toast = inject(ToastService);
  private readonly memberService = inject(MemberService);

  readonly loading = signal(false);
  readonly family = signal<Family | null>(null);
  readonly members = signal<FamilyMember[]>([]);
  readonly familyError = signal<string | null>(null);
  readonly membersError = signal<string | null>(null);
  readonly membersLoading = signal(false);
  readonly canManageFamilyMembers = authStore.canAccess(['super_admin', 'program_manager']);

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!Number.isInteger(id) || id < 1) {
      this.family.set(null);
      this.familyError.set('The family ID in this URL is invalid.');
      return;
    }
    this.loading.set(true);
    this.familyError.set(null);
    this.members.set([]);
    this.familyService.get(id).pipe(
      finalize(() => this.loading.set(false)),
    ).subscribe({
      next: response => this.family.set(response.data),
      error: error => {
        this.family.set(null);
        this.familyError.set(this.getErrorMessage(error, 'Unable to load family details.'));
      },
    });
    this.loadMembers(id);
  }

  retryMembers(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (Number.isInteger(id) && id > 0) this.loadMembers(id);
  }

  private loadMembers(familyId: number): void {
    this.membersLoading.set(true);
    this.membersError.set(null);
    this.members.set([]);
    this.familyService.getMembers(familyId).pipe(
      finalize(() => this.membersLoading.set(false)),
    ).subscribe({
      next: response => {
        if (!Array.isArray(response.data)) {
          this.membersError.set('The server returned an invalid family members response.');
          return;
        }
        this.members.set(response.data);
      },
      error: error => this.membersError.set(this.getErrorMessage(error, 'Unable to load family members.')),
    });
  }

  private getErrorMessage(error: unknown, fallback: string): string {
    if (typeof error !== 'object' || error === null || !('error' in error)) return fallback;
    const payload = error.error;
    if (typeof payload !== 'object' || payload === null || !('error' in payload)) return fallback;
    const details = payload.error;
    if (typeof details !== 'object' || details === null || !('message' in details)) return fallback;
    return typeof details.message === 'string' && details.message.trim() !== ''
      ? details.message
      : fallback;
  }

  openEdit(): void {
    const family = this.family();
    const ref = this.dialog.open(FamilyFormComponent, {
      width: '560px',
      ariaLabel: family?.family_name ? `Edit family ${family.family_name}` : 'Edit family',
      data: family,
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  hasRelationship(type: FamilyMember['relationship_type']): boolean {
    return this.members().some(member => member.relationship_type === type);
  }

  getMemberByRelationship(type: 'father' | 'mother'): FamilyMember | null {
    return this.members().find(member => member.relationship_type === type) ?? null;
  }

  getMembersByRelationship(type: FamilyMember['relationship_type']): FamilyMember[] {
    return this.members().filter(member => member.relationship_type === type);
  }

  relationshipLabel(type: FamilyMember['relationship_type']): string {
    return ({ father: 'Husband / Father', mother: 'Wife / Mother', guardian: 'Guardian', child: 'Child' })[type];
  }

  isLoginActive(login: MemberLogin | null): boolean {
    return login !== null && Number(login.is_active) === 1;
  }

  addMember(
    relationshipType: FamilyMember['relationship_type'],
    title: string,
    gender: 'male' | 'female',
  ): void {
    const family = this.family();
    if (!family) return;
    const ref = this.dialog.open(FamilyMemberFormComponent, {
      width: '720px',
      ariaLabel: title,
      data: { familyId: family.id, relationshipType, title, gender },
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  editMember(member: FamilyMember): void {
    const family = this.family();
    if (!family) return;
    const ref = this.dialog.open(FamilyMemberFormComponent, {
      width: '720px',
      ariaLabel: `Edit ${member.profile.first_name} ${member.profile.last_name}`,
      data: { familyId: family.id, relationshipType: member.relationship_type, member },
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  manageLogin(member: FamilyMember): void {
    const ref = this.dialog.open(MemberLoginFormComponent, {
      width: '520px',
      ariaLabel: member.login ? 'Edit member login' : 'Create member login',
      data: { profile: member.profile, login: member.login, familyCode: this.family()?.family_code ?? '' },
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  removeLogin(member: FamilyMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, {
      ariaLabel: 'Confirm member login deactivation',
      data: {
        title: 'Deactivate Login',
        message: `Deactivate the login for ${member.profile.first_name} ${member.profile.last_name}? Their active refresh sessions will be revoked.`,
        danger: true,
        confirmLabel: 'Deactivate Login',
      },
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.memberService.deleteLogin(member.profile_id).subscribe({
        next: () => {
          this.toast.show('Member login deactivated', {
            variant: 'success',
            durationMs: 2000,
            actionLabel: 'Close',
          });
          this.load();
        },
        error: (error) => this.toast.show(error?.error?.error?.message ?? 'Failed to deactivate member login', {
          variant: 'error',
          durationMs: 3000,
          actionLabel: 'Close',
        }),
      });
    });
  }

  removeMember(member: FamilyMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, {
      ariaLabel: 'Confirm family member removal',
      data: { title: 'Remove Member', message: 'Remove this member from the family?', danger: true }
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.familyService.removeMember(this.family()!.id, member.profile_id).subscribe({
        next: () => {
          this.toast.show('Member removed', {
            variant: 'success',
            durationMs: 2000,
            actionLabel: 'Close',
          });
          this.load();
        },
        error: (error) => this.toast.show(error?.error?.error?.message ?? 'Failed to remove member', {
          variant: 'error',
          durationMs: 3000,
          actionLabel: 'Close',
        })
      });
    });
  }
}
