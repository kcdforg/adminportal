# Family Registration — Cross-application workflow

**Workflow ID:** `WF-FAMILY-REGISTRATION`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for family registration without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `API-FAM-001` (api-backend) — Browse families as administrator
- `API-FAM-002` (api-backend) — View family details
- `API-FAM-003` (api-backend) — Create family
- `API-FAM-004` (api-backend) — Update family
- `API-FAM-005` (api-backend) — Add family member association
- `API-FAM-006` (api-backend) — Update family member association
- `API-MEM-001` (api-backend) — Browse member profiles
- `API-MEM-002` (api-backend) — View member profile
- `API-MEM-003` (api-backend) — Create member profile
- `API-MEM-004` (api-backend) — Update member profile
- `API-MEM-005` (api-backend) — Create member login
- `API-MEM-006` (api-backend) — Maintain member login
- `API-MEM-007` (api-backend) — Manage member entity relations
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

1. **Family record creation.** Source: `api-backend-php/api/families/store.php`.
2. **Member profile creation.** Source: `api-backend-php/api/members/store.php`.

## Candidate references not located at expected paths

- Family-member association: `api-backend-php/api/families/add-member.php` — inspect route registry for actual handler path.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
