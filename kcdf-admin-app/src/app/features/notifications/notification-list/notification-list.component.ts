import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { NotificationService } from '../../../core/services/notification.service';
import { Notification } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';

@Component({
  selector: 'app-notification-list',
  standalone: true,
  imports: [CommonModule, RouterModule, MatCardModule, MatButtonModule, MatIconModule, MatTableModule, MatPaginatorModule, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Notifications" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" routerLink="/notifications/send"><mat-icon>send</mat-icon> Send Notification</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <table mat-table [dataSource]="notifications()" class="full-width">
          <ng-container matColumnDef="member"><th mat-header-cell *matHeaderCellDef>Member</th><td mat-cell *matCellDef="let n">{{ n.member?.first_name }} {{ n.member?.last_name }}</td></ng-container>
          <ng-container matColumnDef="title"><th mat-header-cell *matHeaderCellDef>Title</th><td mat-cell *matCellDef="let n">{{ n.title }}</td></ng-container>
          <ng-container matColumnDef="channel"><th mat-header-cell *matHeaderCellDef>Channel</th><td mat-cell *matCellDef="let n">{{ n.channel }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let n"><app-status-badge [status]="n.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="sent_at"><th mat-header-cell *matHeaderCellDef>Sent</th><td mat-cell *matCellDef="let n">{{ n.sent_at | date:'dd MMM yyyy HH:mm' }}</td></ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No notifications sent</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.full-width{width:100%}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class NotificationListComponent implements OnInit {
  private readonly notificationService = inject(NotificationService);
  readonly loading = signal(false);
  readonly notifications = signal<Notification[]>([]);
  readonly total = signal(0);
  readonly cols = ['member', 'title', 'channel', 'status', 'sent_at'];
  private page = 1;

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading.set(true);
    this.notificationService.list({ page: this.page, per_page: 20 }).subscribe({
      next: res => { this.notifications.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }
}
