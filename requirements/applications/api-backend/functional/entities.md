# Entities


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-ENT-001 — Browse entities

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ENT-001`
**Actor:** Authenticated users and administrators.

**Requirement:** An authenticated user browses entities visible within their permissions.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/entities/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `if (!isAdmin($jwt)) {` at `api-backend-php/api/entities/index.php:9`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/entities/index.php:26`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/entities/index.php`, `api-backend-php/api/entities/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ENT-002 — View entity details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ENT-002`
**Actor:** Authenticated users and administrators.

**Requirement:** An authenticated user opens a permitted entity record.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/entities/index.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `if (!isAdmin($jwt)) {` at `api-backend-php/api/entities/index.php:9`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/entities/index.php:26`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/entities/index.php`, `api-backend-php/api/entities/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ENT-003 — Create entity

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ENT-003`
**Actor:** Authorized entity managers.

**Requirement:** A permitted user creates an entity record using supported fields.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/entities/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isElevatedAdmin($jwt));` at `api-backend-php/api/entities/store.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `'meta' => !empty($body['meta']) ? json_encode($body['meta'], JSON_THROW_ON_ERROR) : null,` at `api-backend-php/api/entities/store.php:18`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Entity not found.', 404);` at `api-backend-php/api/entities/update.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/entities/store.php`, `api-backend-php/api/entities/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ENT-004 — Update entity

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ENT-004`
**Actor:** Authorized entity managers.

**Requirement:** A permitted user edits an existing entity record using supported fields.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/entities/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(isElevatedAdmin($jwt));` at `api-backend-php/api/entities/store.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `'meta' => !empty($body['meta']) ? json_encode($body['meta'], JSON_THROW_ON_ERROR) : null,` at `api-backend-php/api/entities/store.php:18`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Entity not found.', 404);` at `api-backend-php/api/entities/update.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/entities/store.php`, `api-backend-php/api/entities/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).