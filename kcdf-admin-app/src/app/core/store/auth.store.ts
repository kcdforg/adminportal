import { computed, signal } from '@angular/core';
import { AdminUser, AdminRole, JwtPayload } from '../models';

function parseJwt(token: string): JwtPayload | null {
  try {
    const payload = token.split('.')[1];
    return JSON.parse(atob(payload)) as JwtPayload;
  } catch {
    return null;
  }
}

function getAdminRole(roles: string[]): AdminRole | null {
  if (roles.includes('admin_super')) return 'super_admin';
  if (roles.includes('admin_program_manager')) return 'program_manager';
  if (roles.includes('admin_accounts')) return 'accounts';
  if (roles.includes('admin_readonly')) return 'readonly';
  return null;
}

const _accessToken = signal<string | null>(localStorage.getItem('admin_access_token'));
const _refreshToken = signal<string | null>(localStorage.getItem('admin_refresh_token'));
const _user = signal<AdminUser | null>(null);

const _adminRole = computed<AdminRole | null>(() => {
  const token = _accessToken();
  if (!token) return null;
  const payload = parseJwt(token);
  if (!payload) return null;
  return getAdminRole(payload.roles);
});

export const authStore = {
  accessToken: _accessToken.asReadonly(),
  refreshToken: _refreshToken.asReadonly(),
  user: _user.asReadonly(),
  adminRole: _adminRole,

  isAuthenticated: computed(() => {
    const token = _accessToken();
    if (!token) return false;
    const payload = parseJwt(token);
    if (!payload) return false;
    return payload.exp * 1000 > Date.now();
  }),

  isAdmin: computed(() => _adminRole() !== null),

  hasRole: (role: AdminRole) => computed(() => _adminRole() === role),

  canAccess: (allowedRoles: AdminRole[]) =>
    computed(() => {
      const role = _adminRole();
      return role !== null && allowedRoles.includes(role);
    }),

  setTokens(access: string, refresh: string) {
    localStorage.setItem('admin_access_token', access);
    localStorage.setItem('admin_refresh_token', refresh);
    _accessToken.set(access);
    _refreshToken.set(refresh);
  },

  setUser(user: AdminUser) {
    _user.set(user);
  },

  clearAuth() {
    localStorage.removeItem('admin_access_token');
    localStorage.removeItem('admin_refresh_token');
    _accessToken.set(null);
    _refreshToken.set(null);
    _user.set(null);
  },

  getAdminRole(): AdminRole | null {
    return _adminRole();
  },
};
