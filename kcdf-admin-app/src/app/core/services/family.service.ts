import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Family, FamilyMember, FamilyMemberRequest, CreateFamilyRequest, ListParams, Payment } from '../models';

@Injectable({ providedIn: 'root' })
export class FamilyService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Family>> {
    return this.api.list<Family>('/families', params);
  }

  get(id: number): Observable<ApiResponse<Family>> {
    return this.api.get<Family>(`/families/${id}`);
  }

  create(data: CreateFamilyRequest): Observable<ApiResponse<Family>> {
    return this.api.post<Family>('/families', data);
  }

  update(id: number, data: Partial<CreateFamilyRequest>): Observable<ApiResponse<Family>> {
    return this.api.put<Family>(`/families/${id}`, data);
  }

  getMembers(familyId: number): Observable<ApiResponse<FamilyMember[]>> {
    return this.api.get<FamilyMember[]>(`/families/${familyId}/members`);
  }

  addMember(familyId: number, data: FamilyMemberRequest): Observable<ApiResponse<FamilyMember>> {
    return this.api.post<FamilyMember>(`/families/${familyId}/members`, data);
  }

  updateMember(familyId: number, profileId: number, data: Partial<FamilyMemberRequest>): Observable<ApiResponse<FamilyMember>> {
    return this.api.put<FamilyMember>(`/families/${familyId}/members/${profileId}`, data);
  }

  removeMember(familyId: number, profileId: number): Observable<ApiResponse<null>> {
    return this.api.delete<null>(`/families/${familyId}/members/${profileId}`);
  }

  getPayments(familyId: number, params?: ListParams): Observable<ApiListResponse<Payment>> {
    return this.api.list<Payment>(`/families/${familyId}/payments`, params);
  }
}
