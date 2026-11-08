# Authentication

This document describes the current backend authentication requirements in plain language. It covers member sign-in, admin sign-in, session protection, token refresh, logout, and the person currently signed in.

## Requirement summary

- Member and admin users must authenticate through separate portal-specific login flows.
- Protected API routes must reject missing, malformed, expired, or invalid tokens.
- Refresh tokens must be validated and rotated in a secure way.
- A logged-out user must be able to revoke the matching refresh token.
- The current-user endpoint must return the authenticated identity and profile context.

## Status

This is a working requirement draft intended for planning and review. It should be validated against live runtime behavior before being treated as a final production requirement.

### API-AUTH-003 — JWT access token verification

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-003`
**Actor:** Authenticated API users.

**Requirement:** Protected operations shall validate a bearer access token before allowing the request. Token identity, expiry, account type and portal claims must be valid.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `function checkAuth(?string $authorizationHeader = null, bool $allowRefreshToken = false): array` at `api-backend-php/services/auth.php:149`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (strlen($secret) < 32) {` at `api-backend-php/services/auth.php:20`. Runtime outcome not established.

**Source references:**
- `api-backend-php/services/auth.php` (`extractBearerToken`, `decodeJwtToken`, `checkAuth`).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-AUTH-004 — Refresh access credentials

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-004`
**Actor:** Signed-in admin and member users.

**Requirement:** A signed-in client submits an eligible refresh token to obtain replacement credentials. Expired, revoked and mismatched tokens are rejected.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$claims = checkAuth('Bearer ' . $refreshToken, true);` at `api-backend-php/api/auth/refresh.php:16`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `function getUserRoles(array $jwt): array` at `api-backend-php/services/identity.php:5`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!isset($roleMap[$role])) {` at `api-backend-php/services/identity.php:302`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [` at `api-backend-php/api/auth/refresh.php:10`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/auth/refresh.php`
- `api-backend-php/api/auth/logout.php`
- `api-backend-php/services/identity.php` (`issueLoginTokens`)
- `api-backend-php/services/auth.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-AUTH-005 — Logout and refresh-token revocation

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-005`
**Actor:** Signed-in admin and member users.

**Requirement:** A client submits a refresh token to log out. The backend attempts to revoke the token; its current success response does not establish that a database row was changed.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$claims = checkAuth('Bearer ' . $refreshToken, true);` at `api-backend-php/api/auth/refresh.php:16`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `function getUserRoles(array $jwt): array` at `api-backend-php/services/identity.php:5`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!isset($roleMap[$role])) {` at `api-backend-php/services/identity.php:302`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [` at `api-backend-php/api/auth/refresh.php:10`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/auth/refresh.php`
- `api-backend-php/api/auth/logout.php`
- `api-backend-php/services/identity.php` (`issueLoginTokens`)
- `api-backend-php/services/auth.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-AUTH-006 — Current authenticated account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-006`
**Actor:** Signed-in users.

**Requirement:** The current-account operation shall return the authenticated login and related profile context.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$payload = checkAuth();` at `api-backend-php/api/auth/me.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `$roles = getUserRoles($payload);` at `api-backend-php/api/auth/me.php:34`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);` at `api-backend-php/api/auth/me.php:10`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$login = $database->get('user_logins', [` at `api-backend-php/api/auth/me.php:18`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/auth/me.php`
- `kcdf-admin-app/src/app/core/services/auth.service.ts` (`fetchMe`).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).


## Additional source-reconciled atomic requirements (2026-11-08)

The statements below are verified against the cited PHP handler branches only. They do not establish end-to-end operation.

### API-AUTH-007 — Require username for member login

**Requirement:** The member login endpoint rejects a missing or blank username.

**Source:** `api-backend-php/api/auth/login.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-008 — Require password for member login

**Requirement:** The member login endpoint rejects a missing password.

**Source:** `api-backend-php/api/auth/login.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-009 — Restrict member login to member portal

**Requirement:** The member login endpoint calls portal authentication with the member portal type.

**Source:** `api-backend-php/api/auth/login.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-010 — Restrict admin login to admin portal

**Requirement:** The admin login endpoint calls portal authentication with the admin portal type.

**Source:** `api-backend-php/api/auth/admin-login.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-011 — Require refresh token for credential renewal

**Requirement:** The refresh endpoint rejects a missing or blank refresh_token.

**Source:** `api-backend-php/api/auth/refresh.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-012 — Reject revoked refresh tokens

**Requirement:** The refresh endpoint rejects refresh tokens without a matching unrevoked, unexpired stored token.

**Source:** `api-backend-php/api/auth/refresh.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-013 — Rotate refresh token on renewal

**Requirement:** The refresh endpoint revokes the matched stored token and issues replacement credentials within a transaction.

**Source:** `api-backend-php/api/auth/refresh.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-014 — Check account activity on refresh

**Requirement:** The refresh endpoint rejects a login that is no longer active.

**Source:** `api-backend-php/api/auth/refresh.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-015 — Require matching login on logout

**Requirement:** The logout endpoint rejects a refresh token whose login or portal does not match the current access token.

**Source:** `api-backend-php/api/auth/logout.php`

**Verification:** Source branch inspected; runtime untested.

### API-AUTH-016 — Return authenticated login profile

**Requirement:** The current-user endpoint returns login identity and portal information for a valid active login.

**Source:** `api-backend-php/api/auth/me.php`

**Verification:** Source branch inspected; runtime untested.