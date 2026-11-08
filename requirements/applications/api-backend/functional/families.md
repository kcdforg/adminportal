# Families

## Requirement summary

This document defines the expected families behavior provided by the API.

### API-FAM-001 — Browse families as administrator

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-001`
**Actor:** Administrators.

**Requirement:** An administrator browses family records within the administration application.

---

### API-FAM-002 — View family details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-002`
**Actor:** Administrators and eligible family members.

**Requirement:** An eligible user opens a family record; member access is restricted to their own family.

---

### API-FAM-003 — Create family

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-003`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator creates a family record.

---

### API-FAM-004 — Update family

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-004`
**Actor:** Elevated administrators and primary family members.

**Requirement:** An elevated administrator or the family’s primary member updates permitted family details. Family status changes must follow the approved policy.

---

### API-FAM-005 — Add family member association

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-005`
**Actor:** Authorized family managers.

**Requirement:** An eligible user links a member to a family within the supported permissions.

---

### API-FAM-006 — Update family member association

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `FAM-006`
**Actor:** Authorized family managers.

**Requirement:** An eligible user changes a supported association between an existing member and a family.

## Additional requirements

### API-FAM-019 — Restrict family creation to elevated administrators

**Requirement:** The system permits family creation only for an elevated administrator.

### API-FAM-007 — Reject duplicate family codes

**Requirement:** The system rejects creation when the submitted family code already exists.

### API-FAM-008 — Create family with active status

**Requirement:** A newly created family is saved with active status.

### API-FAM-009 — Create optional family address

**Requirement:** The system creates an address record when a nonempty address is provided during family creation.

### API-FAM-010 — Restrict family membership addition

**Requirement:** Only elevated administrators can add a member to a family through the family-members creation operation.

### API-FAM-011 — Validate family relationship type

**Requirement:** The system accepts father, mother, guardian or child as the relationship type when adding a family member.

### API-FAM-012 — Validate family member role

**Requirement:** The system accepts primary, normal or student as the role when adding a family member.

### API-FAM-013 — Reject duplicate active family membership

**Requirement:** The system rejects addition of a profile that is already an active member of the family.

### API-FAM-014 — Limit active primary family member

**Requirement:** The system rejects assigning a second active primary member to the same family.

### API-FAM-015 — Reactivate removed family membership

**Requirement:** When a removed membership exists, adding that profile restores the membership to active instead of inserting another membership.

### API-FAM-016 — Restrict family member updates

**Requirement:** The system requires family edit access before updating a family member.

### API-FAM-017 — Prevent duplicate member email on family update

**Requirement:** The system rejects an email already assigned to a different member profile.

### API-FAM-018 — Soft-remove family membership

**Requirement:** Removing a family member changes the membership status to removed rather than deleting the row.
