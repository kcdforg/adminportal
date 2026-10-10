import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { BatchService } from '../../../core/services/batch.service';
import { SessionService } from '../../../core/services/session.service';
import { Batch, Session, MemberProfile } from '../../../core/models';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { SessionFormComponent } from '../../sessions/session-form/session-form.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';

@Component({
  selector: 'app-batch-detail',
  standalone: true,
  imports: [
    CommonModule, RouterModule,
    AppIconComponent, TooltipDirective,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  templateUrl: './batch-detail.component.html',
  styleUrl: './batch-detail.component.scss'
})
export class BatchDetailComponent implements OnInit {
  private readonly batchService = inject(BatchService);
  private readonly sessionService = inject(SessionService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);

  readonly loading = signal(false);
  readonly activeTab = signal<'info' | 'members' | 'sessions'>('info');
  readonly batch = signal<Batch | null>(null);
  readonly members = signal<MemberProfile[]>([]);
  readonly sessions = signal<Session[]>([]);
  ngOnInit(): void { this.load(); }

  onTabKeydown(event: KeyboardEvent): void {
    if (!(event.currentTarget instanceof HTMLElement)) return;
    const tabs = Array.from(event.currentTarget.querySelectorAll<HTMLButtonElement>('[role="tab"]'));
    const currentIndex = tabs.indexOf(event.target as HTMLButtonElement);
    if (currentIndex < 0) return;

    let nextIndex: number | undefined;
    if (event.key === 'ArrowRight') nextIndex = (currentIndex + 1) % tabs.length;
    if (event.key === 'ArrowLeft') nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
    if (event.key === 'Home') nextIndex = 0;
    if (event.key === 'End') nextIndex = tabs.length - 1;
    if (nextIndex === undefined) return;

    event.preventDefault();
    tabs[nextIndex].click();
    tabs[nextIndex].focus();
  }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({
      batch: this.batchService.get(id),
      members: this.batchService.getMembers(id),
      sessions: this.sessionService.getByBatch(id),
    }).subscribe({
      next: ({ batch, members, sessions }) => {
        this.batch.set(batch.data);
        this.members.set(members.data);
        this.sessions.set(sessions.data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  addSession(): void {
    const ref = this.dialog.open<SessionFormComponent, { batch_id: number }, boolean>(SessionFormComponent, {
      width: '560px',
      data: { batch_id: this.batch()!.id },
      ariaLabel: 'Add session to batch',
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
