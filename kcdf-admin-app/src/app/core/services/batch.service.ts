import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Batch, CreateBatchRequest, MemberProfile, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class BatchService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Batch>> {
    return this.api.list<Batch>('/batches', params);
  }

  get(id: number): Observable<ApiResponse<Batch>> {
    return this.api.get<Batch>(`/batches/${id}`);
  }

  create(data: CreateBatchRequest): Observable<ApiResponse<Batch>> {
    return this.api.post<Batch>('/batches', data);
  }

  update(id: number, data: Partial<CreateBatchRequest & { status: string }>): Observable<ApiResponse<Batch>> {
    return this.api.patch<Batch>(`/batches/${id}`, data);
  }

  getMembers(batchId: number): Observable<ApiListResponse<MemberProfile>> {
    return this.api.list<MemberProfile>(`/batches/${batchId}/members`);
  }

  addMember(batchId: number, memberId: number): Observable<ApiResponse<unknown>> {
    return this.api.post<unknown>(`/batches/${batchId}/members`, { member_id: memberId });
  }
}
