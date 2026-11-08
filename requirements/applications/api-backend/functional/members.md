# Members



## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### API-MEM-001 — Browse member profiles

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-001`
**Actor:** Administrators and member account holders.

**Requirement:** An administrator browses member profiles.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isAdmin($jwt));` at `api-backend-php/api/members/index.php:9`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/members/index.php:19`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/index.php` (7: $jwt = checkAuth();, 9: requirePermission(isAdmin($jwt));), `api-backend-php/api/members/show.php` (7: $jwt = checkAuth();, 15: requirePermission(canAccessMember($database, $jwt, $id, 'view'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-MEM-002 — View member profile

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-002`
**Actor:** Administrators and member account holders.

**Requirement:** An administrator views an eligible profile; a member can view their own profile in the parent portal.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isAdmin($jwt));` at `api-backend-php/api/members/index.php:9`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/members/index.php:19`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/index.php` (7: $jwt = checkAuth();, 9: requirePermission(isAdmin($jwt));), `api-backend-php/api/members/show.php` (7: $jwt = checkAuth();, 15: requirePermission(canAccessMember($database, $jwt, $id, 'view'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-MEM-003 — Create member profile

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-003`
**Actor:** Elevated administrators and profile owners.

**Requirement:** An elevated administrator creates a member profile.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isElevatedAdmin($jwt));` at `api-backend-php/api/members/store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!empty($body['email']) && recordExists($database, 'member_profiles', ['email' => $body['email']])) {` at `api-backend-php/api/members/store.php:12`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Member profile not found.', 404);` at `api-backend-php/api/members/update.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/store.php` (7: $jwt = checkAuth();, 9: requirePermission(isElevatedAdmin($jwt));), `api-backend-php/api/members/update.php` (7: $jwt = checkAuth();, 13: requirePermission(canAccessMember($database, $jwt, $id, 'edit'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-MEM-004 — Update member profile

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-004`
**Actor:** Elevated administrators and profile owners.

**Requirement:** An administrator or profile owner edits an existing member profile. The current handler accepts a status field; the intended self-service policy remains to be confirmed.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isElevatedAdmin($jwt));` at `api-backend-php/api/members/store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!empty($body['email']) && recordExists($database, 'member_profiles', ['email' => $body['email']])) {` at `api-backend-php/api/members/store.php:12`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Member profile not found.', 404);` at `api-backend-php/api/members/update.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/store.php` (7: $jwt = checkAuth();, 9: requirePermission(isElevatedAdmin($jwt));), `api-backend-php/api/members/update.php` (7: $jwt = checkAuth();, 13: requirePermission(canAccessMember($database, $jwt, $id, 'edit'));).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-MEM-005 — Create member login

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-005`
**Actor:** Authorized account managers.

**Requirement:** An eligible account manager creates login credentials linked to a member profile.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/login-store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `$roles = getUserRoles($jwt);` at `api-backend-php/api/members/login-store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `} elseif (strlen($username) > 100 || preg_match('/[\x00-\x1F\x7F]/', $username)) {` at `api-backend-php/api/members/login-store.php:37`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [` at `api-backend-php/api/members/login-store.php:19`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/login-store.php` (7: $jwt = checkAuth();, 15: requirePermission($canCreateLogin);), `api-backend-php/api/members/login-update.php` (7: $jwt = checkAuth();, 14: requirePermission($canManageLogin);), `api-backend-php/api/members/login-destroy.php` (7: $jwt = checkAuth();, 14: requirePermission($canManageLogin);).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-MEM-006 — Maintain member login

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-006`
**Actor:** Authorized account managers.

**Requirement:** An eligible account manager updates supported member-login details using the available API operations.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/login-store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `$roles = getUserRoles($jwt);` at `api-backend-php/api/members/login-store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `} elseif (strlen($username) > 100 || preg_match('/[\x00-\x1F\x7F]/', $username)) {` at `api-backend-php/api/members/login-store.php:37`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [` at `api-backend-php/api/members/login-store.php:19`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/login-store.php` (7: $jwt = checkAuth();, 15: requirePermission($canCreateLogin);), `api-backend-php/api/members/login-update.php` (7: $jwt = checkAuth();, 14: requirePermission($canManageLogin);), `api-backend-php/api/members/login-destroy.php` (7: $jwt = checkAuth();, 14: requirePermission($canManageLogin);).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-MEM-007 — Manage member entity relations

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-007`
**Actor:** Authorized relation managers.

**Requirement:** An eligible user can maintain the relationship between a member and an entity.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/members/entity-relations.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessMember($database, $jwt, $memberId, 'view'));` at `api-backend-php/api/members/entity-relations.php:12`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (empty($body['entity_id'])) {` at `api-backend-php/api/members/entity-relation-store.php:15`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Member profile not found.', 404);` at `api-backend-php/api/members/entity-relations.php:10`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/members/entity-relations.php` (7: $jwt = checkAuth();, 12: requirePermission(canAccessMember($database, $jwt, $memberId, 'view'));), `api-backend-php/api/members/entity-relation-store.php` (8: $jwt = checkAuth();, 12: requirePermission(canAccessMember($database, $jwt, $memberId, 'manage_relations'));), `api-backend-php/api/members/entity-relation-delete.php` (9: $jwt = checkAuth();, 13: requirePermission(canAccessMember($database, $jwt, $memberId, 'manage_relations'));). ## Pass 7 — profile edit authorization and status boundary **Source-observed authorization:** `POST /api/v1/members` requires an elevated administrator. Profile edits (`PUT /api/v1/members/{id}`) are all

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).


## Additional source-verified atomic requirements — 2026-11-08

These describe observed PHP handler behavior, not runtime-tested outcomes. Existing broad IDs remain unchanged.

### API-MEM-012 — Restrict member profile creation

**Requirement:** Only elevated administrators can create member profiles through the member creation endpoint.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/members/store.php:9`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-MEM-011 — Reject duplicate member email on creation

**Requirement:** The backend rejects a new member profile when its email is already used by another profile.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/members/store.php:13`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-MEM-008 — Create member profiles as active

**Requirement:** New member profiles are stored with active status.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/members/store.php:28`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-MEM-009 — Restrict member profile updates

**Requirement:** The backend checks member edit permission before changing a member profile.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/members/update.php:13`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-MEM-010 — Reject duplicate member email on update

**Requirement:** The backend rejects changing a member email to one belonging to a different profile.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/members/update.php:18`.

**Evidence status:** PHP handler inspected; runtime unverified.