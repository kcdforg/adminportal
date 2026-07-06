import { Component, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatListModule } from '@angular/material/list';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';
import { authStore } from '../../core/store/auth.store';
import { AdminRole } from '../../core/models';

interface NavItem {
  label: string;
  icon: string;
  route: string;
  roles: AdminRole[];
}

interface NavSection {
  title: string;
  items: NavItem[];
}

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule, RouterModule, MatListModule, MatIconModule, MatDividerModule],
  template: `
    <div class="sidebar-header">
      <img src="assets/logo.png" alt="KCDF" class="sidebar-logo" onerror="this.style.display='none'">
      <span class="sidebar-title">KCDF Admin</span>
    </div>
    <mat-divider></mat-divider>
    <mat-nav-list dense>
      <ng-container *ngFor="let section of visibleSections()">
        <div class="nav-section-title">{{ section.title }}</div>
        <ng-container *ngFor="let item of section.items">
          <a mat-list-item [routerLink]="item.route" routerLinkActive="active-link">
            <mat-icon matListItemIcon>{{ item.icon }}</mat-icon>
            <span matListItemTitle>{{ item.label }}</span>
          </a>
        </ng-container>
        <mat-divider></mat-divider>
      </ng-container>
    </mat-nav-list>
  `,
  styles: [`
    :host { display: flex; flex-direction: column; height: 100%; }
    .sidebar-header {
      padding: 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      background: #1a237e;
      color: white;
    }
    .sidebar-logo { height: 32px; width: 32px; border-radius: 50%; }
    .sidebar-title { font-size: 18px; font-weight: 600; }
    .nav-section-title {
      padding: 12px 16px 4px;
      font-size: 11px;
      font-weight: 700;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .active-link { background: #e8eaf6 !important; color: #1a237e !important; font-weight: 600; }
    .active-link mat-icon { color: #1a237e !important; }
    mat-nav-list { overflow-y: auto; flex: 1; }
  `]
})
export class SidebarComponent {
  private readonly sections: NavSection[] = [
    {
      title: 'People',
      items: [
        { label: 'Members', icon: 'people', route: '/members', roles: ['super_admin', 'program_manager', 'accounts', 'readonly'] },
        { label: 'Families', icon: 'family_restroom', route: '/families', roles: ['super_admin', 'program_manager', 'accounts', 'readonly'] },
        { label: 'Trainers', icon: 'school', route: '/trainers', roles: ['super_admin', 'program_manager'] },
      ]
    },
    {
      title: 'Academics',
      items: [
        { label: 'Programs', icon: 'auto_stories', route: '/programs', roles: ['super_admin', 'program_manager'] },
        { label: 'Batches', icon: 'groups', route: '/batches', roles: ['super_admin', 'program_manager'] },
        { label: 'Enrollments', icon: 'assignment', route: '/enrollments', roles: ['super_admin', 'program_manager', 'accounts'] },
      ]
    },
    {
      title: 'Finance',
      items: [
        { label: 'Payments', icon: 'payments', route: '/payments', roles: ['super_admin', 'accounts'] },
      ]
    },
    {
      title: 'Community',
      items: [
        { label: 'Groups', icon: 'forum', route: '/groups', roles: ['super_admin'] },
        { label: 'Notifications', icon: 'notifications', route: '/notifications', roles: ['super_admin', 'program_manager'] },
      ]
    },
    {
      title: 'Reports',
      items: [
        { label: 'Attendance Report', icon: 'fact_check', route: '/reports/attendance', roles: ['super_admin', 'program_manager', 'readonly'] },
        { label: 'Payment Report', icon: 'receipt_long', route: '/reports/payments', roles: ['super_admin', 'accounts', 'readonly'] },
        { label: 'Enrollment Report', icon: 'bar_chart', route: '/reports/enrollments', roles: ['super_admin', 'program_manager', 'accounts', 'readonly'] },
      ]
    },
    {
      title: 'System',
      items: [
        { label: 'Audit Logs', icon: 'manage_search', route: '/audit-logs', roles: ['super_admin'] },
      ]
    },
  ];

  visibleSections = computed(() => {
    const role = authStore.adminRole();
    if (!role) return [];
    return this.sections
      .map(section => ({
        ...section,
        items: section.items.filter(item => item.roles.includes(role))
      }))
      .filter(section => section.items.length > 0);
  });
}
