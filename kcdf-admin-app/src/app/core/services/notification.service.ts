import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Notification, SendNotificationRequest, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class NotificationService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Notification>> {
    return this.api.list<Notification>('/notifications', params);
  }

  send(data: SendNotificationRequest): Observable<ApiResponse<unknown>> {
    return this.api.post<unknown>('/notifications', data);
  }
}
