# Programs

This document describes the program-management capabilities exposed through the system API.

## Requirement summary

- Authorized users can browse and view program information.
- Users with the appropriate administrative permission can create and update programs.
- Program status changes follow the organization’s agreed lifecycle rules.
- Invalid input, missing records, and unauthorized operations are rejected with clear outcomes.

## Status

Working draft. Confirm the rules below with product stakeholders and verify the portal-to-API integration before approval.

### API-PRG-001 — Browse programs

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-001`
**Actor:** Authenticated users with program-view access.

**Requirement:** An authorized user can retrieve a list of programs and narrow the results using supported filters.

**Acceptance criteria:**
- The response contains a consistent list and pagination information.
- Supported filters are applied consistently to both the displayed results and total count.
- An empty result set is returned as a valid outcome.
- The product defines which program statuses are visible to each type of user.

### API-PRG-003 — Create program

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-003`
**Actor:** Administrators with program-management permission.

**Requirement:** An authorized administrator can create a program by supplying the required program information.

**Acceptance criteria:**
- Required values and allowed formats are defined and validated.
- A successful request creates one program and returns its identifying information.
- Invalid input does not create a partial program and is reported clearly.
- Users without permission cannot create programs.

### API-PRG-004 — Update program

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-004`
**Actor:** Administrators with program-management permission.

**Requirement:** An authorized administrator can update the supported information for an existing program.

**Acceptance criteria:**
- Only supported fields can be changed through this operation.
- A missing program is reported as not found.
- Invalid updates are rejected without partially changing the record.
- Users without permission cannot update programs.

### API-PRG-005 — Change program status

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PRG-005`
**Actor:** Administrators with program-management permission.

**Requirement:** An authorized administrator can change a program’s lifecycle status.

**Acceptance criteria:**
- Only defined statuses and allowed status transitions are accepted.
- A missing program or invalid transition is reported clearly.
- The updated status is returned or can be retrieved immediately after the change.
- Status changes do not silently modify unrelated program information.

## Open questions

- Define program statuses, transition rules, and which user groups may see unpublished or inactive programs.
- Confirm which program fields are required, editable, and unique.
- Confirm the member portal’s expected program/course listing and how it should handle pagination.
- Resolve the portal-to-API update-method mismatch before treating program editing as an end-to-end capability.
