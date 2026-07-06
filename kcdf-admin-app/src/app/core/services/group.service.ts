import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Group, GroupMember, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class GroupService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Group>> {
    return this.api.list<Group>('/groups', params);
  }

  get(id: number): Observable<ApiResponse<Group>> {
    return this.api.get<Group>(`/groups/${id}`);
  }

  create(data: Partial<Group>): Observable<ApiResponse<Group>> {
    return this.api.post<Group>('/groups', data);
  }

  update(id: number, data: Partial<Group>): Observable<ApiResponse<Group>> {
    return this.api.patch<Group>(`/groups/${id}`, data);
  }

  getMembers(groupId: number): Observable<ApiListResponse<GroupMember>> {
    return this.api.list<GroupMember>(`/groups/${groupId}/members`);
  }

  addMember(groupId: number, memberId: number): Observable<ApiResponse<GroupMember>> {
    return this.api.post<GroupMember>(`/groups/${groupId}/members`, { member_id: memberId });
  }

  removeMember(groupId: number, memberId: number): Observable<ApiResponse<null>> {
    return this.api.delete<null>(`/groups/${groupId}/members/${memberId}`);
  }

  banMember(groupId: number, memberId: number): Observable<ApiResponse<GroupMember>> {
    return this.api.patch<GroupMember>(`/groups/${groupId}/members/${memberId}`, { is_banned: true });
  }
}
