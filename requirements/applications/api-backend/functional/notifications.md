# Notifications


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-NOT-001 — Read own notifications

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-001`
**Actor:** Authenticated users.

**Requirement:** An authenticated user retrieves notifications belonging to their account.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/notifications/index.php:7`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/notifications/index.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/notifications/index.php`, `api-backend-php/api/notifications/mark-read.php`, `api-backend-php/api/notifications/read-all.php`, `api-backend-php/api/notifications/archive.php`, `kcdf-admin-app/src/app/features/notifications/`, `kcdf-admin-app/src/app/core/services/notification.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-NOT-002 — Mark notification as read

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-002`
**Actor:** Authenticated users.

**Requirement:** An authenticated user marks an eligible notification as read.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/notifications/index.php:7`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/notifications/index.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/notifications/index.php`, `api-backend-php/api/notifications/mark-read.php`, `api-backend-php/api/notifications/read-all.php`, `api-backend-php/api/notifications/archive.php`, `kcdf-admin-app/src/app/features/notifications/`, `kcdf-admin-app/src/app/core/services/notification.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-NOT-003 — Archive notification

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-003`
**Actor:** Authenticated users.

**Requirement:** An authenticated user archives an eligible notification.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/notifications/index.php:7`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Notification not found.', 404);` at `api-backend-php/api/notifications/mark-read.php:12`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('notifications', ['status' => 'read', 'read_at' => date('Y-m-d H:i:s')], $where);` at `api-backend-php/api/notifications/mark-read.php:15`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/notifications/index.php:11`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/notifications/index.php`, `api-backend-php/api/notifications/mark-read.php`, `api-backend-php/api/notifications/read-all.php`, `api-backend-php/api/notifications/archive.php`, `kcdf-admin-app/src/app/features/notifications/`, `kcdf-admin-app/src/app/core/services/notification.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-NOT-004 — Send direct notification

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-004`
**Actor:** Authorized notification senders.

**Requirement:** An authorized sender can submit a direct notification through the backend send operation.

**Source-supported acceptance checks (static code inspection; runtime untested):**
- The registered send endpoint is `POST /api/v1/notifications/send` (`api-backend-php/api/routes.php`, line 121).
- The send handler requires an authenticated elevated administrator and validates the request with `validateNotification(..., "send")`.
- The handler inserts one notification per submitted `member_ids` entry with `title`, `message`, `type` and `status = unread`, then returns `sent_to`.
- The Angular notification service instead posts to `/notifications`, so its send operation does not target the registered send route.

**Source references:**
- `api-backend-php/api/notifications/send.php`, `kcdf-admin-app/src/app/features/notifications/`, `kcdf-admin-app/src/app/core/services/notification.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-NOT-005 — Broadcast notifications

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-005`
**Actor:** Privileged notification senders.

**Requirement:** A privileged sender may submit a notification broadcast to a selected audience through the backend broadcast endpoint.

**Source-supported acceptance checks (static code inspection; runtime untested):**
- The registered broadcast endpoint is `POST /api/v1/notifications/broadcast` (`api-backend-php/api/routes.php`, line 122).
- The broadcast handler requires an authenticated elevated administrator and validates `target_type` and other fields through `validateNotification(..., "broadcast")`.
- The handler derives active recipients from batch, group, or family membership for supported target types, deduplicates IDs and inserts unread notifications.
- The inspected Angular notification service does not call the broadcast endpoint.

**Source references:**
- `api-backend-php/api/notifications/broadcast.php`, `kcdf-admin-app/src/app/features/notifications/`, `kcdf-admin-app/src/app/core/services/notification.service.ts`. ### Stage 10 integration audit: INT-006 The Angular service sends `POST /notifications`, while `api/routes.php` registers `POST /api/v1/notifications/send or /broadcast`. Confirmed invoked by send form; payload may also differ. This is an **integration discrepancy**, not a successful runtime test. See `requirements/audits/reachable-frontend-api-gaps.md`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-NOT-006 — Notification dispatch integration discrepancy (source-observed)

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-006`
**Actor:** Developers and testers.

**Requirement:** The notification-dispatch integration shall be tracked as an identified gap rather than described as a completed user workflow.

**Source-supported discrepancy checks (static code inspection; runtime untested):**
- Angular `notification.service.ts` calls `POST /notifications` while the backend registers `POST /api/v1/notifications/send` and `POST /api/v1/notifications/broadcast`, not `POST /api/v1/notifications`.
- Angular `send-notification.component.ts` submits `body` and `channel`, whereas the backend send handler consumes `message`, `type` and `member_ids` after validation.
- Treat notification dispatch from this Angular form as an unresolved integration discrepancy; no runtime outcome is asserted.

**Source references:**
- `kcdf-admin-app/src/app/core/services/notification.service.ts`
- `kcdf-admin-app/src/app/features/notifications/send-notification/send-notification.component.ts`
- `api-backend-php/api/routes.php`
- `api-backend-php/services/community-notifications.php`. ## End-to-end notification qualification (F05) The PHP API registers `POST /api/v1/notifications/send` and `POST /api/v1/notifications/broadcast`, but the current Angular notification service sends `POST /notifications` relative to its API base. The Angular form submits `title`, `body`, `channel`, and optional target fields; the PHP handlers require `title`, `message`, `type` and mode-specific `member_ids` or target fields. **The current Angula

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).