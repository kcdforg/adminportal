# CAP-010: Notifications

## 1. Purpose

Keep users informed of relevant events.

## 2. Scope

Reading, marking read and archiving own notifications; sending direct and broadcast notifications from the admin portal.

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-NOT-001` | Read own notifications | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| `API-NOT-002` | Mark notification as read | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| `API-NOT-003` | Archive notification | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| `API-NOT-004` | Send direct notification | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| `API-NOT-005` | Broadcast notifications | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| `API-NOT-006` | Support notification sending from the admin portal | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-NOT-001` | Review notifications | Primary | [notifications.md](../applications/admin-portal/functional/notifications.md) |
| `ADM-NOT-002` | Send a direct notification | Primary | [notifications.md](../applications/admin-portal/functional/notifications.md) |
| `ADM-NOT-003` | Broadcast a notification | Primary | [notifications.md](../applications/admin-portal/functional/notifications.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-PAYMENTS-AND-NOTIFICATIONS` | Primary | [payments-and-notifications.md](../workflows/payments-and-notifications.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Notification List (admin) | `UI-ADM-021-01` … `UI-ADM-021-04` (4) | Primary | [notification-list.md](../applications/admin-portal/ui/screens/notifications/notification-list.md) |
| Send Notification (admin) | `UI-ADM-022-01` … `UI-ADM-022-05` (5) | Primary | [send-notification.md](../applications/admin-portal/ui/screens/notifications/send-notification.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`
- **Proposed (not approved):** `NFR-REL-004`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-007 Payments](./CAP-007-payments.md) — combined workflow
- [CAP-009 Group Content (Posts, Events, Polls)](./CAP-009-group-content.md) — content could notify members (undecided)

## 10. Implementation Status

**Partially implemented.** In-app list/read/archive/send/broadcast operations exist. Types `push` and `email` are accepted but no delivery integration was found.

## 11. Source-Code Evidence

- `api-backend-php/api/notifications/`; `services/community-notifications.php:425-439` (types); routes `/notifications*`.
- `kcdf-admin-app/src/app/features/notifications/`; `kcdf-parents-app/src/app/notifications/notifications.page.ts:32,45`. Database: `notifications`.

## 12. Known Gaps

- Member notifications screen has no requirement (documentation gap).
- Delivery channels, retries and opt-out are undefined (proposed `NFR-REL-004`).
- Audience rules for broadcast ("approved audience", `ADM-NOT-003`) are not defined.
