# Authentication and access — technical specification

**Evidence status:** Static source trace only. This describes current code, not approved identity policy or verified runtime behavior. Phase 05 findings remain proposals.

## Requirement-to-source behavior

| ID | Current source behavior and interface | Status / boundary |
|---|---|---|
| AUTH-001 | `POST /api/v1/auth/admin/login` → `api-backend-php/api/auth/admin-login.php` → `services/identity.php::authenticatePortalLogin()` and `issueLoginTokens()`. Username/password, active login, admin account type and eligible role are checked; response contains issued access/refresh tokens and identity context. Admin client: `kcdf-admin-app/src/app/core/services/auth.service.ts`, login page. | **Observed**; no runtime verification. |
| AUTH-002 | `POST /api/v1/auth/login` → `api-backend-php/api/auth/login.php`; shared identity authentication and token issuance. Member client: `kcdf-parents-app/src/app/core/member-auth.service.ts`, `login/login.page.ts`. | **Partial**: existing profile linkage is checked, but profile active/suspended state is not established by the login helper. |
| AUTH-003 | Bearer extraction/JWT checks and portal role filtering reside in `services/auth.php::extractBearerToken`, `decodeJwtToken`, `checkAuth` and `services/identity.php::getUserRoles`. Checks are invoked by individual handlers; `api/index.php` has no global auth gate. Admin guard/interceptor and member auth service are in the client paths above. | **Observed helper behavior**; route-by-route runtime enforcement is not claimed. |
| AUTH-004 | `POST /api/v1/auth/refresh` → `api/auth/refresh.php`; refresh-token hash, state, expiry and account/profile context use `services/identity.php::issueLoginTokens`; `refresh_tokens` in `database/schema.sql`. Admin `error.interceptor.ts` has refresh/retry; member service stores a refresh token but has no automatic refresh/retry method. | **Partial**: source transaction does not establish race-safe single-use rotation. |
| AUTH-005 | `POST /api/v1/auth/logout` → `api/auth/logout.php`, checks access context and refresh JWT scope, then attempts to revoke a stored row. | **Contradicted**: no eligible matching stored row/update is required before success. This is a Phase 05 mismatch, not a desired contract or fix decision. |
| AUTH-006 | `/api/v1/admins` GET/POST and `/{id}` GET/PUT, plus POST alias `/admins/login-accounts` → `api/admins/{index,store,show,update}.php`. All require `admin_super`. List is paginated/filterable; create validates username, role, optional profile link and 12-character-minimum password; update accepts role/status/display name. Successful create/update logs activity. | **Observed API-only capability**; no app management consumer was found. Admin handler behavior was traced for this requirement; no runtime verification. |
| AUTH-007 | `GET /api/v1/auth/me` → `api/auth/me.php`; checks active stored login, account type and unchanged profile link, and requires a resolvable profile for member accounts. Returns account/profile identity and role context. Admin `AuthService.fetchMe()` consumes it. | **Observed**; no member client consumer or runtime verification. |

## Interfaces and data

- Route definitions: `api-backend-php/api/routes.php` (`/auth/login`, `/auth/admin/login`, `/auth/refresh`, `/auth/logout`, `/auth/me`, `/admins`, `/admins/login-accounts`, `/admins/{id}`).
- Identity persistence: `api-backend-php/database/schema.sql` (`user_logins`, `member_profiles`, `refresh_tokens`); token payload and permission helpers: `api-backend-php/services/auth.php`, `services/identity.php`.
- OpenAPI paths: `api-backend-php/swagger/openapi.json` documents auth operations. The two login operations are not bearer-secured there; refresh/logout/me are documented with bearer security. Detailed route parity is in `docs/architecture/api-contract-audit.md`.
- `/admins` management has no Angular/Ionic consumer in the bounded review. AUTH-006 describes only the observed API behavior and does not infer a desired management UI or external workflow.

## Unresolved matters

Keep AUTH-002 profile eligibility, AUTH-004 concurrent refresh semantics, AUTH-005 stored-token-row behavior, `admin_readonly` authorization, self-deactivation behavior, and access-token behavior after deactivation explicitly unresolved. AUTH-006/007 capture observed API capabilities, not user-approved product policy. Consult `baseline-workflow/analysis/requirements-accuracy-audit.md`.
