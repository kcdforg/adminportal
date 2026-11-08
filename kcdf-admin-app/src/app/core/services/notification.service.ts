import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import {
  ApiListResponse,
  ApiResponse,
  BroadcastNotificationRequest,
  Notification,
  SendNotificationRequest,
  ListParams,
} from '../models';

@Injectable({ providedIn: 'root' })
export class NotificationService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<Notification>> {
    return this.api.list<Notification>('/notifications', params);
  }

  send(data: SendNotificationRequest): Observable<ApiResponse<{ sent_to: number }>> {
    return this.api.post<{ sent_to: number }>('/notifications/send', data);
  }

  broadcast(data: BroadcastNotificationRequest): Observable<ApiResponse<{ sent_to: number }>> {
    return this.api.post<{ sent_to: number }>('/notifications/broadcast', data);
  }
}
