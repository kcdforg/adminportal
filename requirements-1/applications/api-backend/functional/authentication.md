# Authentication

This document describes the current backend authentication requirements in plain language. It covers member sign-in, admin sign-in, session protection, token refresh, logout, and the person currently signed in.

## Requirement summary

- Member and admin users must authenticate through separate portal-specific login flows.
- Protected API routes must reject missing, malformed, expired, or invalid tokens.
- Refresh tokens must be validated and rotated in a secure way.
- A logged-out user must be able to revoke the matching refresh token.
- The current-user operation must return the authenticated identity and profile context.

## Status

This is a working requirement draft intended for planning and review. It should be validated against live runtime behavior before being treated as a final production requirement.

### API-AUTH-003 — JWT access token verification

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-003`
**Actor:** Authenticated API users.

**Requirement:** Protected operations shall validate a bearer access token before allowing the request. Token identity, expiry, account type and portal claims must be valid.

---

### API-AUTH-004 — Refresh access credentials

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-004`
**Actor:** Signed-in admin and member users.

**Requirement:** A signed-in client submits an eligible refresh token to obtain replacement credentials. Expired, revoked and mismatched tokens are rejected.

---

### API-AUTH-005 — Logout and refresh-token revocation

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-005`
**Actor:** Signed-in admin and member users.

**Requirement:** A client can end its session by submitting the matching refresh token. The system revokes that token so it cannot be used to renew access.

---

### API-AUTH-006 — Current authenticated account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `AUTH-006`
**Actor:** Signed-in users.

**Requirement:** The current-account operation shall return the authenticated login and related profile context.

## Additional requirements

### API-AUTH-007 — Require username for member login

**Requirement:** The member login operation rejects a missing or blank username.

### API-AUTH-008 — Require password for member login

**Requirement:** The member login operation rejects a missing password.

### API-AUTH-009 — Restrict member login to member portal

**Requirement:** The member login operation calls portal authentication with the member portal type.

### API-AUTH-010 — Restrict admin login to admin portal

**Requirement:** The admin login operation calls portal authentication with the admin portal type.

### API-AUTH-011 — Require refresh token for credential renewal

**Requirement:** The refresh operation rejects a missing or blank refresh_token.

### API-AUTH-012 — Reject revoked refresh tokens

**Requirement:** The refresh operation rejects refresh tokens without a matching unrevoked, unexpired stored token.

### API-AUTH-013 — Rotate refresh token on renewal

**Requirement:** The refresh operation revokes the matched stored token and issues replacement credentials within a transaction.

### API-AUTH-014 — Check account activity on refresh

**Requirement:** The refresh operation rejects a login that is no longer active.

### API-AUTH-015 — Require matching login on logout

**Requirement:** The logout operation rejects a refresh token whose login or portal does not match the current access token.

### API-AUTH-016 — Return authenticated login profile

**Requirement:** The current-user operation returns login identity and portal information for a valid active login.
