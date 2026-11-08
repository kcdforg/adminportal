import { inject, Injectable } from '@angular/core';
import { Observable, map } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Program, CreateProgramRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class ProgramService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Program>> {
    return this.api.list<ApiProgram>('/programs', params).pipe(
      map(response => ({ ...response, data: response.data.map(program => this.fromApi(program)) })),
    );
  }

  get(id: number): Observable<ApiResponse<Program>> {
    return this.api.get<ApiProgram>(`/programs/${id}`).pipe(
      map(response => ({ ...response, data: this.fromApi(response.data) })),
    );
  }

  create(data: CreateProgramRequest): Observable<ApiResponse<Program>> {
    return this.api.post<ApiProgram>('/programs', this.toApi(data)).pipe(
      map(response => ({ ...response, data: this.fromApi(response.data) })),
    );
  }

  update(id: number, data: Partial<CreateProgramRequest & { status: Program['status'] }>): Observable<ApiResponse<Program>> {
    return this.api.put<ApiProgram>(`/programs/${id}`, this.toApi(data)).pipe(
      map(response => ({ ...response, data: this.fromApi(response.data) })),
    );
  }

  private toApi(data: Partial<CreateProgramRequest & { status: Program['status'] }>): Record<string, unknown> {
    const { name, ...rest } = data;
    return {
      ...rest,
      ...(name !== undefined ? { program_name: name } : {}),
    };
  }

  private fromApi(program: ApiProgram): Program {
    return { ...program, name: program.program_name };
  }
}

type ApiProgram = Omit<Program, 'name'> & { program_name: string };
