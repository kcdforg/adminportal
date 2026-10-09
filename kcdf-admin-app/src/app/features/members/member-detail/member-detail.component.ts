import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { MemberFormComponent } from '../member-form/member-form.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-member-detail',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    AppIconComponent,
    StatusBadgeComponent,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="memberName()" subtitle="Member Profile">
      <button type="button" routerLink="/members"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
        <app-icon name="arrow_back" aria-hidden="true" class="h-6 w-6"></app-icon> Back
      </button>
      <button type="button" (click)="openEdit()"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <app-icon name="edit" aria-hidden="true" class="h-6 w-6"></app-icon> Edit
      </button>
    </app-page-header>

    <div *ngIf="member()" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Personal Information</h2>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">First Name</span><span class="text-sm text-gray-900 dark:text-white">{{ member()!.first_name }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Last Name</span><span class="text-sm text-gray-900 dark:text-white">{{ member()!.last_name }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</span><span class="text-sm text-gray-900 dark:text-white">{{ member()!.email ?? '—' }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Mobile</span><span class="text-sm text-gray-900 dark:text-white">{{ member()!.mobile ?? '—' }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Gender</span><span class="text-sm text-gray-900 dark:text-white">{{ member()!.gender ?? '—' }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date of Birth</span><span class="text-sm text-gray-900 dark:text-white">{{ (member()!.date_of_birth | date:'dd MMM yyyy') ?? '—' }}</span></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</span><app-status-badge [status]="member()!.status"></app-status-badge></div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Has Login</span>
              <app-icon [name]="member()!.has_login ? 'check_circle' : 'cancel'" class="!text-xl h-5 w-5"
                [style.color]="member()!.has_login ? '#2e7d32' : '#c62828'"></app-icon>
          </div>
          <div class="flex flex-col gap-1"><span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Member Since</span><span class="text-sm text-gray-900 dark:text-white">{{ member()!.created_at | date:'dd MMM yyyy' }}</span></div>
        </div>
      </div>
    </div>
  `,
})
export class MemberDetailComponent implements OnInit {
  private readonly memberService = inject(MemberService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);

  readonly loading = signal(false);
  readonly member = signal<MemberProfile | null>(null);

  memberName = () => {
    const m = this.member();
    return m ? `${m.first_name} ${m.last_name}` : 'Loading...';
  };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.memberService.get(id).subscribe({
      next: (res) => { this.member.set(res.data); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  openEdit(): void {
    const ref = this.dialog.open(MemberFormComponent, {
      width: '520px',
      ariaLabel: `Edit member ${this.memberName()}`,
      data: this.member(),
    });
    ref.afterClosed().subscribe(saved => {
      if (saved) {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.memberService.get(id).subscribe(res => this.member.set(res.data));
      }
    });
  }
}
