# Batches



## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### API-BAT-001 — Browse batches

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `BAT-001`
**Actor:** Administrators and eligible members.

**Requirement:** An authenticated user browses batches available within their permitted scope.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `if (isAdmin($jwt)) {` at `api-backend-php/api/batches/index.php:10`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$batchIds = $database->select('enrollments', 'batch_id', [` at `api-backend-php/api/batches/index.php:20`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => [], 'meta' => $result['meta']]);` at `api-backend-php/api/batches/index.php:16`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/index.php`, `api-backend-php/api/batches/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-BAT-002 — View batch details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `BAT-002`
**Actor:** Administrators and eligible members.

**Requirement:** An authenticated user opens an eligible batch to inspect its details.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `if (isAdmin($jwt)) {` at `api-backend-php/api/batches/index.php:10`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$batchIds = $database->select('enrollments', 'batch_id', [` at `api-backend-php/api/batches/index.php:20`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => [], 'meta' => $result['meta']]);` at `api-backend-php/api/batches/index.php:16`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/index.php`, `api-backend-php/api/batches/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-BAT-003 — Create batch

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `BAT-003`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator creates a batch attached to an existing program.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/batches/store.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!empty($body['trainer_id']) && !recordExists($database, 'trainers', ['id' => (int) $body['trainer_id']])) errorResponse('NOT_FOUND', 'Trainer not found.', 404);` at `api-backend-php/api/batches/store.php:13`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if (!recordExists($database, 'programs', ['id' => $programId])) errorResponse('NOT_FOUND', 'Program not found.', 404);` at `api-backend-php/api/batches/store.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/store.php`, `api-backend-php/api/batches/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-BAT-004 — Update batch

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `BAT-004`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator edits the supported fields of an existing batch.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/batches/store.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!empty($body['trainer_id']) && !recordExists($database, 'trainers', ['id' => (int) $body['trainer_id']])) errorResponse('NOT_FOUND', 'Trainer not found.', 404);` at `api-backend-php/api/batches/store.php:13`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if (!recordExists($database, 'programs', ['id' => $programId])) errorResponse('NOT_FOUND', 'Program not found.', 404);` at `api-backend-php/api/batches/store.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/store.php`, `api-backend-php/api/batches/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-BAT-005 — View batch membership

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `BAT-005`
**Actor:** Authorized batch viewers.

**Requirement:** A permitted user can view the members associated with a batch.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/members.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessBatch($database, $jwt, $batch, 'members'));` at `api-backend-php/api/batches/members.php:11`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$members = $database->select('batch_members', ['[>]member_profiles' => ['member_id' => 'id']], [` at `api-backend-php/api/batches/members.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/members.php`. ### Stage 10 integration audit: INT-007 The Angular service sends `PATCH /batches/{id}`, while `api/routes.php` registers `PUT /api/v1/batches/{id}`. Service mismatch confirmed; UI reachability not established. This is an **integration discrepancy**, not a successful runtime test. See `requirements/audits/reachable-frontend-api-gaps.md`. ### Stage 10 integration audit: INT-008 The Angular service sends `POST /batches/{id}/members`, while `api/routes.php` registers `No POST route; GET members exists`. Service mismatch confirmed; UI reachability not established. This is an **integration discrepancy**, not a successful runtime test. See `requirements/a

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).