import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Program, CreateProgramRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class ProgramService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Program>> {
    return this.api.list<Program>('/programs', params);
  }

  get(id: number): Observable<ApiResponse<Program>> {
    return this.api.get<Program>(`/programs/${id}`);
  }

  create(data: CreateProgramRequest): Observable<ApiResponse<Program>> {
    return this.api.post<Program>('/programs', data);
  }

  update(id: number, data: Partial<CreateProgramRequest & { status: string }>): Observable<ApiResponse<Program>> {
    return this.api.patch<Program>(`/programs/${id}`, data);
  }
}
