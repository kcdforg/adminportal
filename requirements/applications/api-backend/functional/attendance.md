# Attendance



## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### API-ATT-001 — Read session attendance

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ATT-001`
**Actor:** Authorized attendance viewers.

**Requirement:** A permitted user can inspect attendance recorded for a selected session.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/sessions/attendance.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessAttendance($database, $jwt, $session, $session['batch']));` at `api-backend-php/api/sessions/attendance.php:11`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$rows = $database->select('attendance', ['[>]member_profiles' => ['member_id' => 'id']], [` at `api-backend-php/api/sessions/attendance.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/sessions/attendance.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ATT-002 — Record session attendance

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ATT-002`
**Actor:** Authorized attendance recorders.

**Requirement:** A permitted user can record attendance for members of a session’s batch.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/sessions/attendance-store.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(canAccessAttendance($database, $jwt, $session, $session['batch']));` at `api-backend-php/api/sessions/attendance-store.php:11`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (empty($body['records']) || !is_array($body['records'])) {` at `api-backend-php/api/sessions/attendance-store.php:16`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if ($session === null) errorResponse('NOT_FOUND', 'Session not found.', 404);` at `api-backend-php/api/sessions/attendance-store.php:10`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/sessions/attendance-store.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ATT-003 — Update an attendance record

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ATT-003`
**Actor:** Authorized attendance editors.

**Requirement:** A permitted user can correct an existing attendance entry when the session permits editing.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/attendance/patch.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if ($old === null) errorResponse('NOT_FOUND', 'Attendance record not found.', 404);` at `api-backend-php/api/attendance/patch.php:10`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('attendance', $update, ['id' => $id]);` at `api-backend-php/api/attendance/patch.php:32`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/attendance/patch.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).


## Additional source-reconciled atomic requirements (2026-11-08)

The statements below are verified against the cited PHP handler branches only. They do not establish end-to-end operation.

### API-ATT-004 — Require attendance record for update

**Requirement:** The attendance update endpoint returns not found when the attendance record does not exist.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-005 — Authorize attendance updates

**Requirement:** The attendance update endpoint rejects actors without access to the associated session and batch.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-006 — Reject updates to locked attendance

**Requirement:** The attendance update endpoint rejects updates when the associated session has attendance locked.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-007 — Require attendance status on update

**Requirement:** The attendance update endpoint requires attendance_status in the request.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-008 — Restrict attendance status values

**Requirement:** The attendance update endpoint accepts only present, absent, late or excused as attendance_status.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-009 — Restrict updated attendance fields

**Requirement:** The attendance update endpoint takes only attendance_status and remarks from the supplied update fields.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-010 — Record attendance update actor

**Requirement:** The attendance update endpoint records the actor ID and marking timestamp on update.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.

### API-ATT-011 — Recheck lock inside attendance transaction

**Requirement:** The attendance update endpoint rechecks session access and lock state inside its database transaction.

**Source:** `api-backend-php/api/attendance/patch.php`

**Verification:** Source branch inspected; runtime untested.