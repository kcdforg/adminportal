import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { forkJoin } from 'rxjs';
import { FamilyService } from '../../core/services/family.service';
import { BatchService } from '../../core/services/batch.service';
import { PaymentService } from '../../core/services/payment.service';
import { NotificationService } from '../../core/services/notification.service';
import { PageHeaderComponent } from '../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../shared/components/loading-overlay/loading-overlay.component';

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
    MatCardModule,
    MatIconModule,
    MatButtonModule,
    MatProgressSpinnerModule,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Dashboard" subtitle="Welcome to KCDF Admin Portal"></app-page-header>

    <div class="stats-grid">
      <mat-card class="stat-card" *ngFor="let stat of stats()" [routerLink]="stat.route">
        <mat-card-content>
          <div class="stat-icon" [style.background]="stat.color + '20'">
            <mat-icon [style.color]="stat.color">{{ stat.icon }}</mat-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
          </div>
        </mat-card-content>
      </mat-card>
    </div>

    <div class="quick-nav">
      <h2 class="section-title">Quick Navigation</h2>
      <div class="quick-nav-grid">
        <mat-card class="quick-card" *ngFor="let nav of quickNav" [routerLink]="nav.route">
          <mat-card-content>
            <mat-icon [style.color]="nav.color">{{ nav.icon }}</mat-icon>
            <span>{{ nav.label }}</span>
          </mat-card-content>
        </mat-card>
      </div>
    </div>
  `,
  styles: [`
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }
    .stat-card {
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.15) !important; }
    .stat-card mat-card-content {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 20px !important;
    }
    .stat-icon {
      width: 56px; height: 56px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .stat-icon mat-icon { font-size: 28px; height: 28px; width: 28px; }
    .stat-value { font-size: 28px; font-weight: 700; line-height: 1; }
    .stat-label { font-size: 14px; color: #666; margin-top: 4px; }
    .section-title { font-size: 18px; font-weight: 500; margin-bottom: 16px; color: #333; }
    .quick-nav-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 12px;
    }
    .quick-card {
      cursor: pointer;
      transition: transform 0.2s;
    }
    .quick-card:hover { transform: translateY(-2px); }
    .quick-card mat-card-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 20px 12px !important;
      text-align: center;
    }
    .quick-card mat-icon { font-size: 32px; height: 32px; width: 32px; }
    .quick-card span { font-size: 13px; color: #444; }
  `]
})
export class DashboardComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly batchService = inject(BatchService);
  private readonly paymentService = inject(PaymentService);

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
        this.stats.set([
          { label: 'Total Families', value: families.meta.total, icon: 'family_restroom', color: '#1a237e', route: '/families' },
          { label: 'Active Batches', value: batches.meta.total, icon: 'groups', color: '#2e7d32', route: '/batches' },
          { label: 'Pending Payments', value: payments.meta.total, icon: 'payments', color: '#e65100', route: '/payments' },
          { label: 'Notifications Sent', value: '—', icon: 'notifications', color: '#6a1b9a', route: '/notifications' },
        ]);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }
}
