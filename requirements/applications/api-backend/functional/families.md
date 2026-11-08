# Families



## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### API-FAM-001 — Browse families as administrator

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-001`
**Actor:** Administrators and eligible family members.

**Requirement:** An administrator browses family records within the administration application.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/families/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isAdmin($jwt));` at `api-backend-php/api/families/index.php:9`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/families/index.php:24`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/families/index.php` (7: $jwt = checkAuth();, 9: requirePermission(isAdmin($jwt));), `api-backend-php/api/families/show.php` (7: $jwt = checkAuth();, 15: requirePermission(canAccessFamily($database, $jwt, $id, 'view'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-FAM-002 — View family details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-002`
**Actor:** Administrators and eligible family members.

**Requirement:** An eligible user opens a family record; member access is restricted to their own family.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/families/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isAdmin($jwt));` at `api-backend-php/api/families/index.php:9`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/families/index.php:24`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/families/index.php` (7: $jwt = checkAuth();, 9: requirePermission(isAdmin($jwt));), `api-backend-php/api/families/show.php` (7: $jwt = checkAuth();, 15: requirePermission(canAccessFamily($database, $jwt, $id, 'view'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-FAM-003 — Create family

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-003`
**Actor:** Elevated administrators and primary family members.

**Requirement:** An elevated administrator creates a family record.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/families/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isElevatedAdmin($jwt));` at `api-backend-php/api/families/store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (isset($body['family_code']) && is_string($body['family_code'])` at `api-backend-php/api/families/store.php:12`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Family not found.', 404);` at `api-backend-php/api/families/update.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/families/store.php` (7: $jwt = checkAuth();, 9: requirePermission(isElevatedAdmin($jwt));), `api-backend-php/api/families/update.php` (7: $jwt = checkAuth();, 15: requirePermission(canAccessFamily($database, $jwt, $id, 'edit'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-FAM-004 — Update family

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-004`
**Actor:** Elevated administrators and primary family members.

**Requirement:** An elevated administrator or the family’s primary member updates the family record. The existing handler also accepts a status change; policy approval is outstanding.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/families/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isElevatedAdmin($jwt));` at `api-backend-php/api/families/store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (isset($body['family_code']) && is_string($body['family_code'])` at `api-backend-php/api/families/store.php:12`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Family not found.', 404);` at `api-backend-php/api/families/update.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/families/store.php` (7: $jwt = checkAuth();, 9: requirePermission(isElevatedAdmin($jwt));), `api-backend-php/api/families/update.php` (7: $jwt = checkAuth();, 15: requirePermission(canAccessFamily($database, $jwt, $id, 'edit'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-FAM-005 — Add family member association

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-005`
**Actor:** Authorized family managers.

**Requirement:** An eligible user links a member to a family within the supported permissions.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/families/members.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessFamily($database, $jwt, $familyId, 'view'));` at `api-backend-php/api/families/members.php:14`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (isset($body['profile_id'])) {` at `api-backend-php/api/families/members-store.php:15`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Family not found.', 404);` at `api-backend-php/api/families/members.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/families/members.php` (8: $jwt = checkAuth();, 14: requirePermission(canAccessFamily($database, $jwt, $familyId, 'view'));), `api-backend-php/api/families/members-store.php` (8: $jwt = checkAuth();, 12: requirePermission(isElevatedAdmin($jwt));), `api-backend-php/api/families/members-update.php` (9: $jwt = checkAuth();, 14: requirePermission(canAccessFamily($database, $jwt, $familyId, 'edit'));), `api-backend-php/api/families/members-destroy.php` (9: $jwt = checkAuth();, 13: requirePermission(canAccessFamily($database, $jwt, $familyId, 'edit'));). ## Pass 7 — family read/write authorization and status boundary **Source-observed authorization:** `GET /api/v1/families` req

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-FAM-006 — Update family member association

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-006`
**Actor:** Authorized family managers.

**Requirement:** An eligible user changes a supported association between an existing member and a family.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/families/members.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessFamily($database, $jwt, $familyId, 'view'));` at `api-backend-php/api/families/members.php:14`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (isset($body['profile_id'])) {` at `api-backend-php/api/families/members-store.php:15`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Family not found.', 404);` at `api-backend-php/api/families/members.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/families/members.php` (8: $jwt = checkAuth();, 14: requirePermission(canAccessFamily($database, $jwt, $familyId, 'view'));), `api-backend-php/api/families/members-store.php` (8: $jwt = checkAuth();, 12: requirePermission(isElevatedAdmin($jwt));), `api-backend-php/api/families/members-update.php` (9: $jwt = checkAuth();, 14: requirePermission(canAccessFamily($database, $jwt, $familyId, 'edit'));), `api-backend-php/api/families/members-destroy.php` (9: $jwt = checkAuth();, 13: requirePermission(canAccessFamily($database, $jwt, $familyId, 'edit'));). ## Pass 7 — family read/write authorization and status boundary **Source-observed authorization:** `GET /api/v1/families` req

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).


## Additional source-verified atomic requirements — 2026-11-08

These describe observed PHP handler behavior, not runtime-tested outcomes. Existing broad IDs remain unchanged.

### API-FAM-019 — Restrict family creation to elevated administrators

**Requirement:** The backend permits family creation only for an elevated administrator.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/store.php:9`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-007 — Reject duplicate family codes

**Requirement:** The backend rejects creation when the submitted family code already exists.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/store.php:14`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-008 — Create family with active status

**Requirement:** A newly created family is saved with active status.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/store.php:23`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-009 — Create optional family address

**Requirement:** The backend creates an address record when a nonempty address is provided during family creation.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/store.php:18`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-010 — Restrict family membership addition

**Requirement:** Only elevated administrators can add a member to a family through the family-members creation endpoint.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-store.php:12`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-011 — Validate family relationship type

**Requirement:** The backend accepts father, mother, guardian or child as the relationship type when adding a family member.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-store.php:24`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-012 — Validate family member role

**Requirement:** The backend accepts primary, normal or student as the role when adding a family member.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-store.php:30`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-013 — Reject duplicate active family membership

**Requirement:** The backend rejects addition of a profile that is already an active member of the family.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-store.php:65`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-014 — Limit active primary family member

**Requirement:** The backend rejects assigning a second active primary member to the same family.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-store.php:73`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-015 — Reactivate removed family membership

**Requirement:** When a removed membership exists, adding that profile restores the membership to active instead of inserting another membership.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-store.php:76`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-016 — Restrict family member updates

**Requirement:** The backend requires family edit access before updating a family member.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-update.php:14`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-017 — Prevent duplicate member email on family update

**Requirement:** The backend rejects an email already assigned to a different member profile.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-update.php:32`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-FAM-018 — Soft-remove family membership

**Requirement:** Removing a family member changes the membership status to removed rather than deleting the row.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/families/members-destroy.php:19`.

**Evidence status:** PHP handler inspected; runtime unverified.