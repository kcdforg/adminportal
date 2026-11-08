# Families

This document covers the admin portal family-management workflow used to manage household records and their linked members.

## Requirement summary

- Authorized administrators must be able to view the family list.
- The system should support searching and filtering families by relevant criteria.
- Staff must be able to create and update family records.
- Family detail pages should show the family profile and the members linked to it.
- Admin users should be able to add, edit, and remove family-member relationships.
- The system should support family login setup for linked members when required.
- Missing data, invalid relationships, or permission errors should be surfaced clearly.

## Status

This is a working requirement draft for planning and review. It should be validated against runtime behavior before sign-off.

### ADM-FAM-001 — View family list

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-001`
**Actor:** Authorized administrators.

**Requirement:** An authorized admin user can open the family list page and review the current family records.

**Acceptance criteria:**
- The administration portal displays a family list page for authenticated users.
- Family records appear in a clear, navigable table or list format.
- The list supports paging when the number of families exceeds one page.
- If no families exist, the page shows an empty state rather than an error.

---

### ADM-FAM-002 — Search and filter families

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-002`
**Actor:** Authorized administrators.

**Requirement:** An authorized admin user can search and filter the family list to locate a household quickly.

**Acceptance criteria:**
- The user can apply a text search across family names or identifying values.
- Filters can be used to narrow the list by status or other supported criteria.
- Search and filter changes update the visible result set consistently.
- The user can reset the filters to return the full family list.

---

### ADM-FAM-003 — Create a family record

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-003`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can create a new family record in the admin portal.

**Acceptance criteria:**
- The user can access a create-family action from the family list.
- The form captures the required family identity data, such as family name and code where required.
- The form validates required values before the request is submitted.
- A successful save confirms the action and returns the user to a valid working state.

---

### ADM-FAM-004 — Edit a family record

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-004`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can update an existing family record.

**Acceptance criteria:**
- The user can open a family in edit mode from the list or detail page.
- The form allows updating key family details and family status.
- Invalid data is blocked with clear validation feedback.
- Successful edits are saved and the updated family is displayed after completion.

---

### ADM-FAM-005 — View family detail

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-005`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can open a family detail page to review the household record and its linked members.

**Acceptance criteria:**
- Selecting a family opens the corresponding detail page.
- The family detail page displays the household profile and key family metadata.
- The page lists the associated family members and their relationship data.
- If the family cannot be loaded, the system displays a clear error state.

---

### ADM-FAM-006 — Add a family member

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-006`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can add a person to a family and assign the appropriate relationship type.

**Acceptance criteria:**
- The user can add a member to a family from the family detail view.
- The system supports assigning relationship labels such as father, mother, guardian, or child.
- The form validates a valid relationship and required member context.
- A successful save updates the family membership list immediately.

---

### ADM-FAM-007 — Edit a family member relationship

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-007`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can update the relationship details of an existing family member.

**Acceptance criteria:**
- The user can edit a family member record from the family detail screen.
- Relationship changes update the person’s assigned role within the household.
- Invalid relationship values are rejected clearly.
- The updated relationship is displayed consistently in the family member list.

---

### ADM-FAM-008 — Remove a family member

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-008`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can remove a family member connection when the relationship or record is no longer valid.

**Acceptance criteria:**
- The user can initiate removal from the family detail page.
- The action requires confirmation before the household relationship is removed.
- The record is removed cleanly from the family’s member list.
- The system reports an error if the removal fails due to validation or backend constraints.

---

### ADM-FAM-009 — Manage member login access for family members

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-009`
**Actor:** Authorized administrators.

**Requirement:** An authorized administrator can create, update, or deactivate a family member login when access to a portal is required.

**Acceptance criteria:**
- The user can access a login-management action from the family detail view.
- The system supports creating a login for a linked member when the data is valid.
- Existing login settings can be updated without resetting unrelated family data.
- A deactivation action must clearly revoke access and confirm the status change.
- If the backend rejects the login action, the user is shown a clear message and the family view remains stable.

---

### ADM-FAM-010 — Validate family and membership data

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `FAM-010`
**Actor:** Authorized administrators.

**Requirement:** The system must validate family and membership data before saving to avoid incomplete or inconsistent household records.

**Acceptance criteria:**
- Required family information must be present before save.
- Address values are validated when a partial or full address is entered.
- Family member relationship data must be valid before submission.
- Validation failures retain the user’s entered data and use clear feedback messages.
