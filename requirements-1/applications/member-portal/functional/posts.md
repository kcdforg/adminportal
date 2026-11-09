# Group Posts

This document describes how members read announcements and other posts shared with their groups.

## Status

Working draft. Confirm which post types and interaction options are in scope.

### MEM-POST-001 — Browse group posts

**Actor:** Members with access to a group.

**Requirement:** A member can browse posts available to their groups.

**Acceptance criteria:**
- Posts are associated with the correct group and shown in a clear order.
- Each post preview identifies its subject and relevant publication information.
- Empty and loading states are clear.
- Posts from inaccessible groups are not shown.

### MEM-POST-002 — Read a post

**Actor:** Members with access to the post’s group.

**Requirement:** A member can open a post and read its available content.

**Acceptance criteria:**
- The detail view displays the full available post content and relevant metadata.
- A post that is unavailable or removed is not shown as if it were current.
- The member can return to the post list.

## Open questions

- Confirm whether posts support attachments, comments, reactions, or pinned content.
- Define how archived or edited posts are presented.
