# Activity Logs

This document describes the activity log requirements for the admin-facing audit and monitoring workflow.

## Requirement summary

- Only authenticated super administrators should be allowed to access activity logs.
- The system should allow viewing recent log entries in a structured list.
- The user should be able to filter entries by date range, actor, action, entity, and result where supported.
- The system should present enough detail for an administrator to understand what changed and when.
- Unauthorized access, malformed filters, and missing data should be rejected clearly.

## Status

This is a working requirement draft for planning and review. It should be validated against live runtime behavior before being treated as a final production requirement.

### API-LOG-001 — View activity log

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `LOG-001`
**Actor:** Super administrators.

**Requirement:** A super administrator can view the list of recent activity records captured by the system.

**Acceptance criteria:**
- The requester must be authenticated before the system returns activity log data.
- The requester must have super-administrator access.
- The system returns a paginated or filtered list of activity entries in a usable format.
- If no records match, the response should be empty or clearly indicate no results rather than failing unexpectedly.
- Unauthorized access should be rejected with a clear error response.

---

### API-LOG-002 — Filter activity log

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `LOG-002`
**Actor:** Super administrators.

**Requirement:** A super administrator can narrow the activity log using supported filters to focus on relevant events.

**Acceptance criteria:**
- Supported filters should be clearly defined and applied consistently.
- The system should allow narrowing by relevant dimensions such as date, actor, action, target entity, or outcome.
- Invalid or unsupported filter values should not break the request; they should be rejected cleanly.
- The total count and filtered result set should remain consistent with the selected criteria.

---

### API-LOG-003 — Inspect activity change

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `LOG-003`
**Actor:** Super administrators.

**Requirement:** A super administrator can inspect an individual activity record to understand what happened, who performed it, and what changed.

**Acceptance criteria:**
- The system should show the key metadata for each activity record, including timestamp, actor, action, and affected object.
- If the record includes a change payload or before/after values, they should be presented in a readable format.
- The user should be able to distinguish between successful and failed actions when the record contains that data.
- Missing or partial details should not cause the entire view to fail; the system should display what is available and handle gaps gracefully.

---

### API-LOG-004 — Reject unauthorized access

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `LOG-004`
**Actor:** Any user without the required admin role.

**Requirement:** Users without the correct role or authentication status must not be able to retrieve activity log data.

**Acceptance criteria:**
- Unauthenticated users are rejected before access is granted.
- Users without super-admin privileges are denied access.
- The system returns a clear access-denied or unauthorized response instead of exposing log data.
- The denial is consistent across all log-listing and log-detail operations.
