# CAP-005: Enrollment Management

## 1. Purpose

Place eligible family members into batches under program rules.

## 2. Scope

Listing, creating and cancelling enrollments, including eligibility, duplicate and capacity rules, fee recording, scoped visibility, and the enrollment API contract.

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-BAT-005` | View batch membership | Supporting | [batches.md](../applications/api-backend/functional/batches.md) |
| `API-ENR-001` | List enrollments | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-002` | Create enrollment | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-003` | Cancel enrollment | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-004` | Require enrollment details | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-005` | Authorize enrollment creation | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-006` | Verify family membership | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-007` | Restrict batch eligibility | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-008` | Prevent duplicate enrollment | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-009` | Enforce batch capacity | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-010` | Initialize enrollment payment state | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-011` | Limit enrollment visibility | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-012` | Authorize enrollment detail | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-013` | Authorize enrollment cancellation | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-014` | Reject repeat cancellation | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENR-015` | Bound enrollment pagination | Primary | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-PAY-008` | Validate payment enrollment family | Supporting | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-010` | Recalculate enrollment after payment creation | Supporting | [payments.md](../applications/api-backend/functional/payments.md) |
| `API-PAY-013` | Recalculate enrollment after payment update | Supporting | [payments.md](../applications/api-backend/functional/payments.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-BAT-003` | Review batch membership | Supporting | [batches.md](../applications/admin-portal/functional/batches.md) |
| `ADM-ENR-001` | Browse and review enrollments | Primary | [enrollments.md](../applications/admin-portal/functional/enrollments.md) |
| `ADM-ENR-002` | Enroll a family member | Primary | [enrollments.md](../applications/admin-portal/functional/enrollments.md) |
| `ADM-ENR-003` | Cancel an enrollment | Primary | [enrollments.md](../applications/admin-portal/functional/enrollments.md) |
| `ADM-RPT-003` | View enrollment report | Supporting | [reports.md](../applications/admin-portal/functional/reports.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-PROGRAM-ENROLLMENT` | Primary | [program-enrollment.md](../workflows/program-enrollment.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Enrollment Form (admin) | `UI-ADM-008-01` … `UI-ADM-008-04` (4) | Primary | [enrollment-form.md](../applications/admin-portal/ui/screens/enrollments/enrollment-form.md) |
| Enrollment List (admin) | `UI-ADM-009-01` … `UI-ADM-009-04` (4) | Primary | [enrollment-list.md](../applications/admin-portal/ui/screens/enrollments/enrollment-list.md) |
| Enrollment Report (admin) | `UI-ADM-028-01` … `UI-ADM-028-04` (4) | Supporting | [enrollment-report.md](../applications/admin-portal/ui/screens/reports/enrollment-report.md) |

## 7. Related Technical Requirements

- [api-backend/technical/enrollment-contract.md](../applications/api-backend/technical/enrollment-contract.md)

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-REL-001`, `NFR-REL-002`, `NFR-REL-003`, `NFR-PRF-001`, `NFR-OBS-002`
- **Proposed (not approved):** `NFR-PRF-002`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-004 Program, Batch & Trainer Management](./CAP-004-program-batch-trainer-management.md) — batches and programs
- [CAP-002 Family & Member Management](./CAP-002-family-member-management.md) — family-member eligibility
- [CAP-007 Payments](./CAP-007-payments.md) — fee and payment state

## 10. Implementation Status

**Partially implemented.** API rules for batch state, capacity, fee and unpaid state were source-checked. The requirements allow an eligible primary family member to enroll, but the member app has no enrollment route or requirement, so that path is API-only.

## 11. Source-Code Evidence

- `api-backend-php/api/enrollments/store.php:22,30,36,45`, `cancel.php`, `index.php:26`; `services/academics.php:302,326` (transaction, `FOR UPDATE`).
- `kcdf-admin-app/src/app/features/enrollments/`. Database: `enrollments`, `batch_members`. Test: `api-backend-php/tests/phase5-payments.php` touches enrollment payment state (not re-run).

Individually source-checked requirements for this capability: `API-ENR-006`, `API-ENR-007`, `API-ENR-009`, `API-ENR-010`, `API-ENR-015`, `API-PAY-010`, `API-PAY-013`. All other requirements were content-reviewed for mapping but not individually checked against code.

## 12. Known Gaps

- No member-portal enrollment screen or requirement although `API-ENR-002/005` allow primary family members.
- Open product decisions in `WF-PROGRAM-ENROLLMENT` (eligibility, wait-list, cancellation policy, fee prerequisites) remain unresolved.
- The batch status rule in code (`upcoming` or `active`) should be confirmed against `API-ENR-007` wording ("open for enrollment").
- `enrollment-contract.md` lists an "integration issue to resolve"; not re-evaluated here.
