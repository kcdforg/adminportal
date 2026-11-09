# Groups

## Requirement summary

This document defines the expected groups behavior provided by the API.

### API-GRP-001 — Browse groups

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-001`
**Actor:** member-portal users.

**Requirement:** A member browses groups they are allowed to discover or access.

---

### API-GRP-002 — View group details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-002`
**Actor:** member-portal users.

**Requirement:** A member opens a group record they are authorized to see.

---

### API-GRP-003 — Create group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-003`
**Actor:** Group managers and administrators.

**Requirement:** An eligible administrator or manager creates a group.

---

### API-GRP-004 — Update group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-004`
**Actor:** Group managers and administrators.

**Requirement:** An eligible administrator or manager changes the supported details of an existing group.

---

### API-GRP-005 — Browse group categories

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-005`
**Actor:** Authorized administrators.

**Requirement:** An administrator views the categories used to organize groups.

---

### API-GRP-006 — Manage group categories

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-006`
**Actor:** Authorized administrators.

**Requirement:** An administrator creates or maintains group categories using the supported operations.

---

### API-GRP-007 — Request to join group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-007`
**Actor:** Members and group managers.

**Requirement:** A member requests group membership where the group permits joining.

---

### API-GRP-008 — Leave group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-008`
**Actor:** Members and group managers.

**Requirement:** An existing member leaves a group where permitted.

---

### API-GRP-009 — Manage group membership

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-009`
**Actor:** Members and group managers.

**Requirement:** An eligible group manager reviews or changes group membership using the supported operations.

---

### API-GRP-010 — Find eligible group member candidates

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-010`
**Actor:** Group managers.

**Requirement:** An eligible group manager browses candidates for group membership.

---

### API-GRP-011 — Change group member role

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-011`
**Actor:** Group managers.

**Requirement:** An eligible group manager changes a member’s role subject to group role permissions.

## Group membership requirements

### API-GRP-012 — Restrict direct group joining to parents

**Requirement:** The direct group join operation rejects callers who fail parent eligibility requirements.

### API-GRP-013 — Restrict direct joining to active groups

**Requirement:** The direct group join operation rejects inactive or archived groups.

### API-GRP-014 — Restrict direct joining to public groups

**Requirement:** The direct group join operation rejects groups whose visibility is not public.

### API-GRP-015 — Reject banned group member rejoining

**Requirement:** The direct group join operation rejects an existing banned membership.

### API-GRP-016 — Reject duplicate active group membership

**Requirement:** The direct group join operation rejects an existing active membership.

### API-GRP-017 — Reactivate eligible former group membership

**Requirement:** The direct group join operation reactivates an existing nonactive, nonbanned membership and assigns the member role.

### API-GRP-018 — Validate member ID when adding group member

**Requirement:** The add-member operation requires a positive integer member identifier.

### API-GRP-019 — Validate role when adding group member

**Requirement:** The add-member operation accepts owner, admin, moderator or member as member role.

### API-GRP-020 — Authorize assigned group role

**Requirement:** A group manager can assign only roles they are permitted to grant.

### API-GRP-021 — Require active profile when adding group member

**Requirement:** The add-member operation rejects a target without an active member account.

### API-GRP-022 — Reject banned target on group add

**Requirement:** The add-member operation rejects a banned existing membership.

### API-GRP-023 — Reactivate eligible group membership on add

**Requirement:** The add-member operation reactivates an existing nonactive, nonbanned membership with the requested role.

## Group creation requirements

### API-GRP-033 — Restrict group creation to administrators

**Requirement:** The system requires group-administrator permission to create a group.

### API-GRP-034 — Require active group category

**Requirement:** If a category is supplied for a new group, the system rejects categories that do not exist or are inactive.

### API-GRP-026 — Validate initial group owner

**Requirement:** If an initial owner is supplied, the system requires an active member profile with an enabled member login.

### API-GRP-027 — Default new group visibility to public

**Requirement:** The system sets a new group to public visibility when visibility is omitted.

### API-GRP-028 — Create group as active

**Requirement:** The system stores newly created groups with active status.

### API-GRP-029 — Assign initial group owner

**Requirement:** When an initial owner is supplied, the system creates an active owner membership for that profile.

### API-GRP-030 — Restrict direct group joining to parents

**Requirement:** Only authenticated parent accounts can join groups directly.

### API-GRP-031 — Allow direct joining of active public groups

**Requirement:** Direct joining is rejected for inactive groups or groups whose visibility is not public.

### API-GRP-032 — Reject joining when banned

**Requirement:** A banned group member cannot join the group directly.

### API-GRP-024 — Reject duplicate active membership

**Requirement:** The system rejects joining a group when the requester already has an active membership.

### API-GRP-025 — Restore prior group membership

**Requirement:** When an eligible former member joins again, the system reactivates their existing membership with member role.
