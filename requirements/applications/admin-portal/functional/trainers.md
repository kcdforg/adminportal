# Trainers

This document covers the administration of trainer records.

## Status

Working draft. Confirm trainer information requirements and assignment rules.

### ADM-TRN-001 — Browse and view trainers

**Actor:** Administrators with trainer-view access.

**Requirement:** An authorized administrator can browse trainer records and open a trainer’s details.

**Acceptance criteria:**
- The list can be searched or filtered by supported criteria.
- Trainer detail displays the information needed for administration.
- Missing records and empty results have clear states.

### ADM-TRN-002 — Create and update trainer records

**Actor:** Administrators with trainer-management access.

**Requirement:** An authorized administrator can create and update trainer information.

**Acceptance criteria:**
- Required contact and profile information is validated.
- Invalid data is not saved and is explained clearly.
- Successful changes are reflected in the list and detail views.

### ADM-TRN-003 — Manage trainer status and assignments

**Actor:** Administrators with trainer-management access.

**Requirement:** An authorized administrator can maintain a trainer’s availability or status and relevant assignments.

**Acceptance criteria:**
- Current status and assignments are visible to permitted users.
- Changes do not silently remove or invalidate existing session assignments.
- The system explains any conflict that prevents a change.

## Open questions

- Confirm trainer lifecycle statuses and whether trainers can be assigned to multiple batches.
- Define which personal and contact details are required and who may view them.
