import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ActivityLog, ListParams } from '../models';

@Injectable({ providedIn: 'root' })
export class ActivityLogService {
  private readonly api = inject(ApiService);

  list(params?: ListParams): Observable<ApiListResponse<ActivityLog>> {
    return this.api.list<ActivityLog>('/activity-logs', params);
  }
}
