import { inject } from '@angular/core';
import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { catchError, switchMap, throwError } from 'rxjs';
import { AuthService } from '../services/auth.service';
import { authStore } from '../store/auth.store';

let isRefreshing = false;

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const snackBar = inject(MatSnackBar);
  const authService = inject(AuthService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401 && !req.url.includes('/auth/refresh') && !req.url.includes('/auth/login')) {
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
              return throwError(() => refreshError);
            })
          );
        } else {
          authService.clearAuth();
        }
      } else if (error.status === 403) {
        snackBar.open("You don't have permission to perform this action", 'Close', { duration: 4000 });
      } else if (error.status === 404) {
        snackBar.open('Resource not found', 'Close', { duration: 3000 });
      } else if (error.status >= 500) {
        snackBar.open('Server error. Please try again.', 'Retry', { duration: 5000 });
      }
      return throwError(() => error);
    })
  );
};
