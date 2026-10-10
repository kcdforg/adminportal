import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { SidebarComponent } from '../sidebar/sidebar.component';
import { AuthService } from '../../core/services/auth.service';
import { authStore } from '../../core/store/auth.store';
import { AppIconComponent } from '../../shared/components/icon/app-icon.component';

@Component({
  selector: 'app-main-layout',
  standalone: true,
  imports: [CommonModule, RouterModule, SidebarComponent, AppIconComponent],
  templateUrl: './main-layout.component.html',
  styleUrl: './main-layout.component.scss'
})
export class MainLayoutComponent {
  private readonly authService = inject(AuthService);

  readonly user = authStore.user;
  readonly adminRole = authStore.adminRole;
  readonly mobileNavOpen = signal(false);

  toggleMobileNav(): void {
    this.mobileNavOpen.update(open => !open);
  }

  closeMobileNav(): void {
    this.mobileNavOpen.set(false);
  }

  userInitials(): string {
    const user = this.user();
    const initials = `${user?.first_name?.[0] ?? ''}${user?.last_name?.[0] ?? ''}`.trim();
    return initials || 'KC';
  }

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
