# Migration Inventory

This document is the migration baseline for porting the Slim-based `kcdf-api-backend` into the plain-PHP runtime in `api-backend-php/`.

## Source of truth

- Slim backend: `kcdf-api-backend/`
- Plain PHP runtime: `api-backend-php/`
- API conventions: `docs/02-api-conventions.md`

## Routing baseline

The current Slim source registers all routes under `/api/v1` and groups them by module. This migration keeps the same URLs and HTTP methods, but resolves each route to a fixed direct endpoint file under `api/`.

| HTTP method | Public URL | FastRoute registration | Direct endpoint |
| --- | --- | --- | --- |
| POST | `/api/v1/auth/login` | `POST /api/v1/auth/login` | `api/auth/login.php` |
| POST | `/api/v1/auth/admin/login` | `POST /api/v1/auth/admin/login` | `api/auth/admin-login.php` |
| POST | `/api/v1/auth/refresh` | `POST /api/v1/auth/refresh` | `api/auth/refresh.php` |
| POST | `/api/v1/auth/logout` | `POST /api/v1/auth/logout` | `api/auth/logout.php` |
| GET | `/api/v1/auth/me` | `GET /api/v1/auth/me` | `api/auth/me.php` |
| GET | `/api/v1/programs` | `GET /api/v1/programs` | `api/programs/index.php` |
| POST | `/api/v1/programs` | `POST /api/v1/programs` | `api/programs/store.php` |
| GET | `/api/v1/programs/{id}` | `GET /api/v1/programs/{id}` | `api/programs/show.php` |
| PUT | `/api/v1/programs/{id}` | `PUT /api/v1/programs/{id}` | `api/programs/update.php` |
| PATCH | `/api/v1/programs/{id}/status` | `PATCH /api/v1/programs/{id}/status` | `api/programs/status.php` |
| GET | `/api/v1/batches` | `GET /api/v1/batches` | `api/batches/index.php` |
| POST | `/api/v1/batches` | `POST /api/v1/batches` | `api/batches/store.php` |
| GET | `/api/v1/batches/{id}` | `GET /api/v1/batches/{id}` | `api/batches/show.php` |
| PUT | `/api/v1/batches/{id}` | `PUT /api/v1/batches/{id}` | `api/batches/update.php` |
| GET | `/api/v1/batches/{id}/members` | `GET /api/v1/batches/{id}/members` | `api/batches/members.php` |
| GET | `/api/v1/batches/{id}/sessions` | `GET /api/v1/batches/{id}/sessions` | `api/batches/sessions.php` |
| POST | `/api/v1/batches/{id}/sessions` | `POST /api/v1/batches/{id}/sessions` | `api/batches/session-store.php` |
| GET | `/api/v1/sessions/{id}` | `GET /api/v1/sessions/{id}` | `api/sessions/show.php` |
| PUT | `/api/v1/sessions/{id}` | `PUT /api/v1/sessions/{id}` | `api/sessions/update.php` |
| POST | `/api/v1/sessions/{id}/lock` | `POST /api/v1/sessions/{id}/lock` | `api/sessions/lock.php` |
| GET | `/api/v1/sessions/{id}/attendance` | `GET /api/v1/sessions/{id}/attendance` | `api/sessions/attendance.php` |
| POST | `/api/v1/sessions/{id}/attendance` | `POST /api/v1/sessions/{id}/attendance` | `api/sessions/attendance-store.php` |
| PATCH | `/api/v1/attendance/{id}` | `PATCH /api/v1/attendance/{id}` | `api/attendance/patch.php` |
| GET | `/api/v1/enrollments` | `GET /api/v1/enrollments` | `api/enrollments/index.php` |
| POST | `/api/v1/enrollments` | `POST /api/v1/enrollments` | `api/enrollments/store.php` |
| GET | `/api/v1/enrollments/{id}` | `GET /api/v1/enrollments/{id}` | `api/enrollments/show.php` |
| PATCH | `/api/v1/enrollments/{id}/cancel` | `PATCH /api/v1/enrollments/{id}/cancel` | `api/enrollments/cancel.php` |
| GET | `/api/v1/payments` | `GET /api/v1/payments` | `api/payments/index.php` |
| POST | `/api/v1/payments` | `POST /api/v1/payments` | `api/payments/store.php` |
| GET | `/api/v1/payments/{id}` | `GET /api/v1/payments/{id}` | `api/payments/show.php` |
| PATCH | `/api/v1/payments/{id}` | `PATCH /api/v1/payments/{id}` | `api/payments/update.php` |
| GET | `/api/v1/families/{id}/payments` | `GET /api/v1/families/{id}/payments` | `api/payments/family-payments.php` |
| GET | `/api/v1/groups` | `GET /api/v1/groups` | `api/groups/index.php` |
| POST | `/api/v1/groups` | `POST /api/v1/groups` | `api/groups/store.php` |
| GET | `/api/v1/groups/{id}` | `GET /api/v1/groups/{id}` | `api/groups/show.php` |
| PUT | `/api/v1/groups/{id}` | `PUT /api/v1/groups/{id}` | `api/groups/update.php` |
| GET | `/api/v1/groups/{id}/members` | `GET /api/v1/groups/{id}/members` | `api/groups/members.php` |
| POST | `/api/v1/groups/{id}/join` | `POST /api/v1/groups/{id}/join` | `api/groups/join.php` |
| DELETE | `/api/v1/groups/{id}/leave` | `DELETE /api/v1/groups/{id}/leave` | `api/groups/leave.php` |
| DELETE | `/api/v1/groups/{id}/members/{member_id}` | `DELETE /api/v1/groups/{id}/members/{member_id}` | `api/groups/remove-member.php` |
| GET | `/api/v1/invitations` | `GET /api/v1/invitations` | `api/invitations/index.php` |
| POST | `/api/v1/invitations` | `POST /api/v1/invitations` | `api/invitations/store.php` |
| DELETE | `/api/v1/invitations/{id}` | `DELETE /api/v1/invitations/{id}` | `api/invitations/cancel.php` |
| GET | `/api/v1/invitations/{code}` | `GET /api/v1/invitations/{code}` | `api/invitations/show-by-code.php` |
| POST | `/api/v1/invitations/{code}/accept` | `POST /api/v1/invitations/{code}/accept` | `api/invitations/accept.php` |
| GET | `/api/v1/members` | `GET /api/v1/members` | `api/members/index.php` |
| POST | `/api/v1/members` | `POST /api/v1/members` | `api/members/store.php` |
| GET | `/api/v1/members/{id}` | `GET /api/v1/members/{id}` | `api/members/show.php` |
| PUT | `/api/v1/members/{id}` | `PUT /api/v1/members/{id}` | `api/members/update.php` |
| POST | `/api/v1/members/{id}/login` | `POST /api/v1/members/{id}/login` | `api/members/login-store.php` |
| GET | `/api/v1/members/{id}/entity-relations` | `GET /api/v1/members/{id}/entity-relations` | `api/members/entity-relations.php` |
| POST | `/api/v1/members/{id}/entity-relations` | `POST /api/v1/members/{id}/entity-relations` | `api/members/entity-relation-store.php` |
| DELETE | `/api/v1/members/{id}/entity-relations/{relation_id}` | `DELETE /api/v1/members/{id}/entity-relations/{relation_id}` | `api/members/entity-relation-delete.php` |
| GET | `/api/v1/families` | `GET /api/v1/families` | `api/families/index.php` |
| POST | `/api/v1/families` | `POST /api/v1/families` | `api/families/store.php` |
| GET | `/api/v1/families/{id}` | `GET /api/v1/families/{id}` | `api/families/show.php` |
| PUT | `/api/v1/families/{id}` | `PUT /api/v1/families/{id}` | `api/families/update.php` |
| GET | `/api/v1/families/{id}/members` | `GET /api/v1/families/{id}/members` | `api/families/members.php` |
| POST | `/api/v1/families/{id}/members` | `POST /api/v1/families/{id}/members` | `api/families/members-store.php` |
| DELETE | `/api/v1/families/{id}/members/{profile_id}` | `DELETE /api/v1/families/{id}/members/{profile_id}` | `api/families/members-destroy.php` |
| GET | `/api/v1/trainers` | `GET /api/v1/trainers` | `api/trainers/index.php` |
| POST | `/api/v1/trainers` | `POST /api/v1/trainers` | `api/trainers/store.php` |
| GET | `/api/v1/trainers/{id}` | `GET /api/v1/trainers/{id}` | `api/trainers/show.php` |
| PUT | `/api/v1/trainers/{id}` | `PUT /api/v1/trainers/{id}` | `api/trainers/update.php` |
| GET | `/api/v1/admins` | `GET /api/v1/admins` | `api/admins/index.php` |
| POST | `/api/v1/admins` | `POST /api/v1/admins` | `api/admins/store.php` |
| POST | `/api/v1/admins/login-accounts` | `POST /api/v1/admins/login-accounts` | `api/admins/store.php` |
| GET | `/api/v1/admins/{id}` | `GET /api/v1/admins/{id}` | `api/admins/show.php` |
| PUT | `/api/v1/admins/{id}` | `PUT /api/v1/admins/{id}` | `api/admins/update.php` |
| GET | `/api/v1/entities` | `GET /api/v1/entities` | `api/entities/index.php` |
| POST | `/api/v1/entities` | `POST /api/v1/entities` | `api/entities/store.php` |
| GET | `/api/v1/entities/{id}` | `GET /api/v1/entities/{id}` | `api/entities/show.php` |
| PUT | `/api/v1/entities/{id}` | `PUT /api/v1/entities/{id}` | `api/entities/update.php` |
| GET | `/api/v1/notifications` | `GET /api/v1/notifications` | `api/notifications/index.php` |
| POST | `/api/v1/notifications/read-all` | `POST /api/v1/notifications/read-all` | `api/notifications/read-all.php` |
| POST | `/api/v1/notifications/send` | `POST /api/v1/notifications/send` | `api/notifications/send.php` |
| POST | `/api/v1/notifications/broadcast` | `POST /api/v1/notifications/broadcast` | `api/notifications/broadcast.php` |
| PATCH | `/api/v1/notifications/{id}/read` | `PATCH /api/v1/notifications/{id}/read` | `api/notifications/mark-read.php` |
| PATCH | `/api/v1/notifications/{id}/archive` | `PATCH /api/v1/notifications/{id}/archive` | `api/notifications/archive.php` |
| GET | `/api/v1/activity-logs` | `GET /api/v1/activity-logs` | `api/activity-logs/index.php` |

## Contract notes

- The public `/api/v1` URLs are preserved.
- Route parameters are passed into the included endpoint as the `$routeParams` variable and as `$_SERVER['ROUTE_PARAMS']`.
- The runtime uses one explicit route registry and fixed include paths; no request-derived endpoint paths are allowed.
- The docs file `docs/02-api-conventions.md` describes additional report endpoints (`/api/v1/reports/...`), but these are not present in the current Slim route definitions and therefore are intentionally excluded from the implementation until they are regressed in the source backend.

## Runtime organization

- `config/init.php` is the runtime composition root: it loads configuration,
  registers dependencies, validates startup settings, creates PDO/Medoo, and
  returns the bootstrap dependencies.
- The manual vendor autoloader lives in `config/autoload.php`. JWT helpers,
  HTTP/JSON helpers, and startup logging live in their respective `services/`
  and `http/` files. Profile lookups and role resolution are owned by
  `repositories/ProfileRepository.php` and use Medoo.

## Phase 3 implementation

- Member profiles, families and memberships, trainers, admins, entities, and
  entity-member relations are implemented as direct endpoints under `api/`.
- Each endpoint owns its request flow in its registered script.
- `services/identity.php` contains shared auth/authorization, validation,
  address, query, pagination, transaction, and audit-log helpers only; it does
  not dispatch or implement endpoint operations.
- CRUD and relationship reads/writes use the manually installed Medoo 2.6.0
  instance from the bootstrap. Multi-step operations begin/commit/rollback
  through that instance's underlying PDO connection.
- All 26 registered Phase 3 handlers are present and wired. Before treating the
  phase exit checks as complete, run behavioral tests with PHP and a disposable
  database configured; the current development environment has no PHP runtime.

## Phase 4 implementation

- Programs, batches and batch members, sessions, attendance, and enrollments
  are implemented in their direct endpoint scripts under `api/`.
- Endpoint flows and response envelopes remain in those scripts;
  `services/academics.php` contains shared authorization, validation,
  hydration, pagination, transaction, and row-lock helpers.
- CRUD, list, relation, and audit-log operations use Medoo. Enrollment
  creation/cancellation and bulk attendance use transactions on the shared
  Medoo PDO connection.
- Enrollment creation locks the batch row before checking active capacity and
  duplicates; the SQLite path uses an immediate transaction. The schema's
  unique enrollment and batch-member constraints remain the final duplicate
  protection. Bulk attendance locks the session row and rechecks its lock state
  within the transaction.
- All 22 registered Phase 4 handlers are implemented. Behavioral checks for
  permissions, capacity, attendance locking, transaction rollback, and state
  changes still require PHP and a configured database; neither is available in
  the current development environment.

## Phase 6 implementation

- All 20 registered groups, invitations, notifications, and activity-log
  handlers are implemented in their direct endpoint scripts.
- Group visibility and membership access, admin controls, invitation sender
  ownership, notification ownership, and super-admin activity-log access follow
  the source policies. Mutation audit records are written with their database
  changes.
- Invitation codes are generated from cryptographic random bytes, checked
  against the unique-code column, and accepted under a transaction with an
  invitation row lock. Account creation, invitation status, audit log, and
  hashed refresh-token persistence roll back together. Passwords and token
  values are excluded from logs.
- Notification delivery is limited to persisted in-app/push/email records; no
  external email or SMS integration was added. Send and broadcast operations
  are transactional, and read/archive operations are constrained to the
  authenticated member.
- `tests/phase6-community-notifications.php` adds in-memory SQLite checks for
  group ownership/visibility, invitation duplicate and expiry rules, code
  generation, notification ownership, role and input validation, and rollback.
  Running it requires PHP with PDO_SQLITE; the current environment has no PHP
  executable, so behavioral execution remains outstanding.

## Phase 5 implementation

- All five payment routes are implemented in their direct endpoint scripts.
  Account-admin operations and primary-family ownership checks preserve the
  source role scope, filters, pagination, and response envelopes.
- Payment validation preserves the source payment types, methods, statuses,
  cash/reference requirements, class-fee enrollment requirement, and amount
  constraints. Completed payments cannot be edited; refunds are recorded as
  separate payments.
- Payment creation and updates use prepared PDO statements on the shared
  connection. Payment writes, enrollment payment-status recalculation, and
  activity logging are performed in the same transaction. The balance is
  recalculated from completed non-refund payments less completed refunds.
- `tests/phase5-payments.php` adds in-memory SQLite-backed checks for validation,
  authorization, family/enrollment consistency, completed-payment
  immutability, pagination, payment-status thresholds, refunds, and transaction
  rollback. It requires PHP with PDO_SQLITE; the current development environment
  does not have a PHP executable, so it could not be run here.

## Phase 7 parity and cutover

- The FastRoute registry and endpoint matrix contain 77 unique method/path
  registrations. Each has exactly one documented direct endpoint, and every
  registered endpoint file exists. The docs-only report paths remain excluded:
  they are not registered by the Slim source and were not added to the new API.
- The admin app retains its `/api/v1` environment base URLs and calls the
  existing auth paths through the shared API service. The parent app also
  retains `/api/v1` environment URLs; no active API service calls were found in
  the source tree inspected for this phase. No frontend files were changed.
- Apache rules preserve the single `/api/v1` front-controller mapping, deny
  direct PHP/config/library/service/storage/dotfile access, and stop
  front-controller rewrites before they can be reprocessed as direct script
  requests. CORS preflight is handled before database initialization. Startup
  errors now return a generic JSON message while details are sent to the
  server error log. The OPTIONS method comparison is explicit.
- `tests/phase7-parity.php` checks every registered method/path against
  FastRoute, the inventory, and an existing fixed endpoint file; it also
  checks query-string path parsing, path parameters, unknown routes, trailing
  slashes, unsupported methods, and deployment/front-controller guards.
- The Phase 7 source scan found no embedded database/JWT secret values. SQL
  value inputs use bound parameters; dynamic lock-table identifiers are
  allowlisted and the payment update fields are selected from fixed internal
  keys. Error details are returned as generic client errors and logged only to
  the server-side error log; production must keep that log and
  `storage/logs/` inaccessible over HTTP.
- The eight top-level library packages match the manually documented
  dependency set in `libraries/README.md`; no additional package roots were
  found.
- `apachectl -t` passed for the local Apache main configuration. It does not
  execute `.htaccess`; local Apache also has no loaded `mod_rewrite` or PHP
  module. The route parity test and Phase 1/5/6 PHP tests could not be run
  because PHP is unavailable; PDO SQLite feature checks and the disposable
  MySQL checks remain outstanding.
- Deployment variables, PHP/Apache requirements, log permissions, rewrite
  constraints, staging checklist, known operational differences, and Slim
  rollback procedure are documented in `DEPLOYMENT.md`.
- Production readiness is not claimed. Apache virtual-host behavior, live
  HTTP contract checks, PHP/database feature suites, admin integration, and
  staging acceptance remain cutover blockers. Keep Slim as the rollback
  target until those checks pass.
