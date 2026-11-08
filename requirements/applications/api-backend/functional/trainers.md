# Trainers


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-TRN-001 — Browse trainer records

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `TRN-001`
**Actor:** Administrators and eligible trainers.

**Requirement:** An administrator browses trainer records.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires an authenticated administrator.
- Returns paginated trainer records with profile and address data; supports status filtering.

**Source references:**
- `api-backend-php/api/trainers/index.php`, `api-backend-php/api/trainers/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-TRN-002 — View trainer details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `TRN-002`
**Actor:** Administrators and eligible trainers.

**Requirement:** An administrator views a trainer record; an eligible trainer may view their own details.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Returns 404 when the trainer does not exist.
- Permits administrators or a trainer whose authenticated profile matches the requested trainer record.

**Source references:**
- `api-backend-php/api/trainers/index.php`, `api-backend-php/api/trainers/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-TRN-003 — Create trainer record

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `TRN-003`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator can create a trainer record, linking it to an existing member profile.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires elevated administrator permission.
- Requires an existing `profile_id` and rejects a profile already registered as a trainer.
- Creates an active trainer record with generated `TR-` code and logs the creation transaction.

**Source references:**
- `api-backend-php/api/trainers/store.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-TRN-004 — Update trainer record

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `TRN-004`
**Actor:** Authorized trainer managers.

**Requirement:** An authorized manager can update a trainer’s stored information.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires an existing trainer and either elevated administrator permission or ownership as a trainer.
- For non-elevated trainer owners, accepts updates only to `bio` and `specialization` (other submitted fields are discarded).
- Validates allowed fields, updates the trainer record, and records an activity entry.

**Source references:**
- `api-backend-php/api/trainers/update.php`. ### Stage 10 integration audit: INT-002 The Angular service sends `PATCH /trainers/{id}`, while `api/routes.php` registers `PUT /api/v1/trainers/{id}`. Confirmed invoked by trainer form. This is an **integration discrepancy**, not a successful runtime test. See `requirements/audits/reachable-frontend-api-gaps.md`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).