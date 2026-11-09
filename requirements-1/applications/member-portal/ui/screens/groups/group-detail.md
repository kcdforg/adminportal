# Group Detail

This document describes the member-facing group detail screen.

## Status

Working draft. Confirm which actions are available to each membership role.

## UI-MEM-004-01 — View group information

**Requirement:** A member with access can review the selected group’s information and membership.

**Acceptance criteria:**
- The page identifies the group and shows its relevant description and membership state.
- If the group cannot be opened, the member sees a clear unavailable state and a way back to the group list.

## UI-MEM-004-02 — Access group tools

**Requirement:** Members and group managers see controls appropriate to their role.

**Acceptance criteria:**
- Members can access permitted actions such as joining, leaving, or registering for an event.
- Managers can access permitted membership and content-management actions.
- The screen does not imply that a restricted action is available to a member who lacks permission.

## UI-MEM-004-03 — Participate in group content

**Requirement:** A member can view and use the content tools available to the group.

**Acceptance criteria:**
- Posts, polls, and events are clearly distinguished.
- Poll options, event details, and registration or voting actions are understandable before submission.
- The screen communicates successful actions and explains unavailable or failed actions.

## UI-MEM-004-04 — Navigate group sections

**Requirement:** A member can move between the group’s information and content areas.

**Acceptance criteria:**
- Group sections have clear labels.
- The user can return to the group list without losing the ability to navigate.

## UI-MEM-004-05 — Show group loading and error states

**Requirement:** The screen communicates when group information is loading, unavailable, or empty.

**Acceptance criteria:**
- Loading feedback is shown while the group is retrieved.
- A missing or inaccessible group is not displayed as an empty valid group.
- Errors provide a clear recovery path.

**Previous screen-level ID:** `UI-MEM-004`
