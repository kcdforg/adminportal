import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import {
  ApiListResponse,
  ApiResponse,
  Group,
  GroupCategory,
  GroupContent,
  GroupMember,
  GroupMemberCandidate,
  ListParams,
} from '../models';

@Injectable({ providedIn: 'root' })
export class GroupService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Group>> {
    return this.api.list<Group>('/groups', params);
  }

  get(id: number): Observable<ApiResponse<Group>> {
    return this.api.get<Group>(`/groups/${id}`);
  }

  create(data: Pick<Group, 'group_name'> & Partial<Omit<Group, 'group_name'>>): Observable<ApiResponse<Group>> {
    return this.api.post<Group>('/groups', data);
  }

  update(id: number, data: Partial<Group>): Observable<ApiResponse<Group>> {
    return this.api.put<Group>(`/groups/${id}`, data);
  }

  getMembers(groupId: number, includeInactive = false): Observable<ApiResponse<GroupMember[]>> {
    return this.api.get<GroupMember[]>(`/groups/${groupId}/members`, includeInactive ? { include_inactive: 1 } : undefined);
  }

  getMemberCandidates(search?: string): Observable<ApiListResponse<GroupMemberCandidate>> {
    return this.api.list<GroupMemberCandidate>('/groups/member-candidates', {
      search,
      per_page: 100,
    });
  }

  addMember(groupId: number, memberId: number, memberRole: GroupMember['member_role'] = 'member'): Observable<ApiResponse<GroupMember>> {
    return this.api.post<GroupMember>(`/groups/${groupId}/members`, {
      member_id: memberId,
      member_role: memberRole,
    });
  }

  removeMember(groupId: number, memberId: number): Observable<ApiResponse<null>> {
    return this.api.delete<null>(`/groups/${groupId}/members/${memberId}`, { action: 'remove' });
  }

  banMember(groupId: number, memberId: number): Observable<ApiResponse<null>> {
    return this.api.delete<null>(`/groups/${groupId}/members/${memberId}`, { action: 'ban' });
  }

  updateMemberRole(
    groupId: number,
    memberId: number,
    memberRole: GroupMember['member_role'],
  ): Observable<ApiResponse<GroupMember>> {
    return this.api.put<GroupMember>(`/groups/${groupId}/members/${memberId}/role`, { member_role: memberRole });
  }

  getCategories(): Observable<ApiResponse<GroupCategory[]>> {
    return this.api.get<GroupCategory[]>('/groups/categories');
  }

  createCategory(data: Pick<GroupCategory, 'category_name' | 'description'>): Observable<ApiResponse<GroupCategory>> {
    return this.api.post<GroupCategory>('/groups/categories', data);
  }

  updateCategory(
    id: number,
    data: Partial<Pick<GroupCategory, 'category_name' | 'description'>>,
  ): Observable<ApiResponse<GroupCategory>> {
    return this.api.put<GroupCategory>(`/groups/categories/${id}`, data);
  }

  updateCategoryStatus(id: number, status: GroupCategory['status']): Observable<ApiResponse<GroupCategory>> {
    return this.api.patch<GroupCategory>(`/groups/categories/${id}/status`, { status });
  }

  listContent(groupId: number, resource: 'posts' | 'polls' | 'events'): Observable<ApiListResponse<GroupContent>> {
    return this.api.list<GroupContent>(`/groups/${groupId}/${resource}`, { per_page: 100 });
  }

  createContent(
    groupId: number,
    resource: 'posts' | 'polls' | 'events',
    data: Record<string, unknown>,
  ): Observable<ApiResponse<GroupContent>> {
    return this.api.post<GroupContent>(`/groups/${groupId}/${resource}`, data);
  }

  updateContent(
    groupId: number,
    resource: 'posts' | 'polls' | 'events',
    contentId: number,
    data: Record<string, unknown>,
  ): Observable<ApiResponse<GroupContent>> {
    return this.api.put<GroupContent>(`/groups/${groupId}/${resource}/${contentId}`, data);
  }

  archiveContent(
    groupId: number,
    resource: 'posts' | 'polls' | 'events',
    contentId: number,
  ): Observable<ApiResponse<null>> {
    return this.api.delete<null>(`/groups/${groupId}/${resource}/${contentId}`);
  }
}
