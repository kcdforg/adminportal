# Programs

This document describes the current backend requirements for managing training programs.

## Requirement summary

- Authenticated users must be able to view programs through the API.
- Administrators with elevated access must be able to create and update programs.
- Program status changes must be controlled through a dedicated status flow.
- Program operations must reject missing records, invalid input, and unauthorized access.

## Status

This is a working requirement draft intended for planning and review. It should be validated against live runtime behavior before being treated as a final production requirement.

### API-PRG-001 — Browse programs through API

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-001`
**Actor:** Authenticated administrators and members.

**Requirement:** An authenticated user requests program information through the supported API. The list handler does not explicitly restrict results to published programs.

**Source-supported acceptance check:** The list handler calls `checkAuth()` and returns a `success` response containing paginated `data` and `meta`. It passes `status` and `program_type` as available filters to `paginateAcademics`; no mandatory published-only filter is specified in this handler.

**Source references:**
- `api-backend-php/api/programs/index.php`, `api-backend-php/api/programs/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-PRG-003 — Create program

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-003`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator creates a program.

**Source-supported acceptance check:** The create handler calls `checkAuth()`, requires `hasElevatedAccess()`, validates the JSON body with `validateProgram(..., "create")`, inserts a `programs` row, logs creation and responds with HTTP 201.

**Source references:**
- `api-backend-php/api/programs/store.php`, `api-backend-php/api/programs/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-PRG-004 — Update program

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-004`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator edits the supported fields of an existing program.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/programs/store.php:7`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/programs/store.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if ($old === null) errorResponse('NOT_FOUND', 'Program not found.', 404);` at `api-backend-php/api/programs/update.php:11`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->insert('programs', [` at `api-backend-php/api/programs/store.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/programs/store.php`, `api-backend-php/api/programs/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-PRG-005 — Change program status

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-005`
**Actor:** Authorized program managers.

**Requirement:** An authorized manager can change a program’s status through the supported operation.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/programs/status.php:8`. Runtime outcome not established.
- **Role/permission gate (source inspection):** Verify the behavior expressed by `requirePermission(hasElevatedAccess($jwt));` at `api-backend-php/api/programs/status.php:9`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `if ($old === null) errorResponse('NOT_FOUND', 'Program not found.', 404);` at `api-backend-php/api/programs/status.php:11`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('programs', ['status' => $body['status']], ['id' => $id]);` at `api-backend-php/api/programs/status.php:14`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/programs/status.php`. ### Stage 10 integration audit: INT-001 The Angular service sends `PATCH /programs/{id}`, while `api/routes.php` registers `PUT /api/v1/programs/{id}`. Confirmed invoked by program form. This is an **integration discrepancy**, not a successful runtime test. See `requirements/audits/reachable-frontend-api-gaps.md`. ## Pass 6 — member client and program-list boundary **Observed Ionic API integration:** `MemberApiService.getCourses()` requests `GET /api/v1/programs` and unwraps the returned array. The PHP `api/programs/index.php` handler requires authentication and returns a paginated program list without an explicit member-facing status filter. Howeve

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).