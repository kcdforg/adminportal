# Payments

This document covers the recording and review of family or member payments.

## Status

Working draft. Confirm financial controls and correction policies before approval.

### ADM-PAY-001 — Browse and review payments

**Actor:** Administrators with payment-view access.

**Requirement:** An authorized administrator can find and review payment records.

**Acceptance criteria:**
- Payment results can be filtered by supported criteria such as family, date, or status.
- Each record displays its amount, date, method, and related family or account context.
- Users without financial access cannot view restricted payment information.

### ADM-PAY-002 — Record a payment

**Actor:** Administrators with payment-entry access.

**Requirement:** An authorized administrator can record a payment against the correct family or account.

**Acceptance criteria:**
- The user enters the required amount, date, method, and related context.
- Invalid values are rejected before the record is saved.
- A successful payment entry is confirmed and appears in payment history.

### ADM-PAY-003 — Correct a payment record

**Actor:** Administrators with payment-management access.

**Requirement:** An authorized administrator can update a payment record when corrections are permitted.

**Acceptance criteria:**
- The user can identify which values will be changed.
- A successful update is reflected consistently in payment views.
- Failed or disallowed changes are reported without implying success.

## Open questions

- Define permitted payment methods, correction and refund rules, and audit requirements.
- Confirm which roles can view, enter, or correct financial records.
- Decide whether payment records may be deleted or only reversed/adjusted.
