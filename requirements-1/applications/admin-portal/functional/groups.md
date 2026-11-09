# Groups

This document covers administrator management of member groups.

## Status

Working draft. Confirm group governance and membership policies.

### ADM-GRP-001 — Browse and view groups

**Actor:** Administrators with group-view access.

**Requirement:** An authorized administrator can browse groups and review a group’s details and membership.

**Acceptance criteria:**
- The list can be searched or filtered using supported group attributes.
- Group details show the group’s status and current membership.
- Restricted group information is not shown to users without access.

### ADM-GRP-002 — Create and update groups

**Actor:** Administrators with group-management access.

**Requirement:** An authorized administrator can create a group and update its supported details.

**Acceptance criteria:**
- Required group information is validated before saving.
- Invalid categories or unsupported values are clearly rejected.
- Successful changes are reflected in the group list and detail view.

### ADM-GRP-003 — Manage group membership and roles

**Actor:** Authorized group managers.

**Requirement:** An authorized group manager can maintain group membership and assign permitted member roles.

**Acceptance criteria:**
- The user can find eligible members and distinguish current members.
- Membership and role changes require appropriate permission.
- The result is reflected in the group member list and member access.

## Open questions

- Define group visibility, membership approval, and manager appointment policies.
- Define permitted group roles and the effects of removing a member.
