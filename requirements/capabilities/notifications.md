# Module: Notifications

> **Module ID:** MOD-010  
> **Previous broad capability:** CAP-010 (retired as a capability ID; see [ID migration](./id-migration.md))

## Purpose

Keep users informed of relevant events.

## Scope

Reading, marking read and archiving own notifications; sending direct and broadcast notifications from the admin portal.

## Atomic business capabilities

The CAP IDs below identify coherent business abilities; detailed requirements remain authoritative in the linked application documents. Classification is documentation-level and is not a claim of complete source-code verification.

### CAP-045 — Notification creation and broadcasts

**Business outcome:** notification creation and broadcasts within the notifications module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-NOT-001` | Read own notifications | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) | <!-- Mapping requires review -->
| `API-NOT-003` | Archive notification | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) | <!-- Mapping requires review -->
| `API-NOT-004` | Send direct notification | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) | <!-- Mapping requires review -->
| `API-NOT-005` | Broadcast notifications | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| `API-NOT-006` | Support notification sending from the admin portal | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) | <!-- Mapping requires review -->
| `ADM-NOT-001` | Review notifications | Primary | [notifications.md](../applications/admin-portal/functional/notifications.md) | <!-- Mapping requires review -->
| `ADM-NOT-002` | Send a direct notification | Primary | [notifications.md](../applications/admin-portal/functional/notifications.md) | <!-- Mapping requires review -->
| `ADM-NOT-003` | Broadcast a notification | Primary | [notifications.md](../applications/admin-portal/functional/notifications.md) |
| Send Notification (admin) | `UI-ADM-022-01` … `UI-ADM-022-05` (5) | Primary | [send-notification.md](../applications/admin-portal/ui/screens/notifications/send-notification.md) |

### CAP-046 — Member notification inbox and status

**Business outcome:** member notification inbox and status within the notifications module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-NOT-002` | Mark notification as read | Primary | [notifications.md](../applications/api-backend/functional/notifications.md) |
| Notification List (admin) | `UI-ADM-021-01` … `UI-ADM-021-04` (4) | Primary | [notification-list.md](../applications/admin-portal/ui/screens/notifications/notification-list.md) |

### CAP-047 — Notification preferences and delivery

**Business outcome:** notification preferences and delivery within the notifications module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| — | No directly mapped requirement identified; verify proposed boundary | — | — |

## Related workflows and UI

### Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-PAYMENTS-AND-NOTIFICATIONS` | Primary | [payments-and-notifications.md](../workflows/payments-and-notifications.md) |



### UI screen coverage

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Notification List (admin) | `UI-ADM-021-01` … `UI-ADM-021-04` (4) | Primary | [notification-list.md](../applications/admin-portal/ui/screens/notifications/notification-list.md) |
| Send Notification (admin) | `UI-ADM-022-01` … `UI-ADM-022-05` (5) | Primary | [send-notification.md](../applications/admin-portal/ui/screens/notifications/send-notification.md) |



## Technical references



## Non-functional references

The following are historical module-level NFR relationships; see the [NFR index](../non-functional/index.md) for unchanged NFR definitions and approval statuses.



IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`
- **Proposed (not approved):** `NFR-REL-004`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).



## Implementation evidence and gaps

Source inspection and implementation-status statements below were carried forward from the previous module-level catalog. They must **not** be interpreted as independently verified for every atomic CAP.

### Prior module-level assessment

**Partially implemented.** In-app list/read/archive/send/broadcast operations exist. Types `push` and `email` are accepted but no delivery integration was found.



### Prior source evidence

- `api-backend-php/api/notifications/`; `services/community-notifications.php:425-439` (types); routes `/notifications*`.
- `kcdf-admin-app/src/app/features/notifications/`; `kcdf-parents-app/src/app/notifications/notifications.page.ts:32,45`. Database: `notifications`.



### Known gaps

- Member notifications screen has no requirement (documentation gap).
- Delivery channels, retries and opt-out are undefined (proposed `NFR-REL-004`).
- Audience rules for broadcast ("approved audience", `ADM-NOT-003`) are not defined.


## Mapping review notes

- Automated classification assigned 6 requirement/screen rows to a provisional CAP because the title did not clearly identify a sub-capability. Review rows marked `Mapping requires review`.
- Shared workflows, UI screens, and NFRs are shown at module level; do not assume every reference applies to every atomic CAP.
