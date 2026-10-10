import { inject } from '@angular/core';
import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { catchError, switchMap, throwError, NEVER } from 'rxjs';
import { AuthService } from '../services/auth.service';
import { authStore } from '../store/auth.store';
import { ToastService } from '../../shared/components/toast/toast.service';

let isRefreshing = false;

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const toast = inject(ToastService);
  const authService = inject(AuthService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401
        && !req.url.includes('/auth/refresh')
        && !req.url.includes('/auth/login')
        && !req.url.includes('/auth/admin/login')) {
        if (!isRefreshing && authStore.refreshToken()) {
          isRefreshing = true;
          return authService.refresh().pipe(
            switchMap(() => {
              isRefreshing = false;
              const token = authStore.accessToken();
              return next(req.clone({ setHeaders: { Authorization: `Bearer ${token}` } }));
            }),
            catchError((refreshError) => {
              isRefreshing = false;
              authService.clearAuth();
              router.navigate(['/login']);
              return throwError(() => refreshError);
            })
          );
        } else {
          authService.clearAuth();
          router.navigate(['/login']);
          return NEVER;
        }
      } else if (error.status === 403) {
        toast.show("You don't have permission to perform this action", {
          variant: 'warning',
          durationMs: 4000,
          actionLabel: 'Close',
        });
      } else if (error.status === 404) {
        toast.show('Resource not found', {
          variant: 'info',
          durationMs: 3000,
          actionLabel: 'Close',
        });
      } else if (error.status >= 500) {
        toast.show('Server error. Please try again.', {
          variant: 'error',
          durationMs: 5000,
          actionLabel: 'Retry',
        });
      }
      return throwError(() => error);
    })
  );
};
