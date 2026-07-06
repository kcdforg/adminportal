import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { roleGuard } from './core/guards/role.guard';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./features/auth/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: '',
    loadComponent: () => import('./layout/main-layout/main-layout.component').then(m => m.MainLayoutComponent),
    canActivate: [authGuard],
    children: [
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      {
        path: 'dashboard',
        loadComponent: () => import('./features/dashboard/dashboard.component').then(m => m.DashboardComponent)
      },
      {
        path: 'members',
        loadComponent: () => import('./features/members/member-list/member-list.component').then(m => m.MemberListComponent)
      },
      {
        path: 'members/new',
        loadComponent: () => import('./features/members/member-form/member-form.component').then(m => m.MemberFormComponent)
      },
      {
        path: 'members/:id',
        loadComponent: () => import('./features/members/member-detail/member-detail.component').then(m => m.MemberDetailComponent)
      },
      {
        path: 'families',
        loadComponent: () => import('./features/families/family-list/family-list.component').then(m => m.FamilyListComponent)
      },
      {
        path: 'families/new',
        loadComponent: () => import('./features/families/family-form/family-form.component').then(m => m.FamilyFormComponent)
      },
      {
        path: 'families/:id',
        loadComponent: () => import('./features/families/family-detail/family-detail.component').then(m => m.FamilyDetailComponent)
      },
      {
        path: 'trainers',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/trainers/trainer-list/trainer-list.component').then(m => m.TrainerListComponent)
      },
      {
        path: 'trainers/:id',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/trainers/trainer-detail/trainer-detail.component').then(m => m.TrainerDetailComponent)
      },
      {
        path: 'programs',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/programs/program-list/program-list.component').then(m => m.ProgramListComponent)
      },
      {
        path: 'batches',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/batches/batch-list/batch-list.component').then(m => m.BatchListComponent)
      },
      {
        path: 'batches/new',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/batches/batch-form/batch-form.component').then(m => m.BatchFormComponent)
      },
      {
        path: 'batches/:id',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/batches/batch-detail/batch-detail.component').then(m => m.BatchDetailComponent)
      },
      {
        path: 'sessions/:id/attendance',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/sessions/session-attendance/session-attendance.component').then(m => m.SessionAttendanceComponent)
      },
      {
        path: 'enrollments',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager', 'accounts'] },
        loadComponent: () => import('./features/enrollments/enrollment-list/enrollment-list.component').then(m => m.EnrollmentListComponent)
      },
      {
        path: 'payments',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'accounts'] },
        loadComponent: () => import('./features/payments/payment-list/payment-list.component').then(m => m.PaymentListComponent)
      },
      {
        path: 'groups',
        canActivate: [roleGuard],
        data: { roles: ['super_admin'] },
        loadComponent: () => import('./features/groups/group-list/group-list.component').then(m => m.GroupListComponent)
      },
      {
        path: 'groups/:id',
        canActivate: [roleGuard],
        data: { roles: ['super_admin'] },
        loadComponent: () => import('./features/groups/group-detail/group-detail.component').then(m => m.GroupDetailComponent)
      },
      {
        path: 'notifications',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/notifications/notification-list/notification-list.component').then(m => m.NotificationListComponent)
      },
      {
        path: 'notifications/send',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager'] },
        loadComponent: () => import('./features/notifications/send-notification/send-notification.component').then(m => m.SendNotificationComponent)
      },
      {
        path: 'reports/attendance',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager', 'readonly'] },
        loadComponent: () => import('./features/reports/attendance-report/attendance-report.component').then(m => m.AttendanceReportComponent)
      },
      {
        path: 'reports/payments',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'accounts', 'readonly'] },
        loadComponent: () => import('./features/reports/payment-report/payment-report.component').then(m => m.PaymentReportComponent)
      },
      {
        path: 'reports/enrollments',
        canActivate: [roleGuard],
        data: { roles: ['super_admin', 'program_manager', 'accounts', 'readonly'] },
        loadComponent: () => import('./features/reports/enrollment-report/enrollment-report.component').then(m => m.EnrollmentReportComponent)
      },
      {
        path: 'audit-logs',
        canActivate: [roleGuard],
        data: { roles: ['super_admin'] },
        loadComponent: () => import('./features/audit-logs/audit-log-list/audit-log-list.component').then(m => m.AuditLogListComponent)
      },
    ]
  },
  { path: '**', redirectTo: '/dashboard' }
];
