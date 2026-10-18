import { HttpClient, HttpInterceptorFn } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { CanActivateChildFn, Router } from '@angular/router';
import { Observable, map, tap } from 'rxjs';
import { environment } from '../../environments/environment';

export interface MemberSessionProfile {
  id: number;
  profile_id: number;
  login_id: number;
  username: string;
  display_name: string;
  first_name: string;
  last_name: string;
  email: string;
  family_ids: number[];
}

export interface MemberProfile {
  id: number;
  first_name: string;
  middle_name?: string | null;
  last_name: string;
  email?: string | null;
  mobile?: string | null;
  date_of_birth?: string | null;
  gender?: string | null;
  photo_url?: string | null;
  blood_group?: string | null;
}

export interface FamilyMember {
  id: number;
  profile_id: number;
  relationship_type: string;
  member_role: string;
  profile: MemberProfile;
}

export interface ParentGroup {
  id: number;
  group_name: string;
  description: string | null;
  visibility: string;
  member_count?: number;
}

export interface Course {
  id: number;
  program_name: string;
  program_type: string;
  description?: string | null;
}

export interface CourseBatch {
  id: number;
  batch_name: string;
  start_date: string;
  status: string;
  program?: Course | null;
}

interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data: T;
}

interface LoginResponse {
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
  profile: MemberSessionProfile;
}

const ACCESS_TOKEN_KEY = 'kcdf_member_access_token';
const REFRESH_TOKEN_KEY = 'kcdf_member_refresh_token';
const PROFILE_KEY = 'kcdf_member_profile';

@Injectable({ providedIn: 'root' })
export class MemberSessionStore {
  readonly profile = signal<MemberSessionProfile | null>(this.readProfile());
  private readonly accessToken = signal(this.readStorage(ACCESS_TOKEN_KEY));
  private readonly refreshToken = signal(this.readStorage(REFRESH_TOKEN_KEY));

  getAccessToken(): string | null {
    return this.accessToken();
  }

  getRefreshToken(): string | null {
    return this.refreshToken();
  }

  isAuthenticated(): boolean {
    const token = this.accessToken();
    if (!token) {
      return false;
    }

    try {
      const encodedPayload = token.split('.')[1]?.replace(/-/g, '+').replace(/_/g, '/');
      if (!encodedPayload) {
        return false;
      }
      const paddedPayload = encodedPayload.padEnd(Math.ceil(encodedPayload.length / 4) * 4, '=');
      const bytes = Uint8Array.from(atob(paddedPayload), (character) => character.charCodeAt(0));
      const payload = JSON.parse(new TextDecoder().decode(bytes)) as { exp?: number };
      return typeof payload.exp === 'number' && payload.exp * 1000 > Date.now();
    } catch {
      return false;
    }
  }

  saveSession(response: LoginResponse): void {
    localStorage.setItem(ACCESS_TOKEN_KEY, response.access_token);
    localStorage.setItem(REFRESH_TOKEN_KEY, response.refresh_token);
    localStorage.setItem(PROFILE_KEY, JSON.stringify(response.profile));
    this.accessToken.set(response.access_token);
    this.refreshToken.set(response.refresh_token);
    this.profile.set(response.profile);
  }

  clearSession(): void {
    localStorage.removeItem(ACCESS_TOKEN_KEY);
    localStorage.removeItem(REFRESH_TOKEN_KEY);
    localStorage.removeItem(PROFILE_KEY);
    this.accessToken.set(null);
    this.refreshToken.set(null);
    this.profile.set(null);
  }

  private readStorage(key: string): string | null {
    return typeof localStorage === 'undefined' ? null : localStorage.getItem(key);
  }

  private readProfile(): MemberSessionProfile | null {
    const savedProfile = this.readStorage(PROFILE_KEY);
    if (!savedProfile) {
      return null;
    }
    try {
      return JSON.parse(savedProfile) as MemberSessionProfile;
    } catch {
      console.error('Could not restore the saved member profile.');
      return null;
    }
  }
}

export const memberAuthInterceptor: HttpInterceptorFn = (request, next) => {
  const token = inject(MemberSessionStore).getAccessToken();
  if (!token || !request.url.startsWith(environment.apiUrl)) {
    return next(request);
  }
  return next(request.clone({ setHeaders: { Authorization: `Bearer ${token}` } }));
};

@Injectable({ providedIn: 'root' })
export class MemberAuthService {
  private readonly http = inject(HttpClient);
  readonly session = inject(MemberSessionStore);

  login(credentials: { username: string; password: string }): Observable<LoginResponse> {
    return this.http
      .post<ApiResponse<LoginResponse>>(`${environment.apiUrl}/auth/login`, credentials)
      .pipe(
        map((response) => {
          if (!response.success) {
            throw new Error(response.message ?? 'Unable to sign in.');
          }
          return response.data;
        }),
        tap((response) => this.session.saveSession(response)),
      );
  }

  logout(): Observable<unknown> {
    return this.http.post(`${environment.apiUrl}/auth/logout`, {
      refresh_token: this.session.getRefreshToken(),
    });
  }
}

@Injectable({ providedIn: 'root' })
export class MemberApiService {
  private readonly http = inject(HttpClient);

  getProfile(profileId: number): Observable<MemberProfile> {
    return this.unwrap(this.http.get<ApiResponse<MemberProfile>>(`${environment.apiUrl}/members/${profileId}`));
  }

  getFamilyMembers(familyId: number): Observable<FamilyMember[]> {
    return this.unwrap(this.http.get<ApiResponse<FamilyMember[]>>(`${environment.apiUrl}/families/${familyId}/members`));
  }

  getGroups(): Observable<ParentGroup[]> {
    return this.unwrap(this.http.get<ApiResponse<ParentGroup[]>>(`${environment.apiUrl}/groups`));
  }

  getCourses(): Observable<Course[]> {
    return this.unwrap(this.http.get<ApiResponse<Course[]>>(`${environment.apiUrl}/programs`));
  }

  getBatches(): Observable<CourseBatch[]> {
    return this.unwrap(this.http.get<ApiResponse<CourseBatch[]>>(`${environment.apiUrl}/batches`));
  }

  private unwrap<T>(request: Observable<ApiResponse<T>>): Observable<T> {
    return request.pipe(
      map((response) => {
        if (!response.success) {
          throw new Error(response.message ?? 'Unable to load data.');
        }
        return response.data;
      }),
    );
  }
}

export const memberAuthGuard: CanActivateChildFn = (_route, state) => {
  const session = inject(MemberSessionStore);
  const router = inject(Router);
  if (session.isAuthenticated()) {
    return true;
  }
  session.clearSession();
  return router.createUrlTree(['/login'], { queryParams: { returnUrl: state.url } });
};
