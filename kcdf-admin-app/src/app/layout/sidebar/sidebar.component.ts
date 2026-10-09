import { Component, computed, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { authStore } from '../../core/store/auth.store';
import { AdminRole } from '../../core/models';
import { AppIconComponent } from '../../shared/components/icon/app-icon.component';

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
  imports: [CommonModule, RouterModule, AppIconComponent],
  template: `
    <aside
      class="sidebar-panel fixed inset-y-0 left-0 z-50 flex w-[290px] flex-col border-r border-gray-200 bg-white transition-transform duration-300 dark:border-gray-800 dark:bg-gray-900"
      [class.sidebar-mobile-open]="mobileNavOpen">
      <div class="flex h-16 shrink-0 items-center gap-3 border-b border-gray-200 px-6 dark:border-gray-800">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white">
          <app-icon name="admin_panel_settings" aria-hidden="true" class="h-6 w-6"></app-icon>
        </span>
        <span class="min-w-0">
          <span class="block truncate text-base font-semibold text-gray-900 dark:text-white">KCDF Admin</span>
          <span class="block text-xs text-gray-500 dark:text-gray-400">Administration portal</span>
        </span>
      </div>

      <nav aria-label="Main navigation" class="flex-1 overflow-y-auto px-4 py-5">
        <ng-container *ngFor="let section of visibleSections()">
          <h2 class="mb-2 mt-5 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 first:mt-0 dark:text-gray-500">
            {{ section.title }}
          </h2>
          <a
            *ngFor="let item of section.items"
            [routerLink]="item.route"
            routerLinkActive="nav-active"
            [routerLinkActiveOptions]="{ exact: item.route === '/dashboard' }"
            class="nav-link mb-1 flex min-h-11 items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
            (click)="navigate.emit()">
            <app-icon [name]="item.icon" aria-hidden="true" class="h-5 w-5"></app-icon>
            <span>{{ item.label }}</span>
          </a>
        </ng-container>
      </nav>

      <div class="shrink-0 border-t border-gray-200 px-6 py-4 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">
        KCDF Admin Portal
      </div>
    </aside>
  `,
  styles: [`
    .sidebar-panel {
      transform: translateX(-100%);
      visibility: hidden;
    }

    .sidebar-panel.sidebar-mobile-open {
      transform: translateX(0);
      visibility: visible;
    }

    @media (min-width: 1280px) {
      .sidebar-panel {
        transform: translateX(0);
        visibility: visible;
      }
    }

    .nav-link.nav-active {
      background: #eef2ff;
      color: #4338ca;
    }

    .nav-link.nav-active app-icon {
      color: #4f46e5;
    }

    :host-context(.dark) .nav-link.nav-active {
      background: #312e81;
      color: #e0e7ff;
    }

    :host-context(.dark) .nav-link.nav-active app-icon {
      color: #c7d2fe;
    }
  `]
})
export class SidebarComponent {
  @Input() mobileNavOpen = false;
  @Output() readonly navigate = new EventEmitter<void>();

  private readonly sections: NavSection[] = [
    {
      title: 'Overview',
      items: [
        { label: 'Dashboard', icon: 'dashboard', route: '/dashboard', roles: ['super_admin', 'program_manager', 'accounts', 'readonly'] },
      ]
    },
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

  readonly visibleSections = computed(() => {
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
