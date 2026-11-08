# Group Detail

This document describes the expected user-facing behavior of this screen or component.

## Status

Working draft. Confirm the behavior and any role or policy rules with stakeholders before approval.

## UI-ADM-015-01 — Back: Screen structure

**Requirement:** The back screen presents the task with a clear identifying heading.

**Acceptance criteria:**
- The screen displays its main content with a clear heading (Back).

**Previous screen-level ID:** `UI-ADM-015`

## UI-ADM-015-02 — Back: Input controls

**Requirement:** The back screen provides clearly labeled controls appropriate to the task.

**Acceptance criteria:**
- The screen provides controls for the information needed to complete the task: Member Moderator Admin Owner (select); groupName (input); displayName (input); categoryId (select); visibility (select); description (textarea); status (select); memberCandidateSearch (input); newMemberId (select); newMemberRole (select); title (input); content (textarea); options (textarea); allowMultiple (input); allowVoteChange (input); showResults (input); closesAt (input); startAt (input); endAt (input); location (input); capacity (input).

**Previous screen-level ID:** `UI-ADM-015`

## UI-ADM-015-03 — Back: User actions

**Requirement:** The user can complete the actions available on this screen.

**Acceptance criteria:**
- The screen clearly offers the available actions: Back; Edit group details; Members (); Posts; Events; Polls; Add member; Add post; Add event; Add poll; Ban member; Remove member; Edit; Archive; Close; Cancel; Save details; Search.

**Previous screen-level ID:** `UI-ADM-015`

## UI-ADM-015-04 — Back: Navigation

**Requirement:** The user can navigate to related screens from this screen.

**Acceptance criteria:**
- The user can navigate to the relevant area (/groups).

**Previous screen-level ID:** `UI-ADM-015`

## UI-ADM-015-05 — Back: Conditional UI states

**Requirement:** The back screen communicates loading, empty, error, and unavailable states when they apply.

**Acceptance criteria:**
- The screen shows progress while data is loading; explains when data cannot be loaded or an action fails; explains when there is no content to display; indicates when an action is unavailable; provides a clear way to move to related screens.

**Previous screen-level ID:** `UI-ADM-015`
