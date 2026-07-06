import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiListResponse, ApiResponse, Attendance, AttendanceStatus } from '../models';

export interface AttendanceRecord {
  enrollment_id: number;
  member_id: number;
  status: AttendanceStatus;
  notes?: string;
}

@Injectable({ providedIn: 'root' })
export class AttendanceService {
  private readonly api = inject(ApiService);

  getBySession(sessionId: number): Observable<ApiListResponse<Attendance>> {
    return this.api.list<Attendance>(`/sessions/${sessionId}/attendance`);
  }

  save(sessionId: number, records: AttendanceRecord[]): Observable<ApiResponse<Attendance[]>> {
    return this.api.post<Attendance[]>(`/sessions/${sessionId}/attendance`, { attendance: records });
  }

  update(id: number, data: { status: AttendanceStatus; notes?: string }): Observable<ApiResponse<Attendance>> {
    return this.api.patch<Attendance>(`/attendance/${id}`, data);
  }
}
