# Groups

This document describes how members discover groups and participate in groups they can access.

## Status

Working draft. Confirm group discovery, join, and privacy rules.

### MEM-GRP-001 — Browse groups

**Actor:** Signed-in members.

**Requirement:** A member can browse groups that are visible or relevant to them.

**Acceptance criteria:**
- The member can distinguish groups they belong to from groups they may join or request to join.
- Group summaries provide enough information to identify each group.
- Private or restricted group details are not disclosed to ineligible members.

### MEM-GRP-002 — View group details

**Actor:** Members with access to the selected group.

**Requirement:** A member can open an accessible group and review its details and available content.

**Acceptance criteria:**
- The detail page shows the group’s relevant information and membership state.
- The member can access only content permitted by the group’s visibility rules.
- Unavailable groups and failed requests produce clear feedback.

### MEM-GRP-003 — Manage own group membership

**Actor:** Signed-in members.

**Requirement:** A member can request to join or leave a group when the group’s policy permits the action.

**Acceptance criteria:**
- The available action matches the member’s current membership status.
- Pending requests and completed changes are clearly distinguished.
- Duplicate or disallowed requests are prevented with a useful explanation.

## Open questions

- Define open, approval-based, and invitation-only membership behavior.
- Confirm whether members may leave at any time and what content access changes afterward.
