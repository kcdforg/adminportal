import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ApiResponse, ApiListResponse, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  private buildParams(params?: ListParams): HttpParams {
    let p = new HttpParams();
    if (!params) return p;
    for (const [key, value] of Object.entries(params)) {
      if (value !== null && value !== undefined && value !== '') {
        p = p.set(key, String(value));
      }
    }
    return p;
  }

  get<T>(path: string, params?: ListParams): Observable<ApiResponse<T>> {
    return this.http.get<ApiResponse<T>>(`${this.base}${path}`, { params: this.buildParams(params) });
  }

  list<T>(path: string, params?: ListParams): Observable<ApiListResponse<T>> {
    return this.http.get<unknown>(`${this.base}${path}`, { params: this.buildParams(params) }).pipe(
      map(response => this.normalizeListResponse<T>(response, path)),
    );
  }

  post<T>(path: string, body: unknown): Observable<ApiResponse<T>> {
    return this.http.post<ApiResponse<T>>(`${this.base}${path}`, body);
  }

  put<T>(path: string, body: unknown): Observable<ApiResponse<T>> {
    return this.http.put<ApiResponse<T>>(`${this.base}${path}`, body);
  }

  patch<T>(path: string, body: unknown): Observable<ApiResponse<T>> {
    return this.http.patch<ApiResponse<T>>(`${this.base}${path}`, body);
  }

  delete<T>(path: string): Observable<ApiResponse<T>> {
    return this.http.delete<ApiResponse<T>>(`${this.base}${path}`);
  }

  private normalizeListResponse<T>(response: unknown, path: string): ApiListResponse<T> {
    const candidate = this.isRecord(response) && this.isRecord(response['data'])
      ? response['data']
      : response;

    if (!this.isApiListResponse<T>(candidate)) {
      throw new TypeError(`Invalid list response from ${path}: expected data array and pagination metadata.`);
    }

    return candidate;
  }

  private isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
  }

  private isApiListResponse<T>(value: unknown): value is ApiListResponse<T> {
    if (!this.isRecord(value) || !Array.isArray(value['data']) || !this.isRecord(value['meta'])) {
      return false;
    }
    const meta = value['meta'];
    return typeof meta['total'] === 'number'
      && typeof meta['per_page'] === 'number'
      && typeof meta['current_page'] === 'number'
      && typeof meta['last_page'] === 'number';
  }
}
