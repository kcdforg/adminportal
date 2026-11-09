# Enrollments

This document covers administrator-assisted enrollment and enrollment review.

## Status

Working draft. Confirm eligibility and cancellation policies.

### ADM-ENR-001 — Browse and review enrollments

**Actor:** Administrators with enrollment-view access.

**Requirement:** An authorized administrator can browse enrollments and review an enrollment’s member, batch, and status.

**Acceptance criteria:**
- The list can be narrowed using supported filters.
- Each result clearly identifies the member, family, batch, and enrollment state where available.
- Empty results and missing records are handled clearly.

### ADM-ENR-002 — Enroll a family member

**Actor:** Administrators with enrollment-management access.

**Requirement:** An authorized administrator can enroll an eligible family member in a batch.

**Acceptance criteria:**
- The user selects a valid member and eligible batch.
- Ineligible or duplicate enrollment is prevented with an explanation.
- Successful enrollment is confirmed and appears in relevant member and batch views.

### ADM-ENR-003 — Cancel an enrollment

**Actor:** Administrators with enrollment-management access.

**Requirement:** An authorized administrator can cancel an enrollment when cancellation is permitted.

**Acceptance criteria:**
- The user can distinguish active from cancelled enrollments.
- The system applies the agreed cancellation policy and confirms the outcome.
- A failed cancellation does not appear successful.

## Open questions

- Define eligibility, duplicate enrollment, cancellation deadlines, and any fees or refunds.
- Confirm whether members can request enrollment themselves.
