import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { forkJoin } from 'rxjs';
import { ApiListResponse } from '../../core/models';
import { FamilyService } from '../../core/services/family.service';
import { BatchService } from '../../core/services/batch.service';
import { PaymentService } from '../../core/services/payment.service';
import { PageHeaderComponent } from '../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../shared/components/icon/app-icon.component';
import { ToastService } from '../../shared/components/toast/toast.service';

interface StatCard {
  label: string;
  value: string | number;
  icon: string;
  color: string;
  route: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    AppIconComponent,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Dashboard" subtitle="Welcome to KCDF Admin Portal"></app-page-header>

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <a *ngFor="let stat of stats()" [routerLink]="stat.route" class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-gray-800 dark:bg-gray-900">
          <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl" [style.background]="stat.color + '20'">
            <app-icon [name]="stat.icon" aria-hidden="true" [style.color]="stat.color" class="!h-7 !w-7 !text-[28px]"></app-icon>
          </div>
          <div class="min-w-0">
            <div class="text-2xl font-bold leading-none text-gray-900 dark:text-white">{{ stat.value }}</div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ stat.label }}</div>
          </div>
      </a>
    </div>

    <section>
      <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Quick Navigation</h2>
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
        <a *ngFor="let nav of quickNav" [routerLink]="nav.route" class="flex flex-col items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-5 text-center text-sm font-medium text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200">
            <app-icon [name]="nav.icon" aria-hidden="true" [style.color]="nav.color" class="!h-8 !w-8 !text-[32px]"></app-icon>
            <span>{{ nav.label }}</span>
        </a>
      </div>
    </section>
  `,
})
export class DashboardComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly batchService = inject(BatchService);
  private readonly paymentService = inject(PaymentService);
  private readonly toastService = inject(ToastService);

  readonly loading = signal(true);
  readonly stats = signal<StatCard[]>([
    { label: 'Total Families', value: '—', icon: 'family_restroom', color: '#1a237e', route: '/families' },
    { label: 'Active Batches', value: '—', icon: 'groups', color: '#2e7d32', route: '/batches' },
    { label: 'Pending Payments', value: '—', icon: 'payments', color: '#e65100', route: '/payments' },
    { label: 'Notifications Sent', value: '—', icon: 'notifications', color: '#6a1b9a', route: '/notifications' },
  ]);

  readonly quickNav = [
    { label: 'Members', icon: 'people', color: '#1a237e', route: '/members' },
    { label: 'Families', icon: 'family_restroom', color: '#283593', route: '/families' },
    { label: 'Batches', icon: 'groups', color: '#2e7d32', route: '/batches' },
    { label: 'Enrollments', icon: 'assignment', color: '#1565c0', route: '/enrollments' },
    { label: 'Payments', icon: 'payments', color: '#e65100', route: '/payments' },
    { label: 'Reports', icon: 'bar_chart', color: '#6a1b9a', route: '/reports/attendance' },
  ];

  ngOnInit(): void {
    forkJoin({
      families: this.familyService.list({ per_page: 1 }),
      batches: this.batchService.list({ status: 'active', per_page: 1 }),
      payments: this.paymentService.list({ status: 'pending', per_page: 1 }),
    }).subscribe({
      next: ({ families, batches, payments }) => {
        const totals = {
          families: this.readTotal(families),
          batches: this.readTotal(batches),
          payments: this.readTotal(payments),
        };

        this.stats.set([
          { label: 'Total Families', value: totals.families ?? '—', icon: 'family_restroom', color: '#1a237e', route: '/families' },
          { label: 'Active Batches', value: totals.batches ?? '—', icon: 'groups', color: '#2e7d32', route: '/batches' },
          { label: 'Pending Payments', value: totals.payments ?? '—', icon: 'payments', color: '#e65100', route: '/payments' },
          { label: 'Notifications Sent', value: '—', icon: 'notifications', color: '#6a1b9a', route: '/notifications' },
        ]);

        const missingTotals = Object.entries(totals)
          .filter(([, total]) => total === null)
          .map(([resource]) => resource);

        if (missingTotals.length) {
          console.error(
            `Dashboard API response is missing numeric pagination metadata for: ${missingTotals.join(', ')}.`
          );
          this.toastService.show(
            `Could not load dashboard totals for ${missingTotals.join(', ')}. The API response is missing pagination metadata.`,
            { variant: 'warning', durationMs: 6000, actionLabel: 'Close' }
          );
        }

        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  private readTotal<T>(response: ApiListResponse<T> | null | undefined): number | null {
    if (typeof response?.meta?.total === 'number' && Number.isFinite(response.meta.total)) {
      return response.meta.total;
    }

    return null;
  }
}
