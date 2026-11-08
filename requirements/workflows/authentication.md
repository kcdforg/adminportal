# Authentication — Cross-application workflow

**Workflow ID:** `WF-AUTHENTICATION`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for authentication without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `ADM-AUTH-001` (admin-portal) — Admin portal sign-in
- `MEM-AUTH-002` (member-portal) — Member portal sign-in
- `API-AUTH-003` (api-backend) — JWT access token verification
- `API-AUTH-004` (api-backend) — Refresh access credentials
- `API-AUTH-005` (api-backend) — Logout and refresh-token revocation
- `API-AUTH-006` (api-backend) — Current authenticated account
- `ADM-AUTH-007` (admin-portal) — Require admin sign-in for protected screens
- `ADM-AUTH-008` (admin-portal) — Restrict administrator screens by role
- `MEM-AUTH-009` (member-portal) — Persist member session
- `MEM-AUTH-010` (member-portal) — Clear member session

## Source-supported backend operations

The following operations have matching PHP handler files. This establishes backend entry points, **not** successful end-to-end user journeys or the precise order in which users encounter them.

1. **Member sign-in.** Source: `api-backend-php/api/auth/login.php`.
2. **Administrator sign-in uses a distinct backend handler.** Source: `api-backend-php/api/auth/admin-login.php`.
3. **Authenticated account lookup.** Source: `api-backend-php/api/auth/me.php`.
4. **Refresh and logout operations.** Source: `api-backend-php/api/auth/refresh.php`.
5. **Logout operation.** Source: `api-backend-php/api/auth/logout.php`.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
