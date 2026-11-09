# Session Attendance — Cross-application workflow

**Workflow ID:** `WF-SESSION-ATTENDANCE`
**Status:** Draft; confirm attendance correction and session-lock policies.

## Purpose

Describe how staff schedule a batch session, record attendance, and review or correct attendance records.

## Participating requirements

- `API-BAT-001` through `API-BAT-005` — Browse and manage batches and batch membership
- `API-SES-001` through `API-SES-005` — Browse, schedule, update, and lock sessions
- `API-ATT-001` through `API-ATT-003` — Read, record, and update attendance
- Admin portal session and attendance screens

## User journey

1. An authorized staff member opens a batch and schedules or selects a session.
2. The staff member reviews the members eligible for attendance.
3. The staff member records each member’s attendance and saves the session record.
4. The staff member reviews the saved attendance and corrects errors when permitted.
5. The staff member locks the attendance record when it is complete, if locking is part of the approved process.

## Acceptance criteria

- Attendance is associated with the correct batch, session, and eligible members.
- The user can distinguish unrecorded attendance from recorded attendance.
- Saved changes are clearly confirmed and remain visible when the session is reopened.
- Corrections are restricted according to the agreed role and lock policy.
- Invalid or incomplete attendance submissions are reported without losing entered information.

## Open questions

- Define attendance statuses and whether partial attendance is supported.
- Define who may edit attendance and whether locked records can be reopened.
- Confirm whether members can view their attendance and whether any notifications are required.
