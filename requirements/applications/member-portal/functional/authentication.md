# Authentication

## Requirement summary

- This document captures the current working flow in plain language.
- It supports planning, review, and backlog alignment.
- Runtime validation is still required before sign-off.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### MEM-AUTH-002 — Member portal sign-in

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `AUTH-002`
**Actor:** Admin and member account holders.

**Requirement:** An active member with a linked profile signs in through the parent portal using the member-specific login operation. Administrator accounts cannot use this portal.

**Source-grounded acceptance review (static; runtime unverified):**
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, $errors);` at `api-backend-php/api/auth/login.php:18`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/routes.php`
- `api-backend-php/api/auth/login.php`
- `api-backend-php/api/auth/admin-login.php`
- `api-backend-php/services/identity.php` (`authenticatePortalLogin`, `issueLoginTokens`)
- `kcdf-admin-app/src/app/core/services/auth.service.ts` (`login`)
- `kcdf-parents-app/src/app/core/member-auth.service.ts` (`login`).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### MEM-AUTH-009 — Persist member session

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `AUTH-009`
**Actor:** Ionic parent-portal users.

**Requirement:** After member sign-in, the Ionic application saves access and refresh tokens and profile context in browser storage.

**Source-grounded acceptance review (static; runtime unverified):**
- **Client API call (source inspection):** Verify the behavior expressed by `return this.http.post(`${environment.apiUrl}/auth/logout`, {` at `kcdf-parents-app/src/app/core/member-auth.service.ts:240`. Runtime outcome not established.

**Source references:**
- `kcdf-parents-app/src/app/core/member-auth.service.ts` (`login`, storage functions and session service). ## Unresolved / audit follow-ups - Determine full role-to-API authorization parity for every endpoint; Angular role guards are not server-side authorization. - Verify refresh/logout frontend error handling and automatic token refresh behavior through interceptors. - Confirm whether login/account status checks and token revocation are consistently enforced by protected routes. - Execute tests against a disposable database before upgrading static findings to runtime-verified.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### MEM-AUTH-010 — Clear member session

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `AUTH-010`
**Actor:** Ionic parent-portal users.

**Requirement:** When the member session is cleared, the Ionic application removes its stored credentials and profile context.

**Source-grounded acceptance review (static; runtime unverified):**
- **Client API call (source inspection):** Verify the behavior expressed by `return this.http.post(`${environment.apiUrl}/auth/logout`, {` at `kcdf-parents-app/src/app/core/member-auth.service.ts:240`. Runtime outcome not established.

**Source references:**
- `kcdf-parents-app/src/app/core/member-auth.service.ts` (`login`, storage functions and session service). ## Unresolved / audit follow-ups - Determine full role-to-API authorization parity for every endpoint; Angular role guards are not server-side authorization. - Verify refresh/logout frontend error handling and automatic token refresh behavior through interceptors. - Confirm whether login/account status checks and token revocation are consistently enforced by protected routes. - Execute tests against a disposable database before upgrading static findings to runtime-verified.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).