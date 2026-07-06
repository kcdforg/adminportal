import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Session, CreateSessionRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class SessionService {
  private readonly api = inject(ApiService);

  getByBatch(batchId: number, params?: ListParams): Observable<ApiListResponse<Session>> {
    return this.api.list<Session>(`/batches/${batchId}/sessions`, params);
  }

  get(id: number): Observable<ApiResponse<Session>> {
    return this.api.get<Session>(`/sessions/${id}`);
  }

  create(data: CreateSessionRequest): Observable<ApiResponse<Session>> {
    return this.api.post<Session>(`/batches/${data.batch_id}/sessions`, data);
  }

  update(id: number, data: Partial<CreateSessionRequest & { status: string }>): Observable<ApiResponse<Session>> {
    return this.api.patch<Session>(`/sessions/${id}`, data);
  }

  lock(id: number): Observable<ApiResponse<Session>> {
    return this.api.patch<Session>(`/sessions/${id}`, { attendance_locked: true });
  }
}
