import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TrainerFormComponent } from '../trainer-form/trainer-form.component';

@Component({
  selector: 'app-trainer-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, AppIconComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="trainerName()" subtitle="Trainer Profile">
      <button type="button" routerLink="/trainers" class="inline-flex min-h-10 items-center justify-center gap-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"><app-icon name="arrow_back" class="h-6 w-6"></app-icon> Back</button>
      <button type="button" (click)="openEdit()" class="inline-flex min-h-10 items-center justify-center gap-1 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"><app-icon name="edit" class="h-6 w-6"></app-icon> Edit</button>
    </app-page-header>
    <section *ngIf="trainer()" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-6">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
          <div class="flex min-w-0 flex-col gap-1.5"><span class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Trainer Code</span><span class="text-sm text-gray-900 dark:text-gray-100">{{ trainer()!.trainer_code }}</span></div>
          <div class="flex min-w-0 flex-col gap-1.5"><span class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Name</span><span class="text-sm text-gray-900 dark:text-gray-100">{{ trainer()!.member?.first_name }} {{ trainer()!.member?.last_name }}</span></div>
          <div class="flex min-w-0 flex-col gap-1.5"><span class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Specialization</span><span class="text-sm text-gray-900 dark:text-gray-100">{{ trainer()!.specialization ?? '—' }}</span></div>
          <div class="flex min-w-0 flex-col gap-1.5"><span class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</span><app-status-badge [status]="trainer()!.status"></app-status-badge></div>
          <div class="flex min-w-0 flex-col gap-1.5 sm:col-span-2"><span class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bio</span><span class="text-sm leading-6 text-gray-700 dark:text-gray-300">{{ trainer()!.bio ?? '—' }}</span></div>
        </div>
    </section>
  `
})
export class TrainerDetailComponent implements OnInit {
  private readonly trainerService = inject(TrainerService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly trainer = signal<Trainer | null>(null);
  trainerName = () => { const t = this.trainer(); return t ? `${t.member?.first_name ?? ''} ${t.member?.last_name ?? ''}`.trim() || t.trainer_code : 'Trainer'; };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.trainerService.get(id).subscribe({ next: res => { this.trainer.set(res.data); this.loading.set(false); }, error: () => this.loading.set(false) });
  }

  openEdit(): void {
    const ref = this.dialog.open<TrainerFormComponent, Trainer | null, boolean>(TrainerFormComponent, {
      width: '520px',
      data: this.trainer(),
      ariaLabel: 'Edit trainer',
    });
    ref.afterClosed().subscribe(saved => {
      if (saved) {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.trainerService.get(id).subscribe(res => this.trainer.set(res.data));
      }
    });
  }
}
