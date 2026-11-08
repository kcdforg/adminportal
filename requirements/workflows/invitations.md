# Invitations — Cross-application workflow

**Workflow ID:** `WF-INVITATIONS`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for invitations without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `API-INV-001` (api-backend) — Create invitation
- `API-INV-002` (api-backend) — List invitations
- `API-INV-003` (api-backend) — View invitation status
- `API-INV-004` (api-backend) — Cancel invitation
- `API-INV-005` (api-backend) — Accept invitation and register account
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

## Source-supported backend operations

The following operations have matching PHP handler files. This establishes backend entry points, **not** successful end-to-end user journeys or the precise order in which users encounter them.

1. **Create invitation.** Source: `api-backend-php/api/invitations/store.php`.
2. **Retrieve invitation by code.** Source: `api-backend-php/api/invitations/show-by-code.php`.
3. **Accept invitation.** Source: `api-backend-php/api/invitations/accept.php`.
4. **Cancel invitation.** Source: `api-backend-php/api/invitations/cancel.php`.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
