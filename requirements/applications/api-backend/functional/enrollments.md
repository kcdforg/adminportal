# Enrollments

This document describes the rules for creating, viewing, and cancelling program enrollments.

## Status

Working draft. Confirm capacity, eligibility, cancellation, and fee rules before approval.

### API-ENR-001 — List enrollments

**Requirement:** An authorized user can retrieve a paginated list of enrollments within their permitted scope.

**Acceptance criteria:**
- Results include the relevant enrollment and status information.
- Page size and page number are validated and bounded.
- An empty result set is returned as a valid outcome.
- Non-administrators see only enrollments they are allowed to access.

### API-ENR-002 — Create enrollment

**Requirement:** An authorized administrator or eligible primary family member can enroll an eligible family member in a batch.

**Acceptance criteria:**
- The family, member, and batch must be valid and associated as required.
- The member must be eligible for the selected batch.
- Duplicate enrollment is rejected.
- A batch that is not open for enrollment cannot accept a new enrollment.
- When a capacity limit applies, enrollment is rejected once the limit is reached.
- A successful enrollment records the applicable fee and initial payment state.

### API-ENR-003 — Cancel enrollment

**Requirement:** An authorized administrator or eligible primary family member can cancel an enrollment when cancellation is permitted.

**Acceptance criteria:**
- A user may view and cancel only enrollments within their permitted scope.
- An already-cancelled enrollment cannot be cancelled again.
- The system clearly confirms success or explains why cancellation was rejected.

### API-ENR-004 — Require enrollment details

**Requirement:** Creating an enrollment requires a family, member, and batch to be identified.

### API-ENR-005 — Authorize enrollment creation

**Requirement:** Enrollment creation is limited to administrators or an eligible primary member of the specified family.

### API-ENR-006 — Verify family membership

**Requirement:** The selected member must have an active relationship with the specified family.

### API-ENR-007 — Restrict batch eligibility

**Requirement:** Only batches open for enrollment may accept new enrollments.

### API-ENR-008 — Prevent duplicate enrollment

**Requirement:** A member cannot have more than one active enrollment in the same batch.

### API-ENR-009 — Enforce batch capacity

**Requirement:** When a batch capacity is defined, the system rejects enrollment after the capacity is reached.

### API-ENR-010 — Initialize enrollment payment state

**Requirement:** A new enrollment records the applicable program fee and an initial unpaid payment state.

### API-ENR-011 — Limit enrollment visibility

**Requirement:** Non-administrators can list only enrollments associated with a family they are permitted to access.

### API-ENR-012 — Authorize enrollment detail

**Requirement:** Enrollment details are available only to an administrator, an eligible primary family member, or the enrolled member.

### API-ENR-013 — Authorize enrollment cancellation

**Requirement:** Cancellation is limited to an administrator or an eligible primary member of the enrollment’s family.

### API-ENR-014 — Reject repeat cancellation

**Requirement:** An enrollment already marked as cancelled cannot be cancelled again.

### API-ENR-015 — Bound enrollment pagination

**Requirement:** Enrollment listing supports pagination with a page size between 1 and 100.

## Open questions

- Confirm whether members can enroll themselves or whether staff must initiate enrollment.
- Define cancellation deadlines, wait-list behavior, and fee/refund handling.
- Confirm what payment state should be recorded when a program has no fee.
