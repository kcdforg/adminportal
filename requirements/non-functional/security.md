# Security — Non-Functional Requirements

Original requirement text and evidence are preserved below. Existing-baseline entries are not automatically approved; proposed entries remain unapproved.

## Existing observed baseline — pending owner acceptance

### NFR-SEC-001 — Password storage
- **Category / Scope:** Security / API (all portals)
- **Requirement:** Passwords shall be stored only as salted one-way hashes and checked with a constant-time verification function; plaintext passwords shall not be stored.
- **Rationale:** Limits damage from database disclosure.
- **Acceptance criteria:** The `user_logins.password_hash` column holds hashes only; creating or changing a password through any endpoint stores a hash; login succeeds only when verification passes.
- **Capabilities:** CAP-001, CAP-003
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** [API-AUTH-*](../applications/api-backend/functional/authentication.md), [API-ADM-*](../applications/api-backend/functional/administrators.md)
- **Status / Origin:** Verified / verified implementation constraint
- **Evidence:** `api-backend-php/api/admins/store.php:67`, `api/members/login-store.php:73`, `api/members/login-update.php:53`, `api/invitations/accept.php:65`, `services/identity.php:409` (`password_verify`).

### NFR-SEC-002 — Token lifecycle
- **Category / Scope:** Security / API, Admin, Member
- **Requirement:** Access tokens shall be short-lived signed JWTs; refresh tokens shall be stored server-side, be revocable, and be revoked on logout and when used for refresh. A missing or weak signing secret shall prevent the API from starting.
- **Acceptance criteria:** Access TTL and refresh TTL come from configuration (defaults 900 s and 2 592 000 s; the approved values are **TBD — requires approval**); a revoked or expired refresh token returns 401; `JWT_SECRET` shorter than 32 characters fails startup validation.
- **Capabilities:** CAP-001
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** `API-AUTH-003`, `API-AUTH-004`, `API-AUTH-005` in [authentication.md](../applications/api-backend/functional/authentication.md)
- **Status / Origin:** Verified / existing documented requirement plus implementation constraint
- **Evidence:** `api-backend-php/config/config.php:51-54`, `services/auth.php:20,37`, `api/auth/refresh.php:49-90`, `api/auth/logout.php`, table `refresh_tokens`.

### NFR-SEC-003 — Server-side authorization
- **Category / Scope:** Security / API (enforcement), Admin (route guards)
- **Requirement:** Authorization shall be enforced by the API for every protected operation (roles `super_admin`, `program_manager`, `accounts`, `readonly`, group roles, and ownership/family scope); client route guards are a usability aid only.
- **Acceptance criteria:** For each protected endpoint, a request from a user without the required role or scope is rejected, regardless of what the client UI shows. A role-by-operation matrix is **missing** (see NFR-SEC-011).
- **Capabilities:** CAP-001, CAP-002, CAP-008, CAP-009
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** API functional documents; [admin technical authentication](../applications/admin-portal/technical/authentication.md)
- **Status / Origin:** Partial — `requirePermission`/`requireReportRole` appear in 47 places and group permission checks exist, but coverage of every endpoint was not individually verified / verified implementation constraint
- **Evidence:** `api-backend-php/services/identity.php:33`, `services/reports.php:7`, `services/community-notifications.php:70,215`; `kcdf-admin-app/src/app/core/guards/role.guard.ts`, `app.routes.ts`.

### NFR-SEC-004 — Input validation and injection protection
- **Category / Scope:** Security / API
- **Requirement:** Request input shall be validated before use; database access shall use parameterized queries; malformed JSON shall be rejected with a client error.
- **Acceptance criteria:** PDO is configured with native prepared statements and exception mode; malformed JSON returns `MALFORMED_JSON` (400); validation failures return field-level errors.
- **Capabilities:** all
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** functional API requirements with validation criteria (e.g. `API-ENR-004`)
- **Status / Origin:** Verified (pattern); per-endpoint completeness not audited / verified implementation constraint
- **Evidence:** `api-backend-php/config/init.php` (`ATTR_EMULATE_PREPARES => false`, Medoo), `http/helpers.php` (`getJsonBody`), `api/admins/store.php:15-37`.

### NFR-SEC-005 — Safe error handling and production configuration
- **Category / Scope:** Security / API
- **Requirement:** Unexpected failures shall return a generic error without internal details and shall be logged server-side; production start-up shall fail when database configuration is incomplete.
- **Acceptance criteria:** A forced database error returns `INTERNAL_ERROR` (500) without SQL text; duplicate-key errors return `DUPLICATE_ENTRY` (409); `APP_ENV=production` with missing DB name/user throws at start-up.
- **Capabilities:** all
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Status / Origin:** Verified / verified implementation constraint
- **Evidence:** `api-backend-php/http/helpers.php` (`handleException`), `config/init.php:12-17`. Note: `APP_DEBUG` defaults to `true` (`config/config.php:38`) — see NFR-SEC-012.

### NFR-SEC-006 — Cross-origin access allow-list
- **Category / Scope:** Security / API
- **Requirement:** Browser cross-origin access shall be limited to origins configured in `CORS_ALLOWED_ORIGINS`.
- **Acceptance criteria:** A request from a non-listed origin does not receive an `Access-Control-Allow-Origin` header for that origin.
- **Capabilities:** CAP-001
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Status / Origin:** Verified (header logic inspected, not exercised) / verified implementation constraint
- **Evidence:** `api-backend-php/config/config.php:32`, `api/index.php:20-30`. Default allows `localhost:4200` and `localhost:8100`; production origins are deployment configuration.

## Proposed — not approved

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
