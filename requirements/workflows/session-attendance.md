# Session Attendance — Cross-application workflow

**Workflow ID:** `WF-SESSION-ATTENDANCE`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for session attendance without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `API-SES-001` (api-backend) — Browse batch sessions
- `API-SES-002` (api-backend) — View session details
- `API-SES-003` (api-backend) — Schedule batch session
- `API-SES-004` (api-backend) — Update batch session
- `API-SES-005` (api-backend) — Lock session attendance
- `API-ATT-001` (api-backend) — Read session attendance
- `API-ATT-002` (api-backend) — Record session attendance
- `API-ATT-003` (api-backend) — Update an attendance record
- `API-BAT-001` (api-backend) — Browse batches
- `API-BAT-002` (api-backend) — View batch details
- `API-BAT-003` (api-backend) — Create batch
- `API-BAT-004` (api-backend) — Update batch
- `API-BAT-005` (api-backend) — View batch membership

## Source-supported backend operations

The following operations have matching PHP handler files. This establishes backend entry points, **not** successful end-to-end user journeys or the precise order in which users encounter them.

1. **Retrieve session attendance.** Source: `api-backend-php/api/sessions/attendance.php`.
2. **Save session attendance.** Source: `api-backend-php/api/sessions/attendance-store.php`.
3. **Update attendance record.** Source: `api-backend-php/api/attendance/patch.php`.
4. **Lock session attendance.** Source: `api-backend-php/api/sessions/lock.php`.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
