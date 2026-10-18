import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, MemberLogin, MemberProfile, CreateMemberRequest, UpdateMemberRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class MemberService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<MemberProfile>> {
    return this.api.list<MemberProfile>('/members', params);
  }

  get(id: number): Observable<ApiResponse<MemberProfile>> {
    return this.api.get<MemberProfile>(`/members/${id}`);
  }

  create(data: CreateMemberRequest): Observable<ApiResponse<MemberProfile>> {
    return this.api.post<MemberProfile>('/members', data);
  }

  update(id: number, data: UpdateMemberRequest): Observable<ApiResponse<MemberProfile>> {
    return this.api.put<MemberProfile>(`/members/${id}`, data);
  }

  createLogin(id: number, data: { username: string; password: string; display_name?: string }): Observable<ApiResponse<MemberLogin>> {
    return this.api.post<MemberLogin>(`/members/${id}/login`, data);
  }

  updateLogin(id: number, data: { username?: string; password?: string; display_name?: string; is_active?: boolean }): Observable<ApiResponse<MemberLogin>> {
    return this.api.put<MemberLogin>(`/members/${id}/login`, data);
  }

  deleteLogin(id: number): Observable<ApiResponse<null>> {
    return this.api.delete<null>(`/members/${id}/login`);
  }
}
