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
  templateUrl: './sidebar.component.html',
  styleUrl: './sidebar.component.scss'
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
