import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { FamilyService } from '../../../core/services/family.service';
import { Family, FamilyMember } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { FamilyFormComponent } from '../family-form/family-form.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';

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

  readonly loading = signal(false);
  readonly family = signal<Family | null>(null);
  readonly members = signal<FamilyMember[]>([]);

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({
      family: this.familyService.get(id),
      members: this.familyService.getMembers(id),
    }).subscribe({
      next: ({ family, members }) => {
        this.family.set(family.data);
        this.members.set(members.data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
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

  removeMember(member: FamilyMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, {
      ariaLabel: 'Confirm family member removal',
      data: { title: 'Remove Member', message: 'Remove this member from the family?', danger: true }
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.familyService.removeMember(this.family()!.id, member.member_id).subscribe({
        next: () => {
          this.toast.show('Member removed', {
            variant: 'success',
            durationMs: 2000,
            actionLabel: 'Close',
          });
          this.load();
        },
        error: () => this.toast.show('Failed to remove member', {
          variant: 'error',
          durationMs: 3000,
          actionLabel: 'Close',
        })
      });
    });
  }
}
