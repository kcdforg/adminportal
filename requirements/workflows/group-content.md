# Group Content — Cross-application workflow

**Workflow ID:** `WF-GROUP-CONTENT`
**Status:** Draft; confirm which group roles can publish, edit, and archive content.

## Purpose

Describe how group members view and interact with announcements, polls, and events.

## Participating requirements

- `API-GRP-001` through `API-GRP-011` — Discover groups and manage group information and membership
- `API-POST-001` through `API-POST-005` — View, create, edit, and archive group announcements
- `API-POL-001` through `API-POL-004` — Create, publish, vote in, and review group polls
- `API-EVT-001` through `API-EVT-005` — Create, update, and manage event registrations
- Member portal group, post, and post-detail screens

## User journey

1. A member opens a group they are permitted to access.
2. The member views the group’s announcements, polls, and events.
3. The member opens an item to read its details and, when applicable, votes or registers for an event.
4. An authorized group manager creates or updates content and can archive items that are no longer active.
5. The system shows the resulting content or registration state and provides clear feedback when an action is unavailable.

## Acceptance criteria

- Members can view content only for groups they are allowed to access.
- Content is clearly identified by type, status, and relevant dates.
- Members cannot vote or register when an item is closed or otherwise ineligible.
- Only authorized roles can create, edit, publish, or archive group content.
- A user receives a clear result after each content action.

## Open questions

- Define who may manage content in each group and whether approval is required before publication.
- Define poll closing, vote-change, event capacity, and registration-cancellation rules.
- Confirm notification expectations when new content is published or changed.
