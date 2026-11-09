# CAP-011: Reporting, Dashboard & Audit

## 1. Purpose

Give administrators operational visibility and an audit trail.

## 2. Scope

Administration dashboard counts, three reports (attendance, payments, enrollments), report error handling, and activity-log browsing, filtering and inspection.

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-LOG-001` | View activity log | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) |
| `API-LOG-002` | Filter activity log | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) |
| `API-LOG-003` | Inspect activity change | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) |
| `API-LOG-004` | Reject unauthorized access | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) |
| `API-ATT-010` | Record attendance update actor | Supporting | [attendance.md](../applications/api-backend/functional/attendance.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-LOG-001` | Browse activity records | Primary | [audit-logs.md](../applications/admin-portal/functional/audit-logs.md) |
| `ADM-LOG-002` | Inspect activity details | Primary | [audit-logs.md](../applications/admin-portal/functional/audit-logs.md) |
| `ADM-DSH-001` | View administration dashboard | Primary | [dashboard.md](../applications/admin-portal/functional/dashboard.md) |
| `ADM-RPT-001` | View attendance report | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |
| `ADM-RPT-002` | View payment report | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |
| `ADM-RPT-003` | View enrollment report | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |
| `ADM-RPT-004` | Handle report errors gracefully | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Audit Log List (admin) | `UI-ADM-001-01` … `UI-ADM-001-04` (4) | Primary | [audit-log-list.md](../applications/admin-portal/ui/screens/audit-logs/audit-log-list.md) |
| Log Diff Dialog (admin) | `UI-ADM-002-01` … `UI-ADM-002-03` (3) | Primary | [log-diff-dialog.md](../applications/admin-portal/ui/screens/audit-logs/log-diff-dialog.md) |
| Dashboard (admin) | `UI-ADM-007-01` … `UI-ADM-007-02` (2) | Primary | [dashboard.md](../applications/admin-portal/ui/screens/features/dashboard.md) |
| Attendance Report (admin) | `UI-ADM-027-01` … `UI-ADM-027-04` (4) | Primary | [attendance-report.md](../applications/admin-portal/ui/screens/reports/attendance-report.md) |
| Enrollment Report (admin) | `UI-ADM-028-01` … `UI-ADM-028-04` (4) | Primary | [enrollment-report.md](../applications/admin-portal/ui/screens/reports/enrollment-report.md) |
| Payment Report (admin) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Primary | [payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-OBS-002`, `NFR-PRF-001`
- **Proposed (not approved):** `NFR-PRF-002`, `NFR-PRF-003`, `NFR-OBS-001`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-006 Sessions & Attendance](./CAP-006-sessions-attendance.md) — attendance report
- [CAP-007 Payments](./CAP-007-payments.md) — payment report
- [CAP-005 Enrollment Management](./CAP-005-enrollment-management.md) — enrollment report
- [CAP-002 Family & Member Management](./CAP-002-family-member-management.md) — dashboard family count
- [CAP-004 Program, Batch & Trainer Management](./CAP-004-program-batch-trainer-management.md) — dashboard active batches

## 10. Implementation Status

**Implemented.** Reports, activity-log endpoints and admin screens exist; report/dashboard figures were not validated against data. Data-change auditing is written by about 45 handlers via `logActivity`.

## 11. Source-Code Evidence

- `api-backend-php/api/reports/`, `services/reports.php:7`, `api/activity-logs/index.php`, `services/identity.php:51`; database `activity_logs` (indexes at `schema.sql:575-577`).
- `kcdf-admin-app/src/app/features/reports/`, `features/audit-logs/`, `features/dashboard/`.

## 12. Known Gaps

- Authentication events are not written to `activity_logs` (proposed `NFR-OBS-001`).
- Dashboard indicator definitions are undecided (open in `ADM-DSH-001` status).
- `API-LOG-004` (unauthorized access) overlaps with the general authorization requirement; both retained.
