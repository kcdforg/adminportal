# CAP-006: Sessions & Attendance

## 1. Purpose

Track which members attended each batch session.

## 2. Scope

Batch sessions, attendance capture and correction, locking attendance, and recording who changed it.

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-ATT-001` | Read session attendance | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-002` | Record session attendance | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-003` | Update an attendance record | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-004` | Require attendance record for update | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-005` | Authorize attendance updates | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-006` | Reject updates to locked attendance | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-007` | Require attendance status on update | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-008` | Restrict attendance status values | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-009` | Restrict updated attendance fields | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-010` | Record attendance update actor | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-ATT-011` | Recheck lock inside attendance transaction | Primary | [attendance.md](../applications/api-backend/functional/attendance.md) |
| `API-SES-001` | Browse batch sessions | Primary | [sessions.md](../applications/api-backend/functional/sessions.md) |
| `API-SES-002` | View session details | Primary | [sessions.md](../applications/api-backend/functional/sessions.md) |
| `API-SES-003` | Schedule batch session | Primary | [sessions.md](../applications/api-backend/functional/sessions.md) |
| `API-SES-004` | Update batch session | Primary | [sessions.md](../applications/api-backend/functional/sessions.md) |
| `API-SES-005` | Lock session attendance | Primary | [sessions.md](../applications/api-backend/functional/sessions.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-RPT-001` | View attendance report | Supporting | [reports.md](../applications/admin-portal/functional/reports.md) |
| `ADM-SES-001` | Schedule and update a session | Primary | [sessions.md](../applications/admin-portal/functional/sessions.md) |
| `ADM-SES-002` | Record attendance | Primary | [sessions.md](../applications/admin-portal/functional/sessions.md) |
| `ADM-SES-003` | Review, correct, and lock attendance | Primary | [sessions.md](../applications/admin-portal/functional/sessions.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-SESSION-ATTENDANCE` | Primary | [session-attendance.md](../workflows/session-attendance.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Batch Detail (admin) | `UI-ADM-004-01` … `UI-ADM-004-04` (4) | Supporting | [batch-detail.md](../applications/admin-portal/ui/screens/batches/batch-detail.md) |
| Attendance Report (admin) | `UI-ADM-027-01` … `UI-ADM-027-04` (4) | Supporting | [attendance-report.md](../applications/admin-portal/ui/screens/reports/attendance-report.md) |
| Session Attendance (admin) | `UI-ADM-030-01` … `UI-ADM-030-04` (4) | Primary | [session-attendance.md](../applications/admin-portal/ui/screens/sessions/session-attendance.md) |
| Session Form (admin) | `UI-ADM-031-01` … `UI-ADM-031-04` (4) | Primary | [session-form.md](../applications/admin-portal/ui/screens/sessions/session-form.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-REL-001`, `NFR-REL-002`, `NFR-OBS-002`
- **Proposed (not approved):** none specific
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-004 Program, Batch & Trainer Management](./CAP-004-program-batch-trainer-management.md) — sessions belong to batches
- [CAP-011 Reporting, Dashboard & Audit](./CAP-011-reporting-dashboard-audit.md) — attendance report

## 10. Implementation Status

**Implemented.** Lock rejection (`API-ATT-006`) and re-check inside the transaction (`API-ATT-011`) were source-checked; remaining session/attendance requirements were content-reviewed only.

## 11. Source-Code Evidence

- `api-backend-php/api/sessions/` (`lock.php`, `attendance-store.php`), `api/attendance/patch.php:13,31`, `services/academics.php:337` (`FOR UPDATE`).
- `kcdf-admin-app/src/app/features/sessions/session-attendance/`. Database: `batch_sessions`, `attendance` (`attendance_locked`).

Individually source-checked requirements for this capability: `API-ATT-006`, `API-ATT-011`. All other requirements were content-reviewed for mapping but not individually checked against code.

## 12. Known Gaps

- No member-facing view of attendance (none documented or implemented).
- `API-ATT-004..011` are fine-grained validation rules of `API-ATT-003`; they are atomic by design and were not merged.
