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

// Initialize with null, then set from localStorage when store is accessed
const _accessToken = signal<string | null>(null);
const _refreshToken = signal<string | null>(null);
const _user = signal<AdminUser | null>(null);
let _initialized = false;

function ensureInitialized() {
  if (!_initialized) {
    try {
      const accessToken = typeof localStorage !== 'undefined' ? localStorage.getItem('admin_access_token') : null;
      const refreshToken = typeof localStorage !== 'undefined' ? localStorage.getItem('admin_refresh_token') : null;
      _accessToken.set(accessToken);
      _refreshToken.set(refreshToken);
      _initialized = true;
    } catch (e) {
      console.warn('Failed to access localStorage:', e);
      _initialized = true;
    }
  }
}

const _adminRole = computed<AdminRole | null>(() => {
  ensureInitialized();
  const token = _accessToken();
  if (!token) return null;
  const payload = parseJwt(token);
  if (!payload) return null;
  return getAdminRole(payload.roles);
});

export const authStore = {
  accessToken: () => {
    ensureInitialized();
    return _accessToken();
  },
  refreshToken: () => {
    ensureInitialized();
    return _refreshToken();
  },
  user: _user.asReadonly(),
  adminRole: _adminRole,

  isAuthenticated: computed(() => {
    ensureInitialized();
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
    try {
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem('admin_access_token', access);
        localStorage.setItem('admin_refresh_token', refresh);
      }
    } catch (e) {
      console.warn('Failed to save tokens to localStorage:', e);
    }
    _accessToken.set(access);
    _refreshToken.set(refresh);
  },

  setUser(user: AdminUser) {
    _user.set(user);
  },

  clearAuth() {
    try {
      if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('admin_access_token');
        localStorage.removeItem('admin_refresh_token');
      }
    } catch (e) {
      console.warn('Failed to clear localStorage:', e);
    }
    _accessToken.set(null);
    _refreshToken.set(null);
    _user.set(null);
  },

  getAdminRole(): AdminRole | null {
    return _adminRole();
  },
};
