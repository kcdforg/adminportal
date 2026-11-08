# Programs

This document covers how administrators maintain training programs offered by the organization.

## Status

Working draft. Confirm program lifecycle, required details, and member visibility rules.

### ADM-PRG-001 — Browse programs

**Actor:** Administrators with program-view access.

**Requirement:** An authorized administrator can view and search the program catalogue.

**Acceptance criteria:**
- The list shows each program’s identifying details and current status.
- Search and filters return matching programs and support an empty state.
- Selecting a program opens its details.

### ADM-PRG-002 — Create and edit programs

**Actor:** Administrators with program-management access.

**Requirement:** An authorized administrator can create a program and update its supported details.

**Acceptance criteria:**
- Required fields and validation rules are clear before saving.
- Invalid input is identified without losing entered information.
- Successful changes are confirmed and visible in the program list and detail view.

### ADM-PRG-003 — Manage program status

**Actor:** Administrators with program-management access.

**Requirement:** An authorized administrator can change a program’s status according to the agreed lifecycle.

**Acceptance criteria:**
- Only allowed statuses and transitions can be selected.
- The result is reflected consistently in program and batch views.
- Users are warned when a status change affects existing batches or enrollments.

## Open questions

- Define statuses and transition rules.
- Define whether inactive or unpublished programs remain visible to members and administrators.
- Confirm what happens to existing batches and enrollments when a program is deactivated.
