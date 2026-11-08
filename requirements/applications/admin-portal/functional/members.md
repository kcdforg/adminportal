# Members

This document covers the admin portal member-management workflow for the people managed by the program.

## Requirement summary

- Authorized administrators must be able to browse the member list.
- The list should support searching and filtering to find the right member quickly.
- The system should allow opening a member record to review the person’s profile details.
- Staff must be able to create and update member records with key personal data.
- The member record should support clear status handling and user-friendly validation.
- Missing or invalid data should be rejected cleanly without losing the user’s entered context.

## Status

This is a working requirement draft for planning and review. It should be validated against runtime behavior before sign-off.

### ADM-MBR-001 — View member list

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-001`
**Actor:** Authorized administrators.

**Requirement:** An authorized admin user can open the member list page and view members in the system.

**Acceptance criteria:**
- The administration portal shows a member list page for authenticated users.
- The list displays the available member records in a paginated or manageable table layout.
- If no members are found, the list shows an empty state instead of failing unexpectedly.
- Users can navigate to a specific member record from the list.

---

### ADM-MBR-002 — Search and filter members

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-002`
**Actor:** Authorized administrators.

**Requirement:** An authorized admin user can search and filter the member list to narrow the results.

**Acceptance criteria:**
- The list supports a search field for quick text lookup.
- Filters can be applied to reduce the result set by status or other supported criteria.
- Changing the search or filter resets the result to the first page of matching records.
- The system should keep the page state and results consistent with the selected search criteria.

---

### ADM-MBR-003 — Open member detail

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-003`
**Actor:** Authorized administrators.

**Requirement:** An administrator can open a member detail page to review the record for an individual person.

**Acceptance criteria:**
- Selecting a member in the list opens the relevant detail page.
- The page displays the member’s basic profile information and status.
- If the record cannot be loaded, the user sees a clear error state.
- The detail view supports returning to the member list without losing context.

---

### ADM-MBR-004 — Create a member record

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-004`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can create a new member record from the admin portal.

**Acceptance criteria:**
- The user can access a create-member action from the member list or an equivalent entry point.
- The form requires the key mandatory fields needed for a valid member profile.
- The system validates required values before saving.
- A successful create action confirms the record was saved and returns the user to a valid state.

---

### ADM-MBR-005 — Edit a member record

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-005`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can update an existing member record.

**Acceptance criteria:**
- The user can open a selected member in edit mode.
- The form includes edit controls for the member’s personal information and status.
- Invalid updates are blocked with clear validation feedback.
- Successful updates are saved and the user is informed of the result.

---

### ADM-MBR-006 — Validate member input

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-006`
**Actor:** Authorized administrators.

**Requirement:** The system must validate member data before saving to prevent incomplete or invalid records.

**Acceptance criteria:**
- Required names are enforced before submission.
- Email values are validated when provided.
- Empty or invalid submissions are flagged clearly to the user.
- The form preserves user-entered data when validation fails.

---

### ADM-MBR-007 — Manage member lifecycle state

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `MBR-007`
**Actor:** Authorized administrators.

**Requirement:** Members can be tracked with a usable lifecycle status within the admin portal.

**Acceptance criteria:**
- The system displays a member’s current active/inactive or equivalent status.
- Status changes are reflected in the list and detail views consistently.
- Users can update the status as part of member maintenance when the business process requires it.
- Invalid status changes are rejected with a clear error message.
