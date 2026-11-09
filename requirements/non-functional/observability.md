# Observability — Non-Functional Requirements

Original requirement text and evidence are preserved below. Existing-baseline entries are not automatically approved; proposed entries remain unapproved.

## Existing observed baseline — pending owner acceptance

### NFR-OBS-002 — Data-change audit trail
- **Category / Scope:** Observability / API, Admin
- **Requirement:** Administrative and member-initiated data changes shall be recorded with actor, action, entity, and old/new values, and be viewable by authorized administrators.
- **Acceptance criteria:** After a create/update/cancel operation, an `activity_logs` row exists with actor and values; the audit-log screen lists it.
- **Capabilities:** CAP-011, CAP-007, CAP-005
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** [API-LOG-*](../applications/api-backend/functional/activity-logs.md), [ADM-LOG-*](../applications/admin-portal/functional/audit-logs.md)
- **Status / Origin:** Verified — about 45 write handlers call `logActivity`; some record a `null` actor (`api/enrollments/cancel.php:27`) / existing documented requirement
- **Evidence:** `api-backend-php/services/identity.php:51`, `database/schema.sql:565`.

### NFR-OBS-003 — Server-side error logging
- **Category / Scope:** Observability / API
- **Requirement:** Unexpected API and database failures shall be written to a server log.
- **Acceptance criteria:** A forced 500 produces a log entry containing the cause.
- **Capabilities:** all
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Status / Origin:** Verified (`error_log` in the exception handler; a Monolog logger service exists) / verified implementation constraint
- **Evidence:** `api-backend-php/http/helpers.php`, `services/logger.php`.

## Proposed — not approved

### NFR-OBS-001 — Security-event logging
- **Scope:** API
- **Requirement:** Login success/failure, logout, token refresh failure and password changes shall be logged with time, account and source.
- **Rationale:** `api/auth/*` neither calls `logActivity` nor a logger.
- **Acceptance criteria:** Each event creates a retrievable record; retention **TBD — requires approval**.
- **Capabilities / related:** CAP-001; supports NFR-SEC-007
- **Implementation status:** Not implemented.

### NFR-OBS-004 — Operational monitoring
- **Scope:** Hosting
- **Requirement:** A health/readiness check and deployment-failure notification shall be defined.
- **Rationale:** `api-backend-php/api/health/` is an empty directory and no route is registered; deployment is by webhook script.
- **Acceptance criteria:** **TBD — requires approval.**
- **Implementation status:** Not implemented.
