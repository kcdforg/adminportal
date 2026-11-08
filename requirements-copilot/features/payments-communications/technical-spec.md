# Payments and communications — technical specification

**Evidence status:** Static backend/client/schema/test-source trace. No provider, successful API exchange or runtime outcome is asserted.

| ID | Current behavior and interfaces | Status / boundary |
|---|---|---|
| PAY-001 | Payment list/detail and family-payment handlers use `services/payments.php` pagination and access helpers. Accounts/super-admin list; detail/family views have family access checks. `payments` table. Admin list's date filter names differ from backend (`payment_date_from/to` vs `paid_at_from/to`). | **Partial** |
| PAY-002 | `POST /payments` validates active family, amount, type/method/status, non-cash reference and class-fee enrollment/family relation; transactional payment insert, recalculation and activity logging. | **Partial**: admin form fields/payload mismatch; it omits server-required class-fee enrollment ID and renames dates/references. |
| PAY-003 | `PATCH /payments/{id}` limits editable fields and rejects completed payments; refund is a separate `payment_type='refund'` record and recalculation uses net paid totals. Admin exposes refund entry but no payment-update call was found. | **Partial**; refund form shares input mismatch. |
| PAY-004 | `recalculateEnrollment` computes completed fees less refunds against stored fee and maps to unpaid/partial/paid. | **Observed**; schema `refunded` value is not set by the inspected calculation. |
| NOTIF-001 | Member notifications list/read/archive/read-all handlers use profile ID to scope records and update row state. `notifications` schema. No member notification page was found; handlers do not explicitly enforce member portal. | **Partial** |
| NOTIF-002 | `POST /notifications/send` expects recipient member IDs plus title/message/type, persists rows and logs activity. Admin send form/service sends a different path/body shape (`/notifications`, body/channel/audience). | **Partial**; persisted rows do not establish external delivery. |
| NOTIF-003 | `POST /notifications/broadcast` selects supported active batch/group/all-family audiences, deduplicates members and can return a successful zero-recipient result. Current admin form uses mismatched contract. | **Partial**; zero-recipient policy and delivery mechanism remain unresolved. |

## Interfaces and source

- Routes/handlers: `api-backend-php/api/routes.php`; `api/payments/*.php`, `api/notifications/*.php`.
- Helpers: `api-backend-php/services/payments.php`, `services/community-notifications.php`, `services/identity.php`.
- Schema: `payments`, `enrollments`, audience membership tables, `notifications`, `activity_logs` in `api-backend-php/database/schema.sql`.
- Clients: admin `core/services/{payment,notification}.service.ts`, `features/{payments,notifications}/`; member app has no notification page/service found.
- OpenAPI operations and detailed route disposition: `docs/architecture/api-contract-audit.md`.

## Unresolved matters

Do not treat client/server mismatches as an accepted contract. External push/email/SMS/payment transport is not evidenced. Payment date/filter input, notification audience and zero-recipient behavior remain as source findings. See provisional `baseline-workflow/analysis/requirements-accuracy-audit.md`; no policy or requirement changes are made.
