import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { NotificationService } from '../../../core/services/notification.service';
import { Notification } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-notification-list',
  standalone: true,
  imports: [CommonModule, RouterModule, AppIconComponent, TailwindPaginatorComponent, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Notifications" [subtitle]="'Total: ' + total()">
      <button type="button" routerLink="/notifications/send" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><app-icon name="send" aria-hidden="true" class="h-6 w-6"></app-icon> Send Notification</button>
    </app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Member</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Channel</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Sent</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <tr *ngFor="let n of notifications()" class="text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800/50">
              <td class="whitespace-nowrap px-4 py-3">{{ n.member?.first_name }} {{ n.member?.last_name }}</td><td class="px-4 py-3">{{ n.title }}</td><td class="whitespace-nowrap px-4 py-3">{{ n.channel }}</td><td class="whitespace-nowrap px-4 py-3"><app-status-badge [status]="n.status"></app-status-badge></td><td class="whitespace-nowrap px-4 py-3">{{ n.sent_at | date:'dd MMM yyyy HH:mm' }}</td>
            </tr>
            <tr *ngIf="!notifications().length"><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No notifications sent</td></tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize()" [pageSizeOptions]="[10,20,50]" selectId="notification-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
})
export class NotificationListComponent implements OnInit {
  private readonly notificationService = inject(NotificationService);
  readonly loading = signal(false);
  readonly notifications = signal<Notification[]>([]);
  readonly total = signal(0);
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading.set(true);
    this.notificationService.list({ page: this.page, per_page: this.pageSize() }).subscribe({
      next: res => { this.notifications.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }
}
