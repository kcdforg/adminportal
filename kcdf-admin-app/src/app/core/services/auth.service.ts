import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap, map } from 'rxjs/operators';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { authStore } from '../store/auth.store';
import { AuthTokens, LoginRequest, ApiResponse, AdminUser } from '../models';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);
  private readonly router = inject(Router);

  readonly currentUser = authStore.user;
  readonly isAuthenticated = authStore.isAuthenticated;
  readonly adminRole = authStore.adminRole;

  login(credentials: LoginRequest): Observable<ApiResponse<{ tokens: AuthTokens; user: AdminUser }>> {
    return this.http
      .post<any>(
        `${environment.apiUrl}/auth/admin/login`,
        credentials
      )
      .pipe(
        map((res) => {
          if (res.success) {
            const { access_token, refresh_token, token_type, expires_in, profile } = res.data;
            return {
              success: true,
              message: res.message,
              data: {
                tokens: {
                  access_token,
                  refresh_token,
                  token_type,
                  expires_in,
                },
                user: {
                  id: profile.id,
                  profile_id: profile.profile_id,
                  username: profile.username,
                  display_name: profile.display_name,
                  admin_role: profile.role,
                  first_name: profile.first_name,
                  last_name: profile.last_name,
                  email: profile.email,
                } as AdminUser,
              },
            };
          }
          return res;
        }),
        tap((res) => {
          if (res.success) {
            authStore.setTokens(res.data.tokens.access_token, res.data.tokens.refresh_token);
            authStore.setUser(res.data.user);
          }
        })
      );
  }

  logout(): Observable<ApiResponse<null>> {
    return this.http
      .post<ApiResponse<null>>(`${environment.apiUrl}/auth/logout`, {
        refresh_token: authStore.refreshToken(),
      })
      .pipe(
        tap(() => {
          authStore.clearAuth();
          this.router.navigate(['/login']);
        })
      );
  }

  refresh(): Observable<ApiResponse<{ tokens: AuthTokens }>> {
    return this.http
      .post<any>(`${environment.apiUrl}/auth/refresh`, {
        refresh_token: authStore.refreshToken(),
      })
      .pipe(
        map((res) => {
          if (res.success) {
            const { access_token, refresh_token, token_type, expires_in, profile } = res.data;
            return {
              success: true,
              message: res.message,
              data: {
                tokens: {
                  access_token,
                  refresh_token,
                  token_type,
                  expires_in,
                },
              },
            };
          }
          return res;
        }),
        tap((res) => {
          if (res.success) {
            authStore.setTokens(res.data.tokens.access_token, res.data.tokens.refresh_token);
          }
        })
      );
  }

  fetchMe(): Observable<ApiResponse<AdminUser>> {
    return this.http
      .get<ApiResponse<AdminUser>>(`${environment.apiUrl}/auth/me`)
      .pipe(tap((res) => res.success && authStore.setUser(res.data)));
  }

  clearAuth(): void {
    authStore.clearAuth();
    this.router.navigate(['/login']);
  }

  getAccessToken(): string | null {
    return authStore.accessToken();
  }

  getRefreshToken(): string | null {
    return authStore.refreshToken();
  }
}
