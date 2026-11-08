# Post Detail

This document describes the member-facing group post detail screen.

## Status

Working draft. Confirm whether posts support attachments or other interactions.

## UI-MEM-009-01 — Read a post

**Requirement:** A member with access can open a post and read its available content.

**Acceptance criteria:**
- The page identifies the post and displays its content and relevant publication information.
- Content is shown only when the member has access to its group.
- An unavailable post is not displayed as if it were current.

## UI-MEM-009-02 — Return to posts

**Requirement:** A member can return to the post list from the detail screen.

**Acceptance criteria:**
- The return action is clearly labeled.
- The member can return to the list after viewing a post or seeing an unavailable state.

## UI-MEM-009-03 — Navigate to the post list

**Requirement:** A member can navigate back to the post list from the detail screen.

**Acceptance criteria:**
- The destination is clearly identified.
- The member can return to the list after viewing a post or seeing an unavailable state.

## UI-MEM-009-04 — Handle post loading and unavailable states

**Requirement:** The screen explains when a post is loading or cannot be shown.

**Acceptance criteria:**
- Loading feedback is shown while the post is retrieved.
- Missing, archived, or inaccessible posts have a clear message.
- The screen provides a safe way back to the post list.

**Previous screen-level ID:** `UI-MEM-009`
