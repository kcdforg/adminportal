# Payments and communications requirements

**Status:** Provisional as-is reconstruction from current source. Client mismatches and delivery limitations remain unresolved.

## Payment requirements

### PAY-001 — List and read payments

- **Actor:** Accounts admin for payment listing; admin or eligible active family primary for payment detail/family list.
- **Requirement:** The API shall list payments for authorized accounts and expose payment details within the source-defined family access boundary.
- **Acceptance criteria (source-derived):**
  - Payment listing applies supported filters and pagination and requires accounts-admin access.
  - Detail and family payment reads enforce the payment-view helper.
- **Evidence:** `api/payments/{index,show,family-payments}.php`; `services/payments.php`; `schema.sql` (`payments`).
- **Status:** Backend source observed; admin list field/filter names differ from the server contract.

### PAY-002 — Record payment

- **Actor:** Accounts admin.
- **Requirement:** The API shall create a payment for an active family using a supported payment type, amount, method and status, optionally linked to an enrollment where applicable.
- **Acceptance criteria (source-derived):**
  - Amount and required fields/method/type/status are validated.
  - Non-cash methods require a transaction reference.
  - Class-fee payments require a valid enrollment belonging to the family.
  - Payment insert, enrollment recalculation and activity logging follow the source transaction.
- **Evidence:** `api/payments/store.php`; `services/payments.php` (`validatePaymentCreate`, `recalculateEnrollment`); schema (`payments`, `enrollments`).
- **Status:** Backend source observed; current admin form omits/renames required fields and offers an unsupported method.

### PAY-003 — Update or refund payment

- **Actor:** Accounts admin.
- **Requirement:** The API shall permit supported status/reference/note updates for editable payments and represent refunds as separate payment records.
- **Acceptance criteria (source-derived):**
  - Completed payments cannot be edited.
  - Updates are limited to the server-supported fields and recalculate linked enrollment payment status.
  - Refund amounts participate in the source-defined net-paid calculation.
- **Evidence:** `api/payments/update.php`; `services/payments.php`; `schema.sql` (`payments`, `enrollments`).
- **Status:** Backend source observed; no successful client/API exchange was tested.

### PAY-004 — Recalculate enrollment payment status

- **Actor:** System behavior associated with payment writes.
- **Requirement:** On linked payment changes, the API shall calculate enrollment payment state from completed payment/refund totals and the stored fee.
- **Acceptance criteria (source-derived):**
  - Net paid at/below zero maps to unpaid; below fee maps to partial; at/above fee maps to paid.
  - Only completed payments/refunds participate as defined by the service.
- **Evidence:** `services/payments.php` (`recalculateEnrollment`); `api/payments/{store,update}.php`.
- **Status:** Backend source observed. The schema's `refunded` value is not set by the inspected calculation.

## Notification requirements

### NOTIF-001 — Read and update notification state

- **Actor:** Authenticated member associated with the notification.
- **Requirement:** The API shall list the caller's notifications and support read/archive state changes and read-all.
- **Acceptance criteria (source-derived):**
  - Listing is scoped to the caller's profile.
  - Read/archive actions update only an accessible notification.
  - Read-all applies to the caller's notifications.
- **Evidence:** `api/notifications/{index,read-all,mark-read,archive}.php`; schema (`notifications`).
- **Status:** Backend source observed; admin list UI expects a different scope/shape and no member notification page was found.

### NOTIF-002 — Send targeted notification rows

- **Actor:** Elevated admin.
- **Requirement:** The API shall accept a send request with recipient member IDs and validated title, message and supported type, then persist recipient notification rows.
- **Acceptance criteria (source-derived):**
  - An empty recipient list or unsupported type is rejected.
  - One notification row is created per accepted recipient and send activity is logged transactionally.
- **Evidence:** `api/notifications/send.php`; `services/community-notifications.php`; schema (`notifications`).
- **Status:** Backend source observed; Angular form omits specific recipient IDs, uses different field names/path and offers SMS, which the backend does not accept.

### NOTIF-003 — Broadcast to supported audience

- **Actor:** Elevated admin.
- **Requirement:** The API shall create notification rows for active batch/group/all-family recipients using the supported broadcast target types.
- **Acceptance criteria (source-derived):**
  - Batch/group targets require a positive target ID.
  - Recipient profiles are selected from active memberships and deduplicated.
  - The source handler returns a successful zero-recipient result without creating rows.
- **Evidence:** `api/notifications/broadcast.php`; schema (`notifications`, batch/group/family membership tables).
- **Status:** Backend source observed. Provider delivery, retries and external notification transport are not established by this flow.

## Unresolved

The intended client/server contract, actual notification delivery, refunds UI behavior, and policy for zero-recipient broadcasts remain unverified. No requirements are asserted for an external email, push, SMS or payment provider.
