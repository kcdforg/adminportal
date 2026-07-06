import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Trainer, CreateTrainerRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class TrainerService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Trainer>> {
    return this.api.list<Trainer>('/trainers', params);
  }

  get(id: number): Observable<ApiResponse<Trainer>> {
    return this.api.get<Trainer>(`/trainers/${id}`);
  }

  create(data: CreateTrainerRequest): Observable<ApiResponse<Trainer>> {
    return this.api.post<Trainer>('/trainers', data);
  }

  update(id: number, data: Partial<CreateTrainerRequest & { status: string }>): Observable<ApiResponse<Trainer>> {
    return this.api.patch<Trainer>(`/trainers/${id}`, data);
  }
}
