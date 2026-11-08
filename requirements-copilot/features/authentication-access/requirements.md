# Authentication and access requirements

**Status:** Provisional as-is reconstruction; source-observed backend behavior. No runtime or test verification.

## Requirements

### AUTH-001 — Admin portal sign-in

- **Actor:** Admin account holder.
- **Requirement:** The API shall accept username/password through `POST /api/v1/auth/admin/login` and authenticate an active login whose account type is `admin`.
- **Acceptance criteria (source-derived):**
  - Missing username or password returns a validation error.
  - Invalid credentials, inactive login, or wrong portal/account type returns an authentication error.
  - Successful authentication returns an access token, refresh token and account/profile information.
- **Evidence:** `api/routes.php`; `api/auth/admin-login.php`; `services/identity.php` (`authenticatePortalLogin`, `issueLoginTokens`).
- **Status:** Observed in backend source; admin client uses the route. Runtime result unverified.

### AUTH-002 — Member portal sign-in

- **Actor:** Member account holder.
- **Requirement:** The API shall accept username/password through `POST /api/v1/auth/login` and authenticate an active login whose account type is `member` and whose member profile is valid.
- **Acceptance criteria (source-derived):**
  - Missing credentials return a validation error.
  - Invalid credentials, inactive login, wrong portal type or invalid member-profile linkage are rejected.
  - Successful authentication returns access/refresh tokens and member profile context.
- **Evidence:** `api/routes.php`; `api/auth/login.php`; `services/identity.php` (`authenticatePortalLogin`, `issueLoginTokens`).
- **Status:** Observed in backend source; member client calls the route. Runtime result unverified.

### AUTH-003 — Access-token validation and portal separation

- **Actor:** Authenticated API caller.
- **Requirement:** A protected handler that invokes `checkAuth()` shall accept a valid bearer access token with supported account type, matching portal, positive login identifier and, for member accounts, a positive profile identifier; refresh tokens shall not be accepted as ordinary access tokens.
- **Acceptance criteria (source-derived):**
  - Missing, malformed, invalid, expired or wrong-type tokens are rejected as unauthenticated.
  - Portal/account mismatch or malformed identity claims are rejected.
  - Roles are filtered to the matching admin/member portal by the identity helpers.
- **Evidence:** `services/auth.php` (`extractBearerToken`, `decodeJwtToken`, `checkAuth`); `services/identity.php` (`getUserRoles`).
- **Status:** Observed helper behavior; not every route was runtime-tested, and the front controller has no global auth gate.

### AUTH-004 — Refresh-token rotation

- **Actor:** Admin or member account with a refresh token.
- **Requirement:** `POST /api/v1/auth/refresh` shall validate an eligible refresh token against its stored hash and current login/profile state, revoke the presented token and issue replacement tokens within the handler's transaction.
- **Acceptance criteria (source-derived):**
  - Missing, wrong-scope, expired, revoked or invalidated refresh tokens are rejected.
  - On success the presented token is revoked and replacement tokens are returned.
- **Evidence:** `api/auth/refresh.php`; `services/identity.php` (`issueLoginTokens`); `schema.sql` (`refresh_tokens`).
- **Status:** Observed in backend source. The member app stores refresh tokens but no automatic refresh method was found; admin has an automatic 401 refresh/retry path.

### AUTH-005 — Logout revokes refresh token

- **Actor:** Authenticated admin or member.
- **Requirement:** `POST /api/v1/auth/logout` shall revoke the supplied refresh token when it matches the caller's login and portal.
- **Acceptance criteria (source-derived):**
  - The request requires valid access-token context and a matching refresh-token record.
  - A matching token is revoked; mismatched tokens are not accepted as the caller's session.
- **Evidence:** `api/auth/logout.php`; `api/routes.php`; `schema.sql` (`refresh_tokens`).
- **Status:** Observed in backend source; browser/session behavior was not executed.

### AUTH-006 — Manage admin login accounts

- **Actor:** `admin_super`.
- **Requirement:** The API shall list, create, read and update admin login accounts through `/api/v1/admins`; the create operation is also registered at `/api/v1/admins/login-accounts`.
- **Acceptance criteria (source-derived):**
  - Every operation requires a valid access token with the `admin_super` role.
  - Listing supports pagination and filters for account type, active state and role.
  - Creation requires a valid username, one of the source-supported admin roles, and a password of at least 12 characters; an optional profile link must identify an existing profile and must not already have a login.
  - Duplicate usernames and duplicate profile-login links are rejected; successful creation creates an active admin login and records activity.
  - Read returns not found for an unknown account.
  - Update accepts only supported role, active/inactive status and display-name fields; invalid values are rejected and a successful update records activity.
- **Evidence:** `api/routes.php`; `api/admins/{index,store,show,update}.php`; `services/identity.php` (`getAdmin`, activity and identity helpers); `schema.sql` (`user_logins`).
- **Status:** Backend API behavior observed. No Angular/Ionic admin-account management consumer was found; an external consumer or intended UI is unconfirmed.

### AUTH-007 — Read current authenticated account context

- **Actor:** Authenticated admin or member account.
- **Requirement:** `GET /api/v1/auth/me` shall return the current access token's active login and associated account/profile context.
- **Acceptance criteria (source-derived):**
  - The request requires a valid access token and a positive login ID.
  - The stored login must still exist, be active, match the token account type, and retain the same profile link.
  - A member login with no resolvable profile is rejected; an admin login may be standalone with no linked member profile.
  - The response includes login/profile identifiers, username/display name, email and name fields, roles, account type/role, portal and account timestamps/status.
- **Evidence:** `api/routes.php`; `api/auth/me.php`; `services/identity.php` (`getProfileById`, `resolveLoginDisplayName`, `getUserRoles`); admin `AuthService.fetchMe()`.
- **Status:** Backend behavior observed and consumed by the admin client; member-client use was not found. Response behavior was not runtime-tested.

## Unresolved policy

The role `admin_readonly` is included in some server `isAdmin()` access checks, including a member-edit path; intended role policy is unknown. The behavior of an unexpired access token after account deactivation is also unresolved for handlers that rely only on `checkAuth()`. Do not treat either behavior as approved policy.
