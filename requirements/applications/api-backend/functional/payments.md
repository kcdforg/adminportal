# Payments



## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### API-PAY-001 — Browse payments

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-001`
**Actor:** Accounts administrators.

**Requirement:** An accounts administrator browses payment records and applies available filters.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/payments/index.php:7`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/payments/index.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/payments/index.php`, `api-backend-php/api/payments/show.php`, `api-backend-php/api/payments/family-payments.php`, `kcdf-admin-app/src/app/features/payments/`, `kcdf-admin-app/src/app/core/services/payment.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-PAY-002 — View payment details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-002`
**Actor:** Accounts administrators.

**Requirement:** An accounts administrator opens a payment record to inspect its details.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/payments/index.php:7`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/payments/index.php:12`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/payments/index.php`, `api-backend-php/api/payments/show.php`, `api-backend-php/api/payments/family-payments.php`, `kcdf-admin-app/src/app/features/payments/`, `kcdf-admin-app/src/app/core/services/payment.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-PAY-003 — Record payment

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-003`
**Actor:** Accounts administrators.

**Requirement:** An accounts administrator enters a family payment and submits it for recording. For class fees, the payment is associated with an enrollment and its totals are recalculated.

**Source-supported acceptance checks (static code inspection; runtime untested):**
- The backend payment-create handler reads `status` and `transaction_reference` and inserts those fields into `payments`.
- The Angular payment form declares `reference_number` rather than `transaction_reference`, and does not declare a `status` control.
- The payment-create handler requires an authenticated accounts or super administrator; its validation is delegated to `validatePaymentCreate`.
- The Angular form payload therefore requires contract reconciliation before end-to-end creation can be considered verified.

**Source references:**
- `api-backend-php/api/payments/store.php`, `kcdf-admin-app/src/app/features/payments/`, `kcdf-admin-app/src/app/core/services/payment.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-PAY-004 — Update payment

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `PAY-004`
**Actor:** Authorized accounts users.

**Requirement:** The backend permits eligible users to update existing payment records.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/payments/update.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('UNAUTHORIZED', 'You do not have permission to update payments.', 403);` at `api-backend-php/api/payments/update.php:10`. Runtime outcome not established.
- **Client API call (source inspection):** Verify the behavior expressed by `return this.api.get<Payment>(`/payments/${id}`);` at `kcdf-admin-app/src/app/core/services/payment.service.ts:15`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/payments/update.php`, `kcdf-admin-app/src/app/features/payments/`, `kcdf-admin-app/src/app/core/services/payment.service.ts`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).


## Additional source-reconciled atomic requirements (2026-11-08)

The statements below are verified against the cited PHP handler branches only. They do not establish end-to-end operation.

### API-PAY-005 — Validate payment creation input

**Requirement:** The payment creation endpoint validates its input before creating a payment.

**Source:** `api-backend-php/api/payments/store.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-006 — Restrict payment creation to accounts administrators

**Requirement:** The payment creation endpoint rejects callers who fail isAccountsAdmin.

**Source:** `api-backend-php/api/payments/store.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-007 — Require active family for payment

**Requirement:** The payment creation endpoint verifies that the associated family is active.

**Source:** `api-backend-php/api/payments/store.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-008 — Validate payment enrollment family

**Requirement:** When enrollment_id is supplied, the payment creation endpoint verifies the enrollment belongs to the specified family.

**Source:** `api-backend-php/api/payments/store.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-009 — Set payment completion timestamp

**Requirement:** The payment creation endpoint sets paid_at when the submitted status is completed; otherwise it stores null.

**Source:** `api-backend-php/api/payments/store.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-010 — Recalculate enrollment after payment creation

**Requirement:** When the payment is associated with an enrollment, the creation endpoint recalculates that enrollment within the payment transaction.

**Source:** `api-backend-php/api/payments/store.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-011 — Restrict payment updates to accounts administrators

**Requirement:** The payment update endpoint rejects callers who fail isAccountsAdmin.

**Source:** `api-backend-php/api/payments/update.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-012 — Reject updates to noneditable payments

**Requirement:** The payment update endpoint checks that the payment is editable before and within its update transaction.

**Source:** `api-backend-php/api/payments/update.php`

**Verification:** Source branch inspected; runtime untested.

### API-PAY-013 — Recalculate enrollment after payment update

**Requirement:** The payment update endpoint recalculates the linked enrollment when present.

**Source:** `api-backend-php/api/payments/update.php`

**Verification:** Source branch inspected; runtime untested.