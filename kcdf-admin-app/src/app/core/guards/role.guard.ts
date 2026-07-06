import { inject } from '@angular/core';
import { CanActivateFn, Router, ActivatedRouteSnapshot } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { authStore } from '../store/auth.store';
import { AdminRole } from '../models';

export const roleGuard: CanActivateFn = (route: ActivatedRouteSnapshot) => {
  const router = inject(Router);
  const snackBar = inject(MatSnackBar);
  const allowedRoles: AdminRole[] = route.data['roles'] ?? [];
  const role = authStore.adminRole();

  if (role && allowedRoles.includes(role)) {
    return true;
  }

  snackBar.open("You don't have permission to access this page", 'Close', { duration: 4000 });
  return router.createUrlTree(['/dashboard']);
};
