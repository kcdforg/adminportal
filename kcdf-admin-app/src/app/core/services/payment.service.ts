import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Payment, CreatePaymentRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class PaymentService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Payment>> {
    return this.api.list<Payment>('/payments', params);
  }

  get(id: number): Observable<ApiResponse<Payment>> {
    return this.api.get<Payment>(`/payments/${id}`);
  }

  create(data: CreatePaymentRequest): Observable<ApiResponse<Payment>> {
    return this.api.post<Payment>('/payments', data);
  }

  refund(data: CreatePaymentRequest): Observable<ApiResponse<Payment>> {
    return this.api.post<Payment>('/payments', { ...data, payment_type: 'refund' });
  }
}
