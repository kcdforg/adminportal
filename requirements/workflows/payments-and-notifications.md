# Payments And Notifications — Cross-application workflow

**Workflow ID:** `WF-PAYMENTS-AND-NOTIFICATIONS`
**State:** Source-supported backend segments identified; complete end-to-end behavior not verified.

## Purpose

Coordinate the relevant application capabilities for payments and notifications without treating an API endpoint as proof of a complete user journey.

## Participating requirement references

- `API-PAY-001` (api-backend) — Browse payments
- `API-PAY-002` (api-backend) — View payment details
- `API-PAY-003` (api-backend) — Record payment
- `API-PAY-004` (api-backend) — Update payment
- `API-NOT-001` (api-backend) — Read own notifications
- `API-NOT-002` (api-backend) — Mark notification as read
- `API-NOT-003` (api-backend) — Archive notification
- `API-NOT-004` (api-backend) — Send direct notification
- `API-NOT-005` (api-backend) — Broadcast notifications
- `API-NOT-006` (api-backend) — Notification dispatch integration discrepancy (source-observed)

## Source-supported backend operations

The following operations have matching PHP handler files. This establishes backend entry points, **not** successful end-to-end user journeys or the precise order in which users encounter them.

1. **Record payment.** Source: `api-backend-php/api/payments/store.php`.
2. **Update payment.** Source: `api-backend-php/api/payments/update.php`.
3. **Read payment.** Source: `api-backend-php/api/payments/show.php`.

## End-to-end verification gaps

- Confirm initiating screen and user action against its template and component logic.
- Trace the frontend service URL and request payload to the registered PHP route.
- Confirm response mapping, user-visible success and failure behavior, and persisted database changes.
- Execute a running-system test before marking the workflow verified.

**Important:** Payment recording and notification dispatch are separate capabilities. Their automatic linkage is **not established** by these references; do not describe payment as automatically triggering notification.

**Evidence classification:** PHP handler file presence checked against the supplied source ZIP; operation labels are descriptive. Authorization branches and end-to-end results require per-handler and runtime verification.
