# Non-Functional Requirements Baseline

Shared NFRs for the KCDF Parents platform. Each NFR is documented once and referenced from capabilities and application requirements; it is not copied into application folders. Audit and classification: [audit.md](./audit.md). Capability catalog: [../capabilities/index.md](../capabilities/index.md).

**Reading this document**

- **Part A** records behavior verified in source on 2026-11-09 (AS-IS baseline). Verification was by code inspection; behavior was not executed. A "verified" NFR still needs acceptance review before it is treated as an approved requirement.
- **Part B** lists **proposed** NFRs. They are recommendations, **not approved** and **not implemented** unless stated. Numeric targets are **TBD — requires approval**; none have been invented.
- IDs: `NFR-<CATEGORY>-<NNN>` with SEC, PRV, PRF, REL, AVL, MNT, CMP, ACC, OBS. IDs are never reused.
- Common fields for every NFR: **Decision status** is *Existing baseline* (Part A) or *Proposed — awaiting approval* (Part B).

Capability IDs refer to [../capabilities/index.md](../capabilities/index.md). Application requirement IDs refer to files under [../applications/](../applications/).

---

# Part A — Existing verified baseline

### NFR-SEC-001 — Password storage
- **Category / Scope:** Security / API (all portals)
- **Requirement:** Passwords shall be stored only as salted one-way hashes and checked with a constant-time verification function; plaintext passwords shall not be stored.
- **Rationale:** Limits damage from database disclosure.
- **Acceptance criteria:** The `user_logins.password_hash` column holds hashes only; creating or changing a password through any endpoint stores a hash; login succeeds only when verification passes.
- **Capabilities:** CAP-001, CAP-003
- **Related requirements:** [API-AUTH-*](../applications/api-backend/functional/authentication.md), [API-ADM-*](../applications/api-backend/functional/administrators.md)
- **Status / Origin:** Verified / verified implementation constraint
- **Evidence:** `api-backend-php/api/admins/store.php:67`, `api/members/login-store.php:73`, `api/members/login-update.php:53`, `api/invitations/accept.php:65`, `services/identity.php:409` (`password_verify`).

### NFR-SEC-002 — Token lifecycle
- **Category / Scope:** Security / API, Admin, Member
- **Requirement:** Access tokens shall be short-lived signed JWTs; refresh tokens shall be stored server-side, be revocable, and be revoked on logout and when used for refresh. A missing or weak signing secret shall prevent the API from starting.
- **Acceptance criteria:** Access TTL and refresh TTL come from configuration (defaults 900 s and 2 592 000 s; the approved values are **TBD — requires approval**); a revoked or expired refresh token returns 401; `JWT_SECRET` shorter than 32 characters fails startup validation.
- **Capabilities:** CAP-001
- **Related requirements:** `API-AUTH-003`, `API-AUTH-004`, `API-AUTH-005` in [authentication.md](../applications/api-backend/functional/authentication.md)
- **Status / Origin:** Verified / existing documented requirement plus implementation constraint
- **Evidence:** `api-backend-php/config/config.php:51-54`, `services/auth.php:20,37`, `api/auth/refresh.php:49-90`, `api/auth/logout.php`, table `refresh_tokens`.

### NFR-SEC-003 — Server-side authorization
- **Category / Scope:** Security / API (enforcement), Admin (route guards)
- **Requirement:** Authorization shall be enforced by the API for every protected operation (roles `super_admin`, `program_manager`, `accounts`, `readonly`, group roles, and ownership/family scope); client route guards are a usability aid only.
- **Acceptance criteria:** For each protected endpoint, a request from a user without the required role or scope is rejected, regardless of what the client UI shows. A role-by-operation matrix is **missing** (see NFR-SEC-011).
- **Capabilities:** CAP-001, CAP-002, CAP-008, CAP-009
- **Related requirements:** API functional documents; [admin technical authentication](../applications/admin-portal/technical/authentication.md)
- **Status / Origin:** Partial — `requirePermission`/`requireReportRole` appear in 47 places and group permission checks exist, but coverage of every endpoint was not individually verified / verified implementation constraint
- **Evidence:** `api-backend-php/services/identity.php:33`, `services/reports.php:7`, `services/community-notifications.php:70,215`; `kcdf-admin-app/src/app/core/guards/role.guard.ts`, `app.routes.ts`.

### NFR-SEC-004 — Input validation and injection protection
- **Category / Scope:** Security / API
- **Requirement:** Request input shall be validated before use; database access shall use parameterized queries; malformed JSON shall be rejected with a client error.
- **Acceptance criteria:** PDO is configured with native prepared statements and exception mode; malformed JSON returns `MALFORMED_JSON` (400); validation failures return field-level errors.
- **Capabilities:** all
- **Related requirements:** functional API requirements with validation criteria (e.g. `API-ENR-004`)
- **Status / Origin:** Verified (pattern); per-endpoint completeness not audited / verified implementation constraint
- **Evidence:** `api-backend-php/config/init.php` (`ATTR_EMULATE_PREPARES => false`, Medoo), `http/helpers.php` (`getJsonBody`), `api/admins/store.php:15-37`.

### NFR-SEC-005 — Safe error handling and production configuration
- **Category / Scope:** Security / API
- **Requirement:** Unexpected failures shall return a generic error without internal details and shall be logged server-side; production start-up shall fail when database configuration is incomplete.
- **Acceptance criteria:** A forced database error returns `INTERNAL_ERROR` (500) without SQL text; duplicate-key errors return `DUPLICATE_ENTRY` (409); `APP_ENV=production` with missing DB name/user throws at start-up.
- **Capabilities:** all
- **Status / Origin:** Verified / verified implementation constraint
- **Evidence:** `api-backend-php/http/helpers.php` (`handleException`), `config/init.php:12-17`. Note: `APP_DEBUG` defaults to `true` (`config/config.php:38`) — see NFR-SEC-012.

### NFR-SEC-006 — Cross-origin access allow-list
- **Category / Scope:** Security / API
- **Requirement:** Browser cross-origin access shall be limited to origins configured in `CORS_ALLOWED_ORIGINS`.
- **Acceptance criteria:** A request from a non-listed origin does not receive an `Access-Control-Allow-Origin` header for that origin.
- **Capabilities:** CAP-001
- **Status / Origin:** Verified (header logic inspected, not exercised) / verified implementation constraint
- **Evidence:** `api-backend-php/config/config.php:32`, `api/index.php:20-30`. Default allows `localhost:4200` and `localhost:8100`; production origins are deployment configuration.

### NFR-REL-001 — Transactional consistency of multi-step writes
- **Category / Scope:** Reliability and data integrity / API
- **Requirement:** Operations that change more than one record (identity creation, enrollment, payment, group/notification/invitation flows, refresh-token rotation) shall commit atomically or roll back entirely.
- **Acceptance criteria:** Forcing a failure after the first write of such an operation leaves no partial records.
- **Capabilities:** CAP-002, CAP-003, CAP-005, CAP-007, CAP-008, CAP-010
- **Related requirements:** `API-ENR-002`, `API-PAY-*`, `API-FAM-*`
- **Status / Origin:** Verified for the listed code paths; not every multi-write endpoint was checked / verified implementation constraint
- **Evidence:** `api-backend-php/services/identity.php:66`, `services/academics.php:302`, `services/payments.php:180`, `services/community-notifications.php:36`, `api/auth/refresh.php:49`.

### NFR-REL-002 — Concurrency control on contended records
- **Category / Scope:** Reliability / API (MySQL)
- **Requirement:** Capacity-, vote-, registration- and lock-sensitive operations shall serialize on the affected row so concurrent requests cannot violate the rule.
- **Acceptance criteria:** Two simultaneous enrollments for the last seat result in one success; two simultaneous accepts of one invitation result in one success.
- **Capabilities:** CAP-005, CAP-006, CAP-003, CAP-009
- **Status / Origin:** Partial — `SELECT … FOR UPDATE` is used, but it is skipped on SQLite and no concurrency test exists / verified implementation constraint
- **Evidence:** `api-backend-php/services/academics.php:326,337`, `api/groups/resources.php:283,364,527`, `services/community-notifications.php:225,499`.

### NFR-REL-003 — Duplicate protection
- **Category / Scope:** Reliability / API, database
- **Requirement:** Duplicate records shall be prevented by application checks and database constraints, and reported as a conflict.
- **Acceptance criteria:** A violating insert returns 409 `DUPLICATE_ENTRY`.
- **Capabilities:** CAP-002, CAP-005, CAP-008
- **Related requirements:** `API-ENR-002`, `API-GRP-016`
- **Status / Origin:** Verified (mapping of 23000 errors) / verified implementation constraint
- **Evidence:** `api-backend-php/http/helpers.php` (`handleException`), `database/schema.sql` (35 `FOREIGN KEY` clauses and unique indexes).

### NFR-PRF-001 — Bounded list responses
- **Category / Scope:** Performance / API
- **Requirement:** List endpoints shall paginate and clamp page size (default 20, maximum 100).
- **Acceptance criteria:** `per_page=1000` returns at most 100 rows.
- **Capabilities:** CAP-005, CAP-008, CAP-009, CAP-011
- **Related requirements:** `API-ENR-001` (page size bounded)
- **Status / Origin:** Partial — verified in enrollments, groups and group content; other list endpoints not individually checked / existing documented requirement
- **Evidence:** `api-backend-php/api/enrollments/index.php:26`, `api/groups/index.php:18`, `api/groups/resources.php:116`.

### NFR-OBS-002 — Data-change audit trail
- **Category / Scope:** Observability / API, Admin
- **Requirement:** Administrative and member-initiated data changes shall be recorded with actor, action, entity, and old/new values, and be viewable by authorized administrators.
- **Acceptance criteria:** After a create/update/cancel operation, an `activity_logs` row exists with actor and values; the audit-log screen lists it.
- **Capabilities:** CAP-011, CAP-007, CAP-005
- **Related requirements:** [API-LOG-*](../applications/api-backend/functional/activity-logs.md), [ADM-LOG-*](../applications/admin-portal/functional/audit-logs.md)
- **Status / Origin:** Verified — about 45 write handlers call `logActivity`; some record a `null` actor (`api/enrollments/cancel.php:27`) / existing documented requirement
- **Evidence:** `api-backend-php/services/identity.php:51`, `database/schema.sql:565`.

### NFR-OBS-003 — Server-side error logging
- **Category / Scope:** Observability / API
- **Requirement:** Unexpected API and database failures shall be written to a server log.
- **Acceptance criteria:** A forced 500 produces a log entry containing the cause.
- **Capabilities:** all
- **Status / Origin:** Verified (`error_log` in the exception handler; a Monolog logger service exists) / verified implementation constraint
- **Evidence:** `api-backend-php/http/helpers.php`, `services/logger.php`.

### NFR-MNT-001 — Versioned database migrations
- **Category / Scope:** Maintainability / database
- **Requirement:** Schema changes after the baseline schema shall be delivered as dated SQL migration files.
- **Acceptance criteria:** Each post-baseline schema change has a file under `database/migrations/`.
- **Capabilities:** all
- **Status / Origin:** Verified (6 migrations present; no automated runner or ordering table found) / verified implementation constraint
- **Evidence:** `api-backend-php/database/migrations/`, `api-backend-php/MIGRATION_INVENTORY.md`.

### NFR-MNT-002 — Backend test scripts and deployment procedure
- **Category / Scope:** Maintainability / API, deployment
- **Requirement:** The backend shall have repeatable verification scripts and a documented deployment/rollback procedure.
- **Acceptance criteria:** The scripts in `tests/` run against a disposable database as documented in `DEPLOYMENT.md`; deployment is serialized by a lock.
- **Capabilities:** all
- **Status / Origin:** Partial — 7 PHP phase scripts exist; admin app has 0 spec files and member app 2; no automated pipeline found / verified implementation constraint
- **Evidence:** `api-backend-php/tests/`, `api-backend-php/DEPLOYMENT.md` (sections "Cutover and rollback", test steps), `deployment/scripts/deploy.sh:8-9`; `kcdf-parents-app/src/app/home/home.page.spec.ts`, `app.component.spec.ts`.

---

# Part B — Proposed NFRs (not approved)

Each entry: **Status:** not implemented unless noted; **Origin:** proposed improvement; **Decision status:** proposed — awaiting approval. Targets marked TBD must be set by the product owner.

### NFR-SEC-007 — Login abuse protection
- **Scope:** API, both login endpoints
- **Requirement:** The system shall limit repeated failed logins per account and/or source using a mechanism and thresholds to be approved.
- **Rationale:** No rate limiting, lockout or attempt counter was found (`api-backend-php/api/auth/login.php`, `admin-login.php`, schema).
- **Acceptance criteria:** After the approved number of failures within the approved window, further attempts are rejected for the approved period. Thresholds: **TBD — requires approval**.
- **Capabilities / related:** CAP-001; `API-AUTH-*`
- **Implementation status:** Not implemented. **Business decision needed.**

### NFR-SEC-008 — Consistent password policy
- **Scope:** API
- **Requirement:** One password policy shall apply to all account types.
- **Rationale:** Administrator creation enforces a 12-character minimum (`api/admins/store.php:35`); an equivalent rule was not found for member logins or invitation acceptance.
- **Acceptance criteria:** Creating or changing a password for any account type below the approved minimum is rejected. Minimum and complexity: **TBD — requires approval**.
- **Capabilities / related:** CAP-001, CAP-003; `API-ADM-*`, `API-INV-*`
- **Implementation status:** Partial. **Business decision needed.**

### NFR-SEC-009 — Client token storage
- **Scope:** Admin, Member
- **Requirement:** The approved storage approach for access and refresh tokens in the browser/app shall be documented and reviewed.
- **Rationale:** Both apps keep tokens in `localStorage` (`kcdf-admin-app/src/app/core/store/auth.store.ts:85`, `kcdf-parents-app/src/app/core/member-auth.service.ts:204`). Whether this is acceptable for admin and member data is a risk decision, not a confirmed defect.
- **Acceptance criteria:** A decision record exists; if changed, tokens are not readable by page script. **Requires business/security decision.**
- **Capabilities / related:** CAP-001; technical authentication documents
- **Implementation status:** Current behavior documented only here.

### NFR-SEC-010 — HTTP security headers and transport
- **Scope:** API, hosting configuration
- **Requirement:** The hosted API shall be served over HTTPS and send security headers approved by the project.
- **Rationale:** No header configuration is in the repository; hosting configuration may exist outside it.
- **Acceptance criteria:** A scan of the deployed environment shows the approved headers and HTTPS-only access. **Unknown** — needs deployment inspection.
- **Implementation status:** Unknown.

### NFR-SEC-011 — Role and permission matrix
- **Scope:** System-wide
- **Requirement:** A role × operation matrix shall be maintained and each protected API operation shall have an authorization test.
- **Rationale:** Roles exist in code but no requirement lists them (supports NFR-SEC-003).
- **Acceptance criteria:** Matrix reviewed by the product owner; each cell with "denied" has a passing negative test.
- **Capabilities / related:** CAP-001 and all; NFR-SEC-003
- **Implementation status:** Not documented; enforcement partial.

### NFR-SEC-012 — Debug off by default in production
- **Scope:** API configuration
- **Requirement:** Debug/query logging shall be disabled when `APP_ENV=production`.
- **Rationale:** `APP_DEBUG` defaults to `true` and enables Medoo logging (`config/config.php:38`, `config/init.php:52`). Whether production sets it to false is not visible in the repository.
- **Acceptance criteria:** The production configuration template sets `APP_DEBUG=false`, or startup refuses debug in production. **Unknown** for the live deployment.
- **Implementation status:** Partial.

### NFR-PRV-001 — Personal data of minors and consent
- **Scope:** System-wide
- **Requirement:** Handling, consent, retention, export and deletion of family and children's personal data shall be defined.
- **Rationale:** The platform stores member and family data (`member_profiles`, `addresses`, `family_members`); no policy requirement exists. Raised in [analysis/04](../../analysis/04-requirements-and-sdlc-review.md).
- **Acceptance criteria:** **TBD — requires business and legal decision.**
- **Implementation status:** Not documented.

### NFR-PRF-002 — Performance targets
- **Scope:** API, Admin, Member
- **Requirement:** Under an approved reference workload, the listed operations shall meet approved response-time thresholds.
- **Rationale:** No targets, workload or concurrency expectations exist. Recommendation only: start by measuring list endpoints (families, enrollments, reports) before choosing numbers.
- **Acceptance criteria:** **TBD — requires approval** (operations, workload, thresholds).
- **Implementation status:** Not measured.

### NFR-PRF-003 — Query support for common filters
- **Scope:** Database
- **Requirement:** Columns used for filtering and joins on large lists shall be reviewed for indexes.
- **Rationale:** Activity logs have indexes (`schema.sql:575-577`); other tables were not reviewed.
- **Acceptance criteria:** Query-plan review of agreed list/report queries with agreed data volume. Volume: **TBD**.
- **Implementation status:** Unknown.

### NFR-REL-004 — Notification delivery semantics
- **Scope:** API
- **Requirement:** The delivery channels, failure handling and retry rules for `in_app`, `push` and `email` notifications shall be defined.
- **Rationale:** Types exist in code and schema but no push/e-mail delivery integration was found.
- **Acceptance criteria:** **Requires business decision** on channels and retries.
- **Capabilities / related:** CAP-010; `API-NOT-*`, [payments-and-notifications workflow](../workflows/payments-and-notifications.md)
- **Implementation status:** Partial (in-app only verified).

### NFR-AVL-001 — Availability, backup and recovery objectives
- **Scope:** Hosting, database
- **Requirement:** Availability expectation, database and file backup schedule, retention, restore test, and recovery objectives (RPO/RTO) shall be defined.
- **Rationale:** `DEPLOYMENT.md` says to take a backup before migrations and describes cutover/rollback, but no schedule, retention or restore procedure exists.
- **Acceptance criteria:** **TBD — requires approval**; a restore drill passes within the approved objectives.
- **Implementation status:** Documented only (manual backups before changes).

### NFR-MNT-003 — Automated regression tests for the front ends
- **Scope:** Admin, Member
- **Requirement:** Critical flows (login/refresh, role guards) shall have automated tests.
- **Rationale:** Admin app has no spec files; member app has 2 (generated-style).
- **Acceptance criteria:** Agreed flows covered; scope **TBD**. Do not add frameworks beyond the existing Angular/Karma tooling without approval.
- **Implementation status:** Partial.

### NFR-CMP-001 — Supported browsers and devices
- **Scope:** Admin, Member
- **Requirement:** Supported browsers, Android/iOS versions and screen sizes shall be listed.
- **Rationale:** Not documented; the member app includes Capacitor configuration (`kcdf-parents-app/capacitor.config.ts`) but target platforms/versions are unspecified.
- **Acceptance criteria:** **TBD — requires approval.**
- **Implementation status:** Unknown.

### NFR-ACC-001 — Accessibility baseline
- **Scope:** Admin, Member
- **Requirement:** Forms shall have labels, readable validation messages, keyboard operation, and loading/empty/error states, to an approved standard.
- **Rationale:** ARIA attributes appear in 83 front-end source files; conformance is unverified. Shared UI specs exist (e.g. [loading-overlay](../applications/admin-portal/ui/shared/loading-overlay.md), [toast-region](../applications/admin-portal/ui/shared/toast-region.md)) but no accessibility target.
- **Acceptance criteria:** Conformance level **TBD — requires approval**; checked by an agreed audit.
- **Implementation status:** Unknown.

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
