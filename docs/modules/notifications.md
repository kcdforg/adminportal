# Notifications Module

## Scope

Manages in-app notifications, push notifications, email notifications, and the system-wide audit log.

---

## Tables

- `notifications` — per-member notification records
- `activity_logs` — append-only audit trail of all system mutations

See `docs/01-database.md` for full schema with MySQL types.

---

## Business Rules

### Notifications
1. A notification is always addressed to a specific `member_id`.
2. Notification types: `in_app`, `push`, `email`.
3. `in_app` notifications are stored in the DB and fetched by the client.
4. `push` notifications are delivered via a push provider and also stored in DB for reference.
5. `email` notifications are sent via an email provider and stored in DB for reference.
6. A member can only read and archive their own notifications.
7. `status` transitions: `unread → read` (on view) or `unread/read → archived` (on dismiss).
8. `read_at` is set when status changes to `read`.
9. Notifications are never deleted — only archived.
10. Bulk mark-all-as-read is supported.
11. Admin can send notifications to individual members or broadcast to a group.
12. The notification `type` field records the delivery channel at creation time.

### Activity Logs
13. Every create, update, and delete (status change) on any core entity must create an `activity_logs` record.
14. `actor_profile_id` is the authenticated user's profile_id. NULL for system-generated actions.
15. `action` values: `created`, `updated`, `status_changed`, `deleted`, `login`, `logout`, `attendance_marked`, `payment_recorded`, etc.
16. `entity_type` is the table name (e.g., `families`, `enrollments`, `payments`).
17. `entity_id` is the primary key of the affected record.
18. `old_values` and `new_values` are JSON snapshots of the changed fields only (not full records).
19. Activity logs are append-only. They are never updated or deleted.
20. Only `super_admin` can view activity logs via the admin portal.
21. Activity logs are not exposed via the parent app.

---

## Access Control Matrix

| Action | family_primary | family_normal | family_student | trainer | admin |
|---|---|---|---|---|---|
| View own notifications | Yes | Yes | Yes | Yes | Yes |
| Mark own notification as read | Yes | Yes | Yes | Yes | Yes |
| Archive own notification | Yes | Yes | Yes | Yes | Yes |
| Send notification to member | No | No | No | No | Yes |
| Broadcast notification to group | No | No | No | No | Yes |
| View activity logs | No | No | No | No | super_admin |

---

## API Endpoints

### Notifications

#### GET /api/v1/notifications
Returns notifications for the authenticated user, most recent first.

**Query params:** `status`, `type`, `page`, `per_page`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 10,
      "title": "Batch session scheduled",
      "message": "Vedic Maths Batch A - Session 5 is scheduled for 10 Jan 2026.",
      "type": "in_app",
      "status": "unread",
      "read_at": null,
      "created_at": "2026-01-08T09:00:00Z"
    }
  ],
  "meta": { "total": 12, "per_page": 20, "current_page": 1, "last_page": 1 }
}
```

#### PATCH /api/v1/notifications/{id}/read
Marks a specific notification as read. Authenticated user, own notifications only.

**Response 200:**
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

#### POST /api/v1/notifications/read-all
Marks all unread notifications as read for the authenticated user.

#### PATCH /api/v1/notifications/{id}/archive
Archives a notification. Authenticated user, own notifications only.

#### POST /api/v1/notifications/send
Admin only. Sends a notification to one or more members.

**Request:**
```json
{
  "member_ids": [5, 12, 18],
  "title": "Fee payment reminder",
  "message": "Please clear your pending class fee before Jan 31, 2026.",
  "type": "in_app"
}
```

#### POST /api/v1/notifications/broadcast
Admin only. Sends a notification to all members of a group or batch.

**Request:**
```json
{
  "target_type": "batch",
  "target_id": 3,
  "title": "Session cancelled",
  "message": "Tomorrow's Vedic Maths session is cancelled due to a public holiday.",
  "type": "in_app"
}
```

`target_type` values: `batch`, `group`, `all_families`

---

### Activity Logs

#### GET /api/v1/activity-logs
Admin (super_admin) only.

**Query params:** `actor_profile_id`, `entity_type`, `entity_id`, `action`, `created_at_from`, `created_at_to`, `page`, `per_page`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 250,
      "actor_profile_id": 1,
      "actor_name": "Admin User",
      "action": "payment_recorded",
      "entity_type": "payments",
      "entity_id": 42,
      "old_values": null,
      "new_values": {
        "amount": 1500.00,
        "payment_type": "class_fee",
        "status": "completed"
      },
      "created_at": "2026-01-15T10:30:00Z"
    }
  ],
  "meta": { "total": 1200, "per_page": 50, "current_page": 1, "last_page": 24 }
}
```

---

## Audit Logging Implementation

All Services must call `ActivityLogService::log()` after any mutation.

```php
// Example in PaymentService
$this->activityLogService->log(
    actor: $currentProfileId,
    action: 'payment_recorded',
    entityType: 'payments',
    entityId: $payment->id,
    oldValues: null,
    newValues: $payment->toArray()
);
```

`ActivityLogService` writes to `activity_logs` synchronously. In a future iteration, this can be moved to a queue worker.

---

## Validation Rules

| Field | Rule |
|---|---|
| `title` | required, string, max 255 |
| `message` | required, string, max 2000 chars |
| `type` | required, must be: in_app, push, email |
| `member_ids` | required for send, array of valid member IDs |
| `target_type` | required for broadcast: batch, group, all_families |
| `target_id` | required when target_type is batch or group |

---

## Module Folder Structure

```
src/Modules/Notifications/
├── Controllers/
│   ├── NotificationController.php
│   └── ActivityLogController.php
├── Services/
│   ├── NotificationService.php
│   └── ActivityLogService.php
├── Repositories/
│   ├── NotificationRepository.php
│   └── ActivityLogRepository.php
├── Models/
│   ├── Notification.php
│   └── ActivityLog.php
├── DTOs/
│   ├── SendNotificationDTO.php
│   └── BroadcastNotificationDTO.php
└── Validators/
    └── NotificationValidator.php
```
