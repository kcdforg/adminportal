# CAP-007: Payments

## 1. Purpose

Keep a reliable record of money received or refunded for classes, donations and events.

## 2. Scope

Browsing and viewing payments, recording payments, correcting non-completed payments, recalculating linked enrollment payment status, and family payment history.

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-ENR-010` | Initialize enrollment payment state | Supporting | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-PAY-001` | Browse payments | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-002` | View payment details | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-003` | Record payment | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-004` | Update payment | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-005` | Validate payment creation input | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-006` | Restrict payment creation to accounts administrators | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-007` | Require active family for payment | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-008` | Validate payment enrollment family | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-009` | Set payment completion timestamp | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-010` | Recalculate enrollment after payment creation | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-011` | Restrict payment updates to accounts administrators | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-012` | Reject updates to noneditable payments | Primary | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-013` | Recalculate enrollment after payment update | Primary | [payments.md](../applications/api-backend/functional/payments.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-DSH-001` | View administration dashboard | Supporting | [dashboard.md](../applications/admin-portal/functional/dashboard.md) |
| `ADM-PAY-001` | Browse and review payments | Primary | [payments.md](../applications/admin-portal/functional/payments.md) |
| `ADM-PAY-002` | Record a payment | Primary | [payments.md](../applications/admin-portal/functional/payments.md) |
| `ADM-PAY-003` | Correct a payment record | Primary | [payments.md](../applications/admin-portal/functional/payments.md) |
| `ADM-RPT-002` | View payment report | Supporting | [reports.md](../applications/admin-portal/functional/reports.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-PAYMENTS-AND-NOTIFICATIONS` | Primary | [payments-and-notifications.md](../workflows/payments-and-notifications.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Payment Form (admin) | `UI-ADM-023-01` … `UI-ADM-023-04` (4) | Primary | [payment-form.md](../applications/admin-portal/ui/screens/payments/payment-form.md) |
| Payment List (admin) | `UI-ADM-024-01` … `UI-ADM-024-04` (4) | Primary | [payment-list.md](../applications/admin-portal/ui/screens/payments/payment-list.md) |
| Payment Report (admin) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Supporting | [payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-SEC-004`, `NFR-REL-001`, `NFR-OBS-002`
- **Proposed (not approved):** `NFR-SEC-011`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-005 Enrollment Management](./CAP-005-enrollment-management.md) — payments update enrollment payment status
- [CAP-011 Reporting, Dashboard & Audit](./CAP-011-reporting-dashboard-audit.md) — payment report
- [CAP-010 Notifications](./CAP-010-notifications.md) — workflow pairs payments with notifications

## 10. Implementation Status

**Partially implemented.** Manual recording, update rules and enrollment recalculation exist (`API-PAY-010/012/013` source-checked). No payment-gateway integration was found; any online payment expectation would be unimplemented.

## 11. Source-Code Evidence

- `api-backend-php/api/payments/` (`store.php`, `update.php`), `services/payments.php:78,180,214` (editability, transaction, recalculation).
- `kcdf-admin-app/src/app/core/services/payment.service.ts:22` (refund as a payment type), `features/payments/`. Database: `payments`. Tests: `phase5-payments.php`.

Individually source-checked requirements for this capability: `API-ENR-010`, `API-PAY-010`, `API-PAY-012`, `API-PAY-013`. All other requirements were content-reviewed for mapping but not individually checked against code.

## 12. Known Gaps

- Member-facing payment view: none documented or implemented.
- `WF-PAYMENTS-AND-NOTIFICATIONS` lists no payment-to-notification trigger requirement; whether payments should notify members is undecided.
- Payments are manual; gateway/online payments, receipts and reconciliation are not described anywhere.
