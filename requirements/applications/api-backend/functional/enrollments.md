# Enrollments

**Scope:** Source-observed PHP behavior. Static verification only; no runtime tests executed.

Each item below is independently testable. UI behavior and cross-application integration are documented separately.

## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


## API-ENR-001 — List enrollments

**Requirement:** GET /api/v1/enrollments returns a paginated enrollment list.

**Source-supported acceptance check:** The handler calls checkAuth() and returns enrollment records with pagination metadata.

**Source:** `api-backend-php/api/enrollments/index.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-002 — Create enrollment

**Requirement:** POST /api/v1/enrollments creates an enrollment for a family member in a batch.

**Source-supported acceptance check:** A successful request creates an active enrollment and active batch membership in one transaction.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-003 — Cancel enrollment

**Requirement:** PATCH /api/v1/enrollments/{id}/cancel cancels an eligible enrollment.

**Source-supported acceptance check:** A successful request sets enrollment status to cancelled and corresponding batch membership to dropped in one transaction.

**Source:** `api-backend-php/api/enrollments/cancel.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-004 — Require enrollment identifiers

**Requirement:** Enrollment creation requires family_id, member_id, and batch_id as numeric identifiers.

**Source-supported acceptance check:** Missing or nonnumeric required fields are passed to validateOrFail.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-005 — Authorize enrollment creation

**Requirement:** Only an administrator or an active primary member of the specified family may create its enrollment.

**Source-supported acceptance check:** Other authenticated actors receive a 403 authorization response.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-006 — Verify family membership

**Requirement:** The selected member must have an active family_members record for the specified family.

**Source-supported acceptance check:** The handler rejects a member without the required active membership.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-007 — Restrict batch status

**Requirement:** A batch must have upcoming or active status to accept enrollment.

**Source-supported acceptance check:** Other batch statuses raise BATCH_NOT_ENROLLABLE.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-008 — Reject duplicate enrollment

**Requirement:** A member cannot be enrolled twice in the same batch.

**Source-supported acceptance check:** An existing enrollment raises ENROLLMENT_EXISTS.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-009 — Enforce batch capacity

**Requirement:** When a batch capacity is defined, enrollment is rejected if active batch membership reaches that capacity.

**Source-supported acceptance check:** The handler counts active batch_members and raises BATCH_FULL when full.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-010 — Initialize enrollment financial state

**Requirement:** A newly created enrollment starts with unpaid payment status and a fee amount sourced from its program, defaulting to zero.

**Source-supported acceptance check:** The insert uses payment_status unpaid and program fee_amount or zero.

**Source:** `api-backend-php/api/enrollments/store.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-011 — Limit enrollment list visibility

**Requirement:** Non-admin enrollment listings are constrained to a family identifier present in the authentication token.

**Source-supported acceptance check:** If no family IDs exist, an empty list is returned; an unauthorized requested family ID falls back to the first token family ID.

**Source:** `api-backend-php/api/enrollments/index.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-012 — Authorize enrollment detail

**Requirement:** An enrollment can be viewed by an admin, an active primary family member, or the enrolled member.

**Source-supported acceptance check:** canAccessEnrollment returns true for those view scopes; show.php calls it.

**Source:** `api-backend-php/services/academics.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-013 — Authorize enrollment cancellation

**Requirement:** An enrollment can be cancelled by an admin or an active primary member of its family.

**Source-supported acceptance check:** canAccessEnrollment checks cancel scope; cancel.php rechecks inside the transaction.

**Source:** `api-backend-php/services/academics.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-014 — Reject repeat cancellation

**Requirement:** An enrollment already marked cancelled cannot be cancelled again.

**Source-supported acceptance check:** The handler raises ALREADY_CANCELLED.

**Source:** `api-backend-php/api/enrollments/cancel.php`

**Evidence:** Confirmed in source; runtime unverified.

---

## API-ENR-015 — Paginate enrollment listing

**Requirement:** Enrollment listings accept page and per_page parameters with per_page limited to 1–100.

**Source-supported acceptance check:** The response contains total, per_page, current_page and last_page metadata.

**Source:** `api-backend-php/api/enrollments/index.php`

**Evidence:** Confirmed in source; runtime unverified.

---