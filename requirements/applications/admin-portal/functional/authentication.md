# Authentication


## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### ADM-AUTH-001 — Admin portal sign-in

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `AUTH-001`
**Actor:** Admin and member account holders.

**Requirement:** An active administrator signs in through the administration portal using the administrator-specific login operation. Member accounts cannot use this portal.

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

### ADM-AUTH-007 — Require admin sign-in for protected screens

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `AUTH-007`
**Actor:** Angular administration users.

**Requirement:** The Angular administration app redirects unauthenticated users away from protected routes.

**Acceptance verification:** Pending source-specific reconciliation. The implementation and its request validation, authorization, response, and state changes must be checked against the cited source before defining a pass/fail criterion. Do not infer working UI or API integration from handler presence alone.

**Source references:**
- `kcdf-admin-app/src/app/core/guards/auth.guard.ts`
- `kcdf-admin-app/src/app/core/guards/role.guard.ts`
- `kcdf-admin-app/src/app/app.routes.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### ADM-AUTH-008 — Restrict administrator screens by role

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `AUTH-008`
**Actor:** Angular administration users.

**Requirement:** The Angular administration app restricts role-controlled routes and redirects users lacking the required role. Server-side permissions remain authoritative.

**Acceptance verification:** Pending source-specific reconciliation. The implementation and its request validation, authorization, response, and state changes must be checked against the cited source before defining a pass/fail criterion. Do not infer working UI or API integration from handler presence alone.

**Source references:**
- `kcdf-admin-app/src/app/core/guards/auth.guard.ts`
- `kcdf-admin-app/src/app/core/guards/role.guard.ts`
- `kcdf-admin-app/src/app/app.routes.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).