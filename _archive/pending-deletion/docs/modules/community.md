# Community Module

## Scope

Manages parent community groups, group membership, and parent-to-parent invitations.

---

## Tables

- `parent_groups` — community group definition
- `group_members` — group membership records
- `group_contents` — announcements, polls, and events
- `group_poll_votes` — a member's current poll choices
- `group_event_registrations` — private event registration records
- `invitations` — parent-to-parent invitations

See `docs/01-database.md` for full schema with MySQL types.

---

## Business Rules

### Parent Groups
1. Any authenticated parent (family_primary, family_normal) can view public groups.
2. Private and invite_only groups are visible only to their members and admins.
3. Only admins can create or archive groups.
4. A group can be `public` (anyone can join), `private` (admin-managed membership), or `invite_only` (join by invitation).
5. Group status transitions: `active → inactive` or `active → archived`.
6. Archived groups are read-only — no new members can join.

### Group Members
7. A member can join any `public` group by themselves.
8. Joining a `private` or `invite_only` group requires admin approval or a valid invitation.
9. A member can leave a group at any time (status set to `left`).
10. A member can be banned from a group by an admin (`status = banned`). Banned members cannot rejoin.
11. Unique constraint: one record per (group_id, member_id).
12. Rejoining after leaving is allowed (a new record is created or the existing record's status is reset to `active`).

### Invitations
13. Any authenticated parent (family_primary, family_normal) can send an invitation.
14. An invitation can be sent to a mobile number or email address (at least one is required).
15. `invite_code` is auto-generated (UUID or random alphanumeric), unique.
16. Invitations expire after 7 days if not accepted.
17. An accepted invitation (`status = accepted`) cannot be accepted again.
18. A cancelled invitation (`status = cancelled`) cannot be accepted.
19. When an invitation is accepted, the invited person is registered or linked to the platform.
20. One active invitation per (invited_by_member_id + invite_mobile/invite_email) combination — prevent spam.

---

## Access Control Matrix

| Action | family_primary | family_normal | family_student | trainer | admin |
|---|---|---|---|---|---|
| View public groups | Yes | Yes | No | No | Yes |
| View private/invite_only groups | Members only | Members only | No | No | Yes |
| Join public group | Yes | Yes | No | No | Yes |
| Join private group | Admin-approved | Admin-approved | No | No | Admin |
| Leave group | Yes (own) | Yes (own) | No | No | Yes |
| Ban member from group | No | No | No | No | Yes |
| Create group | No | No | No | No | Yes |
| Edit group | No | No | No | No | Yes |
| Archive group | No | No | No | No | Yes |
| Send invitation | Yes | Yes | No | No | Yes |
| Accept invitation | Invitee | Invitee | No | No | Yes |
| Cancel invitation | Sender or admin | No | No | No | Yes |
| View own invitations sent | Yes | Yes | No | No | Yes |

---

## API Endpoints

### Groups

#### GET /api/v1/groups
Returns groups visible to the authenticated user.
- Admin: all groups
- Parent: public groups + groups they are a member of

**Query params:** `visibility`, `status`, `page`, `per_page`

#### POST /api/v1/groups
Admin only.

**Request:**
```json
{
  "group_name": "KCDF Parents 2026",
  "display_name": "KCDF Parents",
  "description": "General discussion group for all KCDF parents",
  "category_id": 1
}
```
Only `group_name` is required. `display_name`, `description`, and `category_id`
are optional; visibility defaults to `public`. A group can be created without
an initial owner, who can be assigned later through member management.

#### GET /api/v1/groups/{id}
Visible to members or admin. Public groups are visible to all authenticated parents.

#### PUT /api/v1/groups/{id}
Admin only.

#### GET /api/v1/groups/{id}/members
Admin or any member of the group.

#### POST /api/v1/groups/{id}/join
Authenticated parent. Only allowed for `public` groups.

**Response 201:**
```json
{
  "success": true,
  "message": "Joined group successfully"
}
```

**Errors:**
- 403 — group is private or invite_only
- 409 — already a member of this group
- 403 — member is banned from this group

#### DELETE /api/v1/groups/{id}/leave
Authenticated parent. Leaves the group (sets status to `left`).

#### DELETE /api/v1/groups/{id}/members/{member_id}
Admin only. Ban or remove a member.

**Request:**
```json
{
  "action": "ban"
}
```

### Group content

Content is exposed through resource-specific routes; all resources are scoped
to a group and use the shared `group_contents` table.

| Resource | Routes |
|---|---|
| Announcements | `GET/POST /api/v1/groups/{group_id}/posts`, `GET/PUT/DELETE /api/v1/groups/{group_id}/posts/{post_id}` |
| Polls | `GET/POST /api/v1/groups/{group_id}/polls`, `GET/PUT/DELETE /api/v1/groups/{group_id}/polls/{poll_id}`, `POST .../{poll_id}/vote`, `GET .../{poll_id}/results` |
| Events | `GET/POST /api/v1/groups/{group_id}/events`, `GET/PUT/DELETE /api/v1/groups/{group_id}/events/{event_id}`, create/read-own/cancel registration under `.../{event_id}/registrations` |

Only group owners, admins, moderators, and platform admins can create content.
Content is published by default; managers can create drafts and archive
content. Polls support multiple-choice rules, vote changes, and configurable
result visibility. A poll cannot be edited after the first vote. Events
support optional capacity limits; attendees can read or cancel only their own
registration. Group members see aggregate registration counts, never attendee
identities.

Apply `api-backend-php/database/migrations/20261023_group_content.sql` to an
existing MySQL database before deploying the new content routes. New
installations receive these tables from `database/schema.sql`.

---

### Invitations

#### GET /api/v1/invitations
Returns invitations sent by the authenticated user. Admin sees all.

**Query params:** `status`, `page`, `per_page`

#### POST /api/v1/invitations
Any authenticated parent.

**Request:**
```json
{
  "invite_mobile": "9876543210",
  "invite_email": "friend@example.com"
}
```

**Response 201:**
```json
{
  "success": true,
  "data": {
    "id": 5,
    "invite_code": "KCDF-INV-ABC123",
    "invite_mobile": "9876543210",
    "invite_email": "friend@example.com",
    "status": "pending",
    "sent_at": "2026-07-03T10:00:00Z"
  }
}
```

**Errors:**
- 422 — neither mobile nor email provided
- 409 — active invitation already sent to this mobile/email by same sender

#### GET /api/v1/invitations/{code}
Public endpoint. Returns invitation details by invite_code (for the accept flow).

**Response:**
```json
{
  "success": true,
  "data": {
    "invite_code": "KCDF-INV-ABC123",
    "invited_by": "John Doe",
    "status": "pending",
    "expires_at": "2026-07-10T10:00:00Z"
  }
}
```

**Errors:**
- 404 — invitation not found
- 422 — invitation is expired or already accepted

#### POST /api/v1/invitations/{code}/accept
Public endpoint (used during registration flow).

**Request:**
```json
{
  "first_name": "Jane",
  "last_name": "Doe",
  "mobile": "9876543210",
  "email": "jane@example.com",
  "password": "SecurePass123"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Invitation accepted. Account created successfully.",
  "data": {
    "access_token": "eyJ...",
    "refresh_token": "eyJ..."
  }
}
```

#### DELETE /api/v1/invitations/{id}
Sender or admin. Cancels a pending invitation.

---

## Validation Rules

| Field | Rule |
|---|---|
| `group_name` | required, string, max 255 |
| `display_name` | optional, string or null, max 255 |
| `description` | optional, string or null |
| `category_id` | optional, active category id or null |
| `visibility` | optional, must be: public, private, invite_only; defaults to public |
| `invite_mobile` | optional, digits only, min 10 digits |
| `invite_email` | optional, valid email format |
| At least one of invite_mobile or invite_email | required when creating invitation |
| `action` (ban/remove) | required, must be: ban, remove |

---

## Module Folder Structure

```
src/Modules/Community/
├── Controllers/
│   ├── GroupController.php
│   └── InvitationController.php
├── Services/
│   ├── GroupService.php
│   └── InvitationService.php
├── Repositories/
│   ├── GroupRepository.php
│   ├── GroupMemberRepository.php
│   └── InvitationRepository.php
├── Models/
│   ├── ParentGroup.php
│   ├── GroupMember.php
│   └── Invitation.php
├── DTOs/
│   ├── CreateGroupDTO.php
│   └── CreateInvitationDTO.php
├── Validators/
│   ├── GroupValidator.php
│   └── InvitationValidator.php
└── Policies/
    ├── GroupPolicy.php
    └── InvitationPolicy.php
```
