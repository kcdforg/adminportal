# Program Enrollment — Cross-application workflow

**Workflow ID:** `WF-PROGRAM-ENROLLMENT`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for program enrollment without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `API-PRG-001` (api-backend) — Browse programs through API
- `MEM-PRG-002` (member-portal) — Display courses in Ionic app
- `API-PRG-003` (api-backend) — Create program
- `API-PRG-004` (api-backend) — Update program
- `API-PRG-005` (api-backend) — Change program status
- `API-BAT-001` (api-backend) — Browse batches
- `API-BAT-002` (api-backend) — View batch details
- `API-BAT-003` (api-backend) — Create batch
- `API-BAT-004` (api-backend) — Update batch
- `API-BAT-005` (api-backend) — View batch membership
- `API-ENR-001` (api-backend) — Browse and view enrollments
- `API-ENR-002` (api-backend) — Enroll a family member in a batch
- `API-ENR-003` (api-backend) — Cancel enrollment

## Source-supported backend operations

The following operations have matching PHP handler files. This establishes backend entry points, **not** successful end-to-end user journeys or the precise order in which users encounter them.

1. **List available batches.** Source: `api-backend-php/api/batches/index.php`.
2. **Create enrollment.** Source: `api-backend-php/api/enrollments/store.php`.
3. **Cancel enrollment.** Source: `api-backend-php/api/enrollments/cancel.php`.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Confirmed integration discrepancy:** The Admin Portal cancellation request path differs from the backend cancellation route. See `source-audit/integration-confirmation-2026-11-08.md`.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
