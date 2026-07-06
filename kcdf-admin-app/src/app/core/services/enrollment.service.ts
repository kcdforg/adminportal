import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Enrollment, CreateEnrollmentRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class EnrollmentService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Enrollment>> {
    return this.api.list<Enrollment>('/enrollments', params);
  }

  get(id: number): Observable<ApiResponse<Enrollment>> {
    return this.api.get<Enrollment>(`/enrollments/${id}`);
  }

  create(data: CreateEnrollmentRequest): Observable<ApiResponse<Enrollment>> {
    return this.api.post<Enrollment>('/enrollments', data);
  }

  cancel(id: number): Observable<ApiResponse<Enrollment>> {
    return this.api.patch<Enrollment>(`/enrollments/${id}`, { status: 'cancelled' });
  }
}
