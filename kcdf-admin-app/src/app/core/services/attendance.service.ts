import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { ApiResponse, Attendance, AttendanceStatus } from '../models';

export interface AttendanceRecord {
  member_id: number;
  status: AttendanceStatus;
  notes?: string;
}

@Injectable({ providedIn: 'root' })
export class AttendanceService {
  private readonly api = inject(ApiService);

  getBySession(sessionId: number): Observable<ApiResponse<Attendance[]>> {
    return this.api.get<Attendance[]>(`/sessions/${sessionId}/attendance`);
  }

  save(sessionId: number, records: AttendanceRecord[]): Observable<ApiResponse<{ records_saved: number }>> {
    return this.api.post<{ records_saved: number }>(`/sessions/${sessionId}/attendance`, {
      records: records.map(record => ({
        member_id: record.member_id,
        attendance_status: record.status,
        remarks: record.notes,
      })),
    });
  }

  update(id: number, data: { status: AttendanceStatus; notes?: string }): Observable<ApiResponse<Attendance>> {
    return this.api.patch<Attendance>(`/attendance/${id}`, data);
  }
}
