# Batches

This document covers how administrators organize program delivery into batches.

## Status

Working draft. Confirm capacity, scheduling, and lifecycle rules.

### ADM-BAT-001 — Browse and view batches

**Actor:** Administrators with batch-view access.

**Requirement:** An authorized administrator can review batches and open a batch to see its program, schedule, status, and membership.

**Acceptance criteria:**
- The list supports finding batches by relevant program, status, or search criteria.
- A batch detail view shows its key information and associated members.
- Empty results and unavailable records have clear states.

### ADM-BAT-002 — Create and update batches

**Actor:** Administrators with batch-management access.

**Requirement:** An authorized administrator can create a batch and update its supported details.

**Acceptance criteria:**
- The user selects a valid program for the batch.
- Required schedule and identifying information are validated.
- Invalid data is not saved and remains available for correction.
- Successful changes are confirmed and reflected in the batch views.

### ADM-BAT-003 — Review batch membership

**Actor:** Administrators with batch-view access.

**Requirement:** An authorized administrator can review the members enrolled in a batch.

**Acceptance criteria:**
- The membership list identifies each enrolled person and relevant enrollment status.
- The displayed membership reflects the current enrollment records.
- Access is limited to users permitted to view the batch.

## Open questions

- Define capacity, overbooking, and wait-list behavior.
- Confirm which batch changes are allowed after enrollment or session scheduling begins.
