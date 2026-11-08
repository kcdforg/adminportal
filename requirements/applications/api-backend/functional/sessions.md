# Sessions


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-SES-001 — Browse batch sessions

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-001`
**Actor:** Authorized batch viewers.

**Requirement:** An eligible user browses sessions associated with a batch.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/sessions.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessBatch($database, $jwt, $batch));` at `api-backend-php/api/batches/sessions.php:11`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$total = (int) $database->count('batch_sessions', $where);` at `api-backend-php/api/batches/sessions.php:16`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $data, 'meta' => [` at `api-backend-php/api/batches/sessions.php:25`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/sessions.php`, `api-backend-php/api/sessions/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-SES-002 — View session details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-002`
**Actor:** Authorized batch viewers.

**Requirement:** An eligible user opens a particular batch session.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/sessions.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessBatch($database, $jwt, $batch));` at `api-backend-php/api/batches/sessions.php:11`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$total = (int) $database->count('batch_sessions', $where);` at `api-backend-php/api/batches/sessions.php:16`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $data, 'meta' => [` at `api-backend-php/api/batches/sessions.php:25`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/sessions.php`, `api-backend-php/api/sessions/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-SES-003 — Schedule batch session

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-003`
**Actor:** Elevated administrators and authorized editors.

**Requirement:** An eligible user creates a session for an existing batch.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/session-store.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/batches/session-store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `'session_number' => isset($body['session_number']) ? (int) $body['session_number'] : null,` at `api-backend-php/api/batches/session-store.php:18`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if ($batch === null) errorResponse('NOT_FOUND', 'Batch not found.', 404);` at `api-backend-php/api/batches/session-store.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/session-store.php`, `api-backend-php/api/sessions/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-SES-004 — Update batch session

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-004`
**Actor:** Elevated administrators and authorized editors.

**Requirement:** An eligible user changes the supported details of an existing session.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/batches/session-store.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/batches/session-store.php:9`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `'session_number' => isset($body['session_number']) ? (int) $body['session_number'] : null,` at `api-backend-php/api/batches/session-store.php:18`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if ($batch === null) errorResponse('NOT_FOUND', 'Batch not found.', 404);` at `api-backend-php/api/batches/session-store.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/batches/session-store.php`, `api-backend-php/api/sessions/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-SES-005 — Lock session attendance

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-005`
**Actor:** Authorized attendance managers.

**Requirement:** An authorized manager can lock a session’s attendance to prevent further changes.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/sessions/lock.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/sessions/lock.php:9`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('batch_sessions', ['attendance_locked' => 1], ['id' => $id]);` at `api-backend-php/api/sessions/lock.php:16`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/sessions/lock.php`. ### Stage 10 integration audit: INT-003 The Angular service sends `PATCH /sessions/{id}`, while `api/routes.php` registers `PUT /api/v1/sessions/{id}`. Confirmed invoked by session form. This is an **integration discrepancy**, not a successful runtime test. See `requirements/audits/reachable-frontend-api-gaps.md`. ### Stage 10 integration audit: INT-004 The Angular service sends `PATCH /sessions/{id}`, while `api/routes.php` registers `POST /api/v1/sessions/{id}/lock`. Confirmed invoked by attendance component. This is an **integration discrepancy**, not a successful runtime test. See `requirements/audits/reachable-frontend-api-gaps.md`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).