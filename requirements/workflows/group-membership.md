# Group Membership — Cross-application workflow

**Workflow ID:** `WF-GROUP-MEMBERSHIP`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for group membership without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `API-GRP-001` (api-backend) — Browse groups
- `API-GRP-002` (api-backend) — View group details
- `API-GRP-003` (api-backend) — Create group
- `API-GRP-004` (api-backend) — Update group
- `API-GRP-005` (api-backend) — Browse group categories
- `API-GRP-006` (api-backend) — Manage group categories
- `API-GRP-007` (api-backend) — Request to join group
- `API-GRP-008` (api-backend) — Leave group
- `API-GRP-009` (api-backend) — Manage group membership
- `API-GRP-010` (api-backend) — Find eligible group member candidates
- `API-GRP-011` (api-backend) — Change group member role

## Source-supported backend operations

The following operations have matching PHP handler files. This establishes backend entry points, **not** successful end-to-end user journeys or the precise order in which users encounter them.

1. **Group discovery.** Source: `api-backend-php/api/groups/index.php`.
2. **Join group.** Source: `api-backend-php/api/groups/join.php`.
3. **Leave group.** Source: `api-backend-php/api/groups/leave.php`.
4. **Manage group members.** Source: `api-backend-php/api/groups/members.php`.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
