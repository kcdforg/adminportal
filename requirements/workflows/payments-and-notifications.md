# Payments and Notifications — Cross-application workflow

**Workflow ID:** `WF-PAYMENTS-AND-NOTIFICATIONS`
**Status:** Draft; confirm payment lifecycle and notification-trigger policies.

## Purpose

Describe the separate workflows for recording and reviewing payments and for sending and receiving notifications.

## Participating requirements

- `API-PAY-001` through `API-PAY-004` — Browse, view, record, and update payments
- `API-NOT-001` through `API-NOT-006` — Read, manage, send, and broadcast notifications
- Admin portal payment and notification screens
- Member portal notification experience

## Payment journey

1. An authorized staff member selects the relevant family or payment context.
2. The staff member records or updates payment details.
3. The system validates the amount, date, method, and related record according to the agreed policy.
4. The user receives confirmation and can review the updated payment history.

## Notification journey

1. An authorized staff member selects recipients or a permitted audience.
2. The staff member composes and sends a direct or broadcast notification.
3. Recipients can view the notification and manage its read state where available.
4. The sender receives a clear result for the send action.

## Acceptance criteria

- Payment actions are restricted to users with the required permission.
- Payment records remain associated with the correct family or account context.
- Notifications are sent only to the intended and permitted audience.
- Failed payment or notification actions are clearly reported and do not appear successful.
- Payment recording and notification delivery are separate actions unless an approved business rule explicitly links them.

## Open questions

- Define payment methods, refund/correction rules, and whether payment edits require an audit reason.
- Confirm whether recording a payment should trigger a receipt or notification.
- Define notification channels, delivery expectations, and handling of failed delivery.
- Confirm who can view payment details and send notifications.
