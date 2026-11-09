# Module: Reporting, Dashboard & Audit

> **Module ID:** MOD-011  
> **Previous broad capability:** CAP-011 (retired as a capability ID; see [ID migration](./id-migration.md))

## Purpose

Give administrators operational visibility and an audit trail.

## Scope

Administration dashboard counts, three reports (attendance, payments, enrollments), report error handling, and activity-log browsing, filtering and inspection.

## Atomic business capabilities

The CAP IDs below identify coherent business abilities; detailed requirements remain authoritative in the linked application documents. Classification is documentation-level and is not a claim of complete source-code verification.

### CAP-048 — Dashboard and summary metrics

**Business outcome:** dashboard and summary metrics within the reporting, dashboard & audit module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-LOG-003` | Inspect activity change | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) | <!-- Mapping requires review -->
| `API-LOG-004` | Reject unauthorized access | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) | <!-- Mapping requires review -->
| `API-ATT-010` | Record attendance update actor | Supporting | [attendance.md](../applications/api-backend/functional/attendance.md) | <!-- Mapping requires review -->
| `ADM-LOG-001` | Browse activity records | Primary | [audit-logs.md](../applications/admin-portal/functional/audit-logs.md) | <!-- Mapping requires review -->
| `ADM-LOG-002` | Inspect activity details | Primary | [audit-logs.md](../applications/admin-portal/functional/audit-logs.md) | <!-- Mapping requires review -->
| `ADM-DSH-001` | View administration dashboard | Primary | [dashboard.md](../applications/admin-portal/functional/dashboard.md) |
| `ADM-RPT-004` | Handle report errors gracefully | Primary | [reports.md](../applications/admin-portal/functional/reports.md) | <!-- Mapping requires review -->
| Log Diff Dialog (admin) | `UI-ADM-002-01` … `UI-ADM-002-03` (3) | Primary | [log-diff-dialog.md](../applications/admin-portal/ui/screens/audit-logs/log-diff-dialog.md) | <!-- Mapping requires review -->
| Dashboard (admin) | `UI-ADM-007-01` … `UI-ADM-007-02` (2) | Primary | [dashboard.md](../applications/admin-portal/ui/screens/features/dashboard.md) |

### CAP-049 — Enrollment and attendance reporting

**Business outcome:** enrollment and attendance reporting within the reporting, dashboard & audit module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `ADM-RPT-001` | View attendance report | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |
| `ADM-RPT-003` | View enrollment report | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |
| Attendance Report (admin) | `UI-ADM-027-01` … `UI-ADM-027-04` (4) | Primary | [attendance-report.md](../applications/admin-portal/ui/screens/reports/attendance-report.md) |
| Enrollment Report (admin) | `UI-ADM-028-01` … `UI-ADM-028-04` (4) | Primary | [enrollment-report.md](../applications/admin-portal/ui/screens/reports/enrollment-report.md) |

### CAP-050 — Payment reporting

**Business outcome:** payment reporting within the reporting, dashboard & audit module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `ADM-RPT-002` | View payment report | Primary | [reports.md](../applications/admin-portal/functional/reports.md) |
| Payment Report (admin) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Primary | [payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) |

### CAP-051 — Activity log review

**Business outcome:** activity log review within the reporting, dashboard & audit module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-LOG-001` | View activity log | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) |
| `API-LOG-002` | Filter activity log | Primary | [activity-logs.md](../applications/api-backend/functional/activity-logs.md) |
| Audit Log List (admin) | `UI-ADM-001-01` … `UI-ADM-001-04` (4) | Primary | [audit-log-list.md](../applications/admin-portal/ui/screens/audit-logs/audit-log-list.md) |

## Related workflows and UI



### UI screen coverage

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Audit Log List (admin) | `UI-ADM-001-01` … `UI-ADM-001-04` (4) | Primary | [audit-log-list.md](../applications/admin-portal/ui/screens/audit-logs/audit-log-list.md) |
| Log Diff Dialog (admin) | `UI-ADM-002-01` … `UI-ADM-002-03` (3) | Primary | [log-diff-dialog.md](../applications/admin-portal/ui/screens/audit-logs/log-diff-dialog.md) |
| Dashboard (admin) | `UI-ADM-007-01` … `UI-ADM-007-02` (2) | Primary | [dashboard.md](../applications/admin-portal/ui/screens/features/dashboard.md) |
| Attendance Report (admin) | `UI-ADM-027-01` … `UI-ADM-027-04` (4) | Primary | [attendance-report.md](../applications/admin-portal/ui/screens/reports/attendance-report.md) |
| Enrollment Report (admin) | `UI-ADM-028-01` … `UI-ADM-028-04` (4) | Primary | [enrollment-report.md](../applications/admin-portal/ui/screens/reports/enrollment-report.md) |
| Payment Report (admin) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Primary | [payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) |



## Technical references



## Non-functional references

The following are historical module-level NFR relationships; see the [NFR index](../non-functional/index.md) for unchanged NFR definitions and approval statuses.



IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-OBS-002`, `NFR-PRF-001`
- **Proposed (not approved):** `NFR-PRF-002`, `NFR-PRF-003`, `NFR-OBS-001`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).



## Implementation evidence and gaps

Source inspection and implementation-status statements below were carried forward from the previous module-level catalog. They must **not** be interpreted as independently verified for every atomic CAP.

### Prior module-level assessment

**Implemented.** Reports, activity-log endpoints and admin screens exist; report/dashboard figures were not validated against data. Data-change auditing is written by about 45 handlers via `logActivity`.



### Prior source evidence

- `api-backend-php/api/reports/`, `services/reports.php:7`, `api/activity-logs/index.php`, `services/identity.php:51`; database `activity_logs` (indexes at `schema.sql:575-577`).
- `kcdf-admin-app/src/app/features/reports/`, `features/audit-logs/`, `features/dashboard/`.



### Known gaps

- Authentication events are not written to `activity_logs` (proposed `NFR-OBS-001`).
- Dashboard indicator definitions are undecided (open in `ADM-DSH-001` status).
- `API-LOG-004` (unauthorized access) overlaps with the general authorization requirement; both retained.


## Mapping review notes

- Automated classification assigned 7 requirement/screen rows to a provisional CAP because the title did not clearly identify a sub-capability. Review rows marked `Mapping requires review`.
- Shared workflows, UI screens, and NFRs are shown at module level; do not assume every reference applies to every atomic CAP.
