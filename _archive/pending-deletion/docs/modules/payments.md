# Payments Module

## Scope

Manages all financial transactions: class fees, donations, event fees, and refunds.

---

## Tables

- `payments` — individual payment transaction records

See `docs/01-database.md` for full schema with MySQL types.

---

## Business Rules

1. Every payment transaction is recorded as a row in `payments`. Payments are never deleted.
2. A payment is always linked to a `family_id` (the paying family).
3. `enrollment_id` is nullable — payments for donations or event fees may not link to an enrollment.
4. `payment_type` determines the nature of the transaction:
   - `class_fee` — payment for a class/batch enrollment
   - `donation` — standalone donation, no enrollment link
   - `event_fee` — payment for a one-time event
   - `refund` — a negative or reversal payment
5. For `class_fee` payments, `enrollment_id` must be provided.
6. For `donation` and `event_fee` payments, `enrollment_id` is optional.
7. A refund is recorded as a separate payment row with `payment_type = refund` and a negative `amount` (or positive amount with context in `notes`).
8. `payment_method` must be one of: `cash`, `bank_transfer`, `upi`, `card`, `cheque`, `online`.
9. `transaction_reference` is optional but required for non-cash methods.
10. `status` transitions: `pending → completed` or `pending → failed`.
11. Once a payment reaches `completed` status, it cannot be edited — only a new refund payment can be created.
12. `paid_at` is set when `status` changes to `completed`.
13. When a `class_fee` payment is recorded, the linked `enrollment.payment_status` must be recalculated:
    - Sum of completed payments for the enrollment vs. `enrollment.fee_amount`
    - `unpaid` → `partial` → `paid`
14. Only admins (`accounts`, `super_admin`) can create payment records.
15. Primary family members can view their family's payment history.
16. Admins can view all payments.

---

## Access Control Matrix

| Action | family_primary | family_normal | family_student | trainer | admin (accounts/super) |
|---|---|---|---|---|---|
| View own family payments | Yes | No | No | No | Yes |
| View all payments | No | No | No | No | Yes |
| Create payment | No | No | No | No | Yes |
| Edit payment | No | No | No | No | No (immutable once completed) |
| Create refund | No | No | No | No | Yes (super/accounts) |
| Download receipt | Yes (own) | No | No | No | Yes |

---

## API Endpoints

### GET /api/v1/payments
Admin (accounts, super_admin) sees all. Supports filters.

**Query params:** `family_id`, `payment_type`, `status`, `payment_method`, `paid_at_from`, `paid_at_to`, `page`, `per_page`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "family_id": 3,
      "enrollment_id": 7,
      "payment_type": "class_fee",
      "amount": 1500.00,
      "payment_method": "upi",
      "transaction_reference": "UPI123456",
      "status": "completed",
      "notes": null,
      "paid_at": "2026-01-15T10:30:00Z",
      "created_at": "2026-01-15T10:28:00Z"
    }
  ],
  "meta": { "total": 85, "per_page": 20, "current_page": 1, "last_page": 5 }
}
```

---

### GET /api/v1/families/{id}/payments
Primary family member (own family) or admin.

**Query params:** `payment_type`, `status`, `paid_at_from`, `paid_at_to`, `page`, `per_page`

---

### POST /api/v1/payments
Admin (accounts, super_admin) only.

**Request — Class Fee:**
```json
{
  "family_id": 3,
  "enrollment_id": 7,
  "payment_type": "class_fee",
  "amount": 1500.00,
  "payment_method": "upi",
  "transaction_reference": "UPI123456",
  "status": "completed",
  "notes": null
}
```

**Request — Donation:**
```json
{
  "family_id": 3,
  "enrollment_id": null,
  "payment_type": "donation",
  "amount": 5000.00,
  "payment_method": "bank_transfer",
  "transaction_reference": "NEFT20260115",
  "status": "completed",
  "notes": "Annual donation for KCDF activities"
}
```

**Request — Refund:**
```json
{
  "family_id": 3,
  "enrollment_id": 7,
  "payment_type": "refund",
  "amount": 1500.00,
  "payment_method": "bank_transfer",
  "transaction_reference": "REFUND001",
  "status": "completed",
  "notes": "Refund for cancelled enrollment"
}
```

**Response 201:**
```json
{
  "success": true,
  "data": {
    "id": 42,
    "family_id": 3,
    "enrollment_id": 7,
    "payment_type": "class_fee",
    "amount": 1500.00,
    "payment_method": "upi",
    "transaction_reference": "UPI123456",
    "status": "completed",
    "paid_at": "2026-01-15T10:30:00Z",
    "created_at": "2026-01-15T10:30:00Z"
  }
}
```

**Errors:**
- 422 — `class_fee` payment without `enrollment_id`
- 422 — invalid payment_method
- 404 — enrollment not found or not belonging to the given family

---

### GET /api/v1/payments/{id}
Admin or primary family member of the payment's family.

---

### PATCH /api/v1/payments/{id}
Admin only. Only allowed when `status = pending`. Can update `status`, `transaction_reference`, `notes`.

**Not allowed** when `status = completed` — create a refund payment instead.

---

## Validation Rules

| Field | Rule |
|---|---|
| `family_id` | required, must exist and be active |
| `enrollment_id` | required when payment_type = class_fee |
| `payment_type` | required, must be: class_fee, donation, event_fee, refund |
| `amount` | required, numeric, min 0.01, max 2 decimal places |
| `payment_method` | required, must be: cash, bank_transfer, upi, card, cheque, online |
| `transaction_reference` | required when payment_method != cash |
| `status` | required, must be: pending, completed, failed |
| `notes` | optional, string, max 1000 chars |

---

## Enrollment Payment Status Sync

After any payment is created for an enrollment, the system must recalculate `enrollment.payment_status`:

```
total_paid = SUM(amount) WHERE enrollment_id = X AND payment_type != 'refund' AND status = 'completed'
total_refunded = SUM(amount) WHERE enrollment_id = X AND payment_type = 'refund' AND status = 'completed'
net_paid = total_paid - total_refunded

if net_paid <= 0:
    enrollment.payment_status = 'unpaid'
elif net_paid < enrollment.fee_amount:
    enrollment.payment_status = 'partial'
elif net_paid >= enrollment.fee_amount:
    enrollment.payment_status = 'paid'
```

This logic lives in `PaymentService` and is called after every payment creation or update.

---

## Module Folder Structure

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
└── Policies/
    └── PaymentPolicy.php
```
