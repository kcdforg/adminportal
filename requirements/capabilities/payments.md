# Module: Payments

> **Module ID:** MOD-007  
> **Previous broad capability:** CAP-007 (retired as a capability ID; see [ID migration](./id-migration.md))

## Purpose

Keep a reliable record of money received or refunded for classes, donations and events.

## Scope

Browsing and viewing payments, recording payments, correcting non-completed payments, recalculating linked enrollment payment status, and family payment history.

## Atomic business capabilities

The CAP IDs below identify coherent business abilities; detailed requirements remain authoritative in the linked application documents. Classification is documentation-level and is not a claim of complete source-code verification.

### CAP-035 — Payment recording

**Business outcome:** payment recording within the payments module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-ENR-010` | Initialize enrollment payment state | Supporting | [enrollments.md](../applications/api-backend/functional/enrollments.md) | <!-- Mapping requires review -->
| `API-PAY-001` | Browse payments | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-002` | View payment details | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-003` | Record payment | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-004` | Update payment | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-005` | Validate payment creation input | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-006` | Restrict payment creation to accounts administrators | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-007` | Require active family for payment | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-008` | Validate payment enrollment family | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-009` | Set payment completion timestamp | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-010` | Recalculate enrollment after payment creation | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-011` | Restrict payment updates to accounts administrators | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-012` | Reject updates to noneditable payments | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `API-PAY-013` | Recalculate enrollment after payment update | Primary | [payments.md](../applications/api-backend/functional/payments.md) | <!-- Mapping requires review -->
| `ADM-DSH-001` | View administration dashboard | Supporting | [dashboard.md](../applications/admin-portal/functional/dashboard.md) | <!-- Mapping requires review -->
| `ADM-PAY-001` | Browse and review payments | Primary | [payments.md](../applications/admin-portal/functional/payments.md) | <!-- Mapping requires review -->
| `ADM-PAY-002` | Record a payment | Primary | [payments.md](../applications/admin-portal/functional/payments.md) | <!-- Mapping requires review -->
| Payment Form (admin) | `UI-ADM-023-01` … `UI-ADM-023-04` (4) | Primary | [payment-form.md](../applications/admin-portal/ui/screens/payments/payment-form.md) | <!-- Mapping requires review -->

### CAP-036 — Payment correction and refunds

**Business outcome:** payment correction and refunds within the payments module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `ADM-PAY-003` | Correct a payment record | Primary | [payments.md](../applications/admin-portal/functional/payments.md) |

### CAP-037 — Payment review and reconciliation

**Business outcome:** payment review and reconciliation within the payments module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `ADM-RPT-002` | View payment report | Supporting | [reports.md](../applications/admin-portal/functional/reports.md) |
| Payment List (admin) | `UI-ADM-024-01` … `UI-ADM-024-04` (4) | Primary | [payment-list.md](../applications/admin-portal/ui/screens/payments/payment-list.md) |
| Payment Report (admin) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Supporting | [payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) |

## Related workflows and UI

### Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-PAYMENTS-AND-NOTIFICATIONS` | Primary | [payments-and-notifications.md](../workflows/payments-and-notifications.md) |



### UI screen coverage

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Payment Form (admin) | `UI-ADM-023-01` … `UI-ADM-023-04` (4) | Primary | [payment-form.md](../applications/admin-portal/ui/screens/payments/payment-form.md) |
| Payment List (admin) | `UI-ADM-024-01` … `UI-ADM-024-04` (4) | Primary | [payment-list.md](../applications/admin-portal/ui/screens/payments/payment-list.md) |
| Payment Report (admin) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Supporting | [payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) |



## Technical references



## Non-functional references

The following are historical module-level NFR relationships; see the [NFR index](../non-functional/index.md) for unchanged NFR definitions and approval statuses.



IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-SEC-004`, `NFR-REL-001`, `NFR-OBS-002`
- **Proposed (not approved):** `NFR-SEC-011`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).



## Implementation evidence and gaps

Source inspection and implementation-status statements below were carried forward from the previous module-level catalog. They must **not** be interpreted as independently verified for every atomic CAP.

### Prior module-level assessment

**Partially implemented.** Manual recording, update rules and enrollment recalculation exist (`API-PAY-010/012/013` source-checked). No payment-gateway integration was found; any online payment expectation would be unimplemented.



### Prior source evidence

- `api-backend-php/api/payments/` (`store.php`, `update.php`), `services/payments.php:78,180,214` (editability, transaction, recalculation).
- `kcdf-admin-app/src/app/core/services/payment.service.ts:22` (refund as a payment type), `features/payments/`. Database: `payments`. Tests: `phase5-payments.php`.

Individually source-checked requirements for this capability: `API-ENR-010`, `API-PAY-010`, `API-PAY-012`, `API-PAY-013`. All other requirements were content-reviewed for mapping but not individually checked against code.



### Known gaps

- Member-facing payment view: none documented or implemented.
- `WF-PAYMENTS-AND-NOTIFICATIONS` lists no payment-to-notification trigger requirement; whether payments should notify members is undecided.
- Payments are manual; gateway/online payments, receipts and reconciliation are not described anywhere.


## Mapping review notes

- Automated classification assigned 17 requirement/screen rows to a provisional CAP because the title did not clearly identify a sub-capability. Review rows marked `Mapping requires review`.
- Shared workflows, UI screens, and NFRs are shown at module level; do not assume every reference applies to every atomic CAP.
