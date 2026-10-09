import { inject } from '@angular/core';
import { CanActivateFn, Router, ActivatedRouteSnapshot } from '@angular/router';
import { ToastService } from '../../shared/components/toast/toast.service';
import { authStore } from '../store/auth.store';
import { AdminRole } from '../models';

export const roleGuard: CanActivateFn = (route: ActivatedRouteSnapshot) => {
  const router = inject(Router);
  const toast = inject(ToastService);
  const allowedRoles: AdminRole[] = route.data['roles'] ?? [];
  const role = authStore.adminRole();

  if (role && allowedRoles.includes(role)) {
    return true;
  }

  toast.show("You don't have permission to access this page", {
    variant: 'warning',
    durationMs: 4000,
    actionLabel: 'Close',
  });
  return router.createUrlTree(['/dashboard']);
};
