# Phase 5 — Payments Module

## Context

You are continuing development of `kcdf-api-backend` (Slim Framework 4).
Phases 1–4 are complete.

This phase implements the **Payments module** covering:
- Recording payment transactions (class fees, donations, event fees, refunds)
- Querying payment history by family
- Updating enrollment payment_status after each payment

---

## Module Location

```
src/Modules/Payments/
├── Controllers/
│   └── PaymentController.php
├── Services/
│   └── PaymentService.php
├── Repositories/
│   └── PaymentRepository.php
├── Models/
│   └── Payment.php
├── DTOs/
│   └── CreatePaymentDTO.php
├── Validators/
│   └── PaymentValidator.php
├── Policies/
│   └── PaymentPolicy.php
└── routes.php
```

---

## Endpoints to Implement

- GET /api/v1/payments — Admin (accounts, super_admin): all; filter: family_id, payment_type, status, payment_method, paid_at_from, paid_at_to
- GET /api/v1/families/{id}/payments — Admin or primary family member of that family
- POST /api/v1/payments — Admin (accounts, super_admin) only
- GET /api/v1/payments/{id} — Admin or primary family member of the payment's family
- PATCH /api/v1/payments/{id} — Admin only, only when status = pending

---

## Business Rules

1. Only admins (accounts, super_admin) can create payment records.
2. payment_type must be: class_fee, donation, event_fee, refund.
3. For payment_type = class_fee: enrollment_id is required and must belong to the given family_id.
4. For payment_type = donation or event_fee: enrollment_id is optional.
5. For payment_type = refund: enrollment_id is optional; amount represents the refunded amount.
6. transaction_reference is required for all payment_method values except cash.
7. A completed payment (status = completed) cannot be edited — return error.
8. paid_at is set automatically when status = completed.
9. After creating or updating a payment linked to an enrollment: recalculate and update enrollment.payment_status.

### Enrollment Payment Status Recalculation Logic

After any payment creation or status change for an enrollment:

```
total_paid    = SUM(amount) WHERE enrollment_id = X AND payment_type != 'refund' AND status = 'completed'
total_refund  = SUM(amount) WHERE enrollment_id = X AND payment_type = 'refund'  AND status = 'completed'
net_paid      = total_paid - total_refund

if net_paid <= 0              → enrollment.payment_status = 'unpaid'
if 0 < net_paid < fee_amount  → enrollment.payment_status = 'partial'
if net_paid >= fee_amount     → enrollment.payment_status = 'paid'
```

This logic must live in `PaymentService::recalculateEnrollmentPaymentStatus(int $enrollmentId)`.

---

## Validation Rules

| Field | Rule |
|---|---|
| family_id | required, must exist and be active |
| enrollment_id | required when payment_type = class_fee; must belong to family_id |
| payment_type | required, must be: class_fee, donation, event_fee, refund |
| amount | required, numeric, min 0.01, max 2 decimal places |
| payment_method | required, one of: cash, bank_transfer, upi, card, cheque, online |
| transaction_reference | required when payment_method != cash |
| status | required, one of: pending, completed, failed |
| notes | optional, string, max 1000 |

---

## Audit Logging

After every payment creation or status update, log to activity_logs:
```
action: payment_recorded (create) or payment_updated (status change)
entity_type: payments
entity_id: payment.id
old_values: null (create) or { status: old_status } (update)
new_values: { amount, payment_type, status, family_id }
```

---

## Rules

- No raw SQL — all DB access through PaymentRepository
- Policy class enforces who can view/create payments
- Register routes in src/Modules/Payments/routes.php
