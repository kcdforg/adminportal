import { Routes } from '@angular/router';
import { memberAuthGuard } from './core/member-auth.service';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./login/login.page').then((m) => m.LoginPage),
  },
  {
    path: '',
    loadComponent: () => import('./tabs/tabs.page').then((m) => m.TabsPage),
    canActivateChild: [memberAuthGuard],
    children: [
      {
        path: 'home',
        loadComponent: () => import('./home/home.page').then((m) => m.HomePage),
      },
      {
        path: 'groups',
        loadComponent: () => import('./groups/groups.page').then((m) => m.GroupsPage),
      },
      {
        path: 'courses',
        loadComponent: () => import('./courses/courses.page').then((m) => m.CoursesPage),
      },
      {
        path: 'posts',
        loadComponent: () => import('./posts/posts.page').then((m) => m.PostsPage),
      },
      {
        path: 'me',
        loadComponent: () => import('./me/me-section.page').then((m) => m.MeSectionPage),
        children: [
          {
            path: '',
            loadComponent: () => import('./me/profile.page').then((m) => m.ProfilePage),
          },
          {
            path: 'family',
            loadComponent: () => import('./family/family.page').then((m) => m.FamilyPage),
          },
        ],
      },
      {
        path: '',
        redirectTo: 'home',
        pathMatch: 'full',
      },
    ],
  },
  {
    path: '**',
    redirectTo: 'home',
  },
];
