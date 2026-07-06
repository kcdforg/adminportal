import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatMenuModule } from '@angular/material/menu';
import { MatChipsModule } from '@angular/material/chips';
import { SidebarComponent } from '../sidebar/sidebar.component';
import { AuthService } from '../../core/services/auth.service';
import { authStore } from '../../core/store/auth.store';

@Component({
  selector: 'app-main-layout',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    MatSidenavModule,
    MatToolbarModule,
    MatIconModule,
    MatButtonModule,
    MatMenuModule,
    MatChipsModule,
    SidebarComponent,
  ],
  template: `
    <mat-sidenav-container class="app-container">
      <mat-sidenav mode="side" opened class="app-sidenav">
        <app-sidebar></app-sidebar>
      </mat-sidenav>

      <mat-sidenav-content class="app-content">
        <mat-toolbar class="app-toolbar" color="primary">
          <span class="toolbar-title">KCDF Admin Portal</span>
          <span class="toolbar-spacer"></span>

          <div class="toolbar-user" [matMenuTriggerFor]="userMenu">
            <span class="user-name">{{ user()?.first_name }} {{ user()?.last_name }}</span>
            <span class="role-badge" *ngIf="adminRole()">{{ roleLabel() }}</span>
            <mat-icon>expand_more</mat-icon>
          </div>

          <mat-menu #userMenu="matMenu">
            <button mat-menu-item (click)="logout()">
              <mat-icon>logout</mat-icon>
              <span>Logout</span>
            </button>
          </mat-menu>
        </mat-toolbar>

        <div class="app-body">
          <router-outlet></router-outlet>
        </div>
      </mat-sidenav-content>
    </mat-sidenav-container>
  `,
  styles: [`
    .app-container { height: 100vh; }
    .app-sidenav { width: 240px; border-right: 1px solid #e0e0e0; }
    .app-toolbar {
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .toolbar-title { font-size: 18px; font-weight: 500; }
    .toolbar-spacer { flex: 1; }
    .toolbar-user {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      padding: 4px 8px;
      border-radius: 4px;
    }
    .toolbar-user:hover { background: rgba(255,255,255,0.1); }
    .user-name { font-size: 14px; }
    .role-badge {
      padding: 2px 8px;
      background: rgba(255,255,255,0.2);
      border-radius: 12px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .app-body { padding: 24px; min-height: calc(100vh - 64px); background: #f5f5f5; }
  `]
})
export class MainLayoutComponent {
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  readonly user = authStore.user;
  readonly adminRole = authStore.adminRole;

  roleLabel(): string {
    const role = this.adminRole();
    const labels: Record<string, string> = {
      super_admin: 'Super Admin',
      program_manager: 'Program Manager',
      accounts: 'Accounts',
      readonly: 'Read Only',
    };
    return role ? (labels[role] ?? role) : '';
  }

  logout(): void {
    this.authService.logout().subscribe({
      error: () => {
        this.authService.clearAuth();
      }
    });
  }
}
