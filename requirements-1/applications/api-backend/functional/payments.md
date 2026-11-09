# Payments

## Requirement summary

This document defines the expected payments behavior provided by the API.

### API-PAY-001 — Browse payments

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-001`
**Actor:** Accounts administrators.

**Requirement:** An accounts administrator browses payment records and applies available filters.

---

### API-PAY-002 — View payment details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-002`
**Actor:** Accounts administrators.

**Requirement:** An accounts administrator opens a payment record to inspect its details.

---

### API-PAY-003 — Record payment

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-003`
**Actor:** Accounts administrators.

**Requirement:** An accounts administrator enters a family payment and submits it for recording. For class fees, the payment is associated with an enrollment and its totals are recalculated.

---

### API-PAY-004 — Update payment

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-004`
**Actor:** Authorized accounts users.

**Requirement:** The system permits eligible users to update existing payment records.

## Additional requirements

### API-PAY-005 — Validate payment creation input

**Requirement:** The payment creation operation validates its input before creating a payment.

### API-PAY-006 — Restrict payment creation to accounts administrators

**Requirement:** The payment creation operation rejects callers who fail isAccountsAdmin.

### API-PAY-007 — Require active family for payment

**Requirement:** The payment creation operation verifies that the associated family is active.

### API-PAY-008 — Validate payment enrollment family

**Requirement:** When enrollment_id is supplied, the payment creation operation verifies the enrollment belongs to the specified family.

### API-PAY-009 — Set payment completion timestamp

**Requirement:** The payment creation operation sets paid_at when the submitted status is completed; otherwise it stores null.

### API-PAY-010 — Recalculate enrollment after payment creation

**Requirement:** When the payment is associated with an enrollment, the creation operation recalculates that enrollment within the payment transaction.

### API-PAY-011 — Restrict payment updates to accounts administrators

**Requirement:** The payment update operation rejects callers who fail isAccountsAdmin.

### API-PAY-012 — Reject updates to noneditable payments

**Requirement:** The payment update operation checks that the payment is editable before and within its update transaction.

### API-PAY-013 — Recalculate enrollment after payment update

**Requirement:** The payment update operation recalculates the linked enrollment when present.
