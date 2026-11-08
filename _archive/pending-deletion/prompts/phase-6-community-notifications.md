# Phase 6 — Community + Notifications + Audit Logs

## Context

You are continuing development of `kcdf-api-backend` (Slim Framework 4).
Phases 1–5 are complete.

This phase implements:
1. Community module (parent groups, group members, invitations)
2. Notifications module (in-app notifications, send/broadcast, mark as read)
3. Activity Logs (admin-only audit log viewing — logging itself is already used throughout the app)

---

## Module Locations

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
├── Policies/
│   ├── GroupPolicy.php
│   └── InvitationPolicy.php
└── routes.php

src/Modules/Notifications/
├── Controllers/
│   ├── NotificationController.php
│   └── ActivityLogController.php
├── Services/
│   ├── NotificationService.php
│   └── ActivityLogService.php       ← already used by other modules
├── Repositories/
│   ├── NotificationRepository.php
│   └── ActivityLogRepository.php
├── Models/
│   ├── Notification.php
│   └── ActivityLog.php
├── DTOs/
│   ├── SendNotificationDTO.php
│   └── BroadcastNotificationDTO.php
└── routes.php
```

---

## Community Endpoints

### Groups
- GET /api/v1/groups — Authenticated user. Admin sees all. Parents see public + groups they're in.
- POST /api/v1/groups — Admin only
- GET /api/v1/groups/{id} — Admin or group member (for private/invite_only)
- PUT /api/v1/groups/{id} — Admin only
- GET /api/v1/groups/{id}/members — Admin or group member
- POST /api/v1/groups/{id}/join — Authenticated parent. Public groups only.
- DELETE /api/v1/groups/{id}/leave — Authenticated group member
- DELETE /api/v1/groups/{id}/members/{member_id} — Admin only (ban or remove)

### Invitations
- GET /api/v1/invitations — Authenticated sender (own). Admin sees all.
- POST /api/v1/invitations — Any authenticated parent
- GET /api/v1/invitations/{code} — Public (no auth required, for accept flow)
- POST /api/v1/invitations/{code}/accept — Public (for new user registration via invite)
- DELETE /api/v1/invitations/{id} — Sender or admin (cancel pending only)

---

## Notification Endpoints

- GET /api/v1/notifications — Own notifications, authenticated user. Filter: status, type.
- PATCH /api/v1/notifications/{id}/read — Own notifications only
- POST /api/v1/notifications/read-all — Marks all unread as read for authenticated user
- PATCH /api/v1/notifications/{id}/archive — Own notifications only
- POST /api/v1/notifications/send — Admin only. Send to specific member_ids.
- POST /api/v1/notifications/broadcast — Admin only. Send to all members of a batch or group.

## Activity Log Endpoints

- GET /api/v1/activity-logs — super_admin only. Filter: actor_profile_id, entity_type, entity_id, action, created_at_from, created_at_to.

---

## Community Business Rules

### Groups
1. Public groups are visible and joinable by any authenticated parent.
2. Private and invite_only groups are visible only to members and admins.
3. A banned member (status = banned) cannot rejoin — return 403.
4. A member who left can rejoin a public group — set status back to active on the existing record.
5. On join: check for existing group_member record and upsert rather than insert duplicate.
6. Archived groups reject new join attempts.

### Invitations
7. Either invite_mobile or invite_email must be provided (or both).
8. invite_code: generate a unique random 12-character alphanumeric code.
9. Invitations expire after 7 days — check sent_at + 7 days vs now when accepting.
10. Expired invitations cannot be accepted — return INVALID_INVITE_CODE error.
11. Accepted invitations cannot be accepted again.
12. The accept endpoint creates a new member_profiles record + user_logins record + issues JWT tokens.
13. Accept request body: { first_name, last_name, mobile, email, password } (all required).

---

## Notification Business Rules

14. Notifications are created per-member — one row per member per notification.
15. For broadcast: create one notifications row per member in the target batch or group.
16. read_at is set when status changes from unread to read.
17. Archived notifications remain in the DB — never deleted.

---

## Validation Rules

Groups:
- group_name: required, string, max 255
- visibility: required, one of: public, private, invite_only

Invitations:
- invite_mobile: optional, digits only, min 10 chars
- invite_email: optional, valid email
- At least one of invite_mobile or invite_email must be present

Accept invitation:
- first_name: required, string, max 100
- last_name: required, string, max 100
- mobile: required, digits, min 10
- email: required, valid email
- password: required, min 8 chars

Notifications (send):
- member_ids: required array of valid member profile IDs
- title: required, max 255
- message: required, max 2000
- type: required, one of: in_app, push, email

Broadcast:
- target_type: required, one of: batch, group, all_families
- target_id: required when target_type is batch or group
- title, message, type: same as send

---

## ActivityLogService (finalize)

`ActivityLogService` should be registered in the DI container as a shared singleton.
All other services (FamilyService, PaymentService, etc.) have it injected and call:

```php
$this->activityLog->log(
    actorProfileId: $this->currentUser->profileId,
    action: 'group_joined',
    entityType: 'parent_groups',
    entityId: $groupId,
    oldValues: null,
    newValues: ['member_id' => $memberId]
);
```

The `log()` method writes synchronously to `activity_logs`. No queue in Phase 6.

---

## Rules

- Register all routes in module routes.php files
- All access control in Policy classes
- Services throw typed exceptions
- No raw SQL
