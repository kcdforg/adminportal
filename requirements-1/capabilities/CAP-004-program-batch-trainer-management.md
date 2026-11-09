# CAP-004: Program, Batch & Trainer Management

## 1. Purpose

Define what the organization offers and who teaches it.

## 2. Scope

Program catalogue and status, batches under programs (including membership view), trainer records, and the member-facing course browsing.

## 3. Sub-Capabilities

- Program configuration
- Batch management
- Trainer management
- Member course browsing

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-BAT-001` | Browse batches | Primary | [batches.md](../applications/api-backend/functional/batches.md) |
| `API-BAT-002` | View batch details | Primary | [batches.md](../applications/api-backend/functional/batches.md) |
| `API-BAT-003` | Create batch | Primary | [batches.md](../applications/api-backend/functional/batches.md) |
| `API-BAT-004` | Update batch | Primary | [batches.md](../applications/api-backend/functional/batches.md) |
| `API-BAT-005` | View batch membership | Primary | [batches.md](../applications/api-backend/functional/batches.md) |
| `API-PRG-001` | Browse programs | Primary | [programs.md](../applications/api-backend/functional/programs.md) |
| `API-PRG-003` | Create program | Primary | [programs.md](../applications/api-backend/functional/programs.md) |
| `API-PRG-004` | Update program | Primary | [programs.md](../applications/api-backend/functional/programs.md) |
| `API-PRG-005` | Change program status | Primary | [programs.md](../applications/api-backend/functional/programs.md) |
| `API-SES-001` | Browse batch sessions | Supporting | [sessions.md](../applications/api-backend/functional/sessions.md) |
| `API-SES-003` | Schedule batch session | Supporting | [sessions.md](../applications/api-backend/functional/sessions.md) |
| `API-TRN-001` | Browse trainer records | Primary | [trainers.md](../applications/api-backend/functional/trainers.md) |
| `API-TRN-002` | View trainer details | Primary | [trainers.md](../applications/api-backend/functional/trainers.md) |
| `API-TRN-003` | Create trainer record | Primary | [trainers.md](../applications/api-backend/functional/trainers.md) |
| `API-TRN-004` | Update trainer record | Primary | [trainers.md](../applications/api-backend/functional/trainers.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-BAT-001` | Browse and view batches | Primary | [batches.md](../applications/admin-portal/functional/batches.md) |
| `ADM-BAT-002` | Create and update batches | Primary | [batches.md](../applications/admin-portal/functional/batches.md) |
| `ADM-BAT-003` | Review batch membership | Primary | [batches.md](../applications/admin-portal/functional/batches.md) |
| `ADM-DSH-001` | View administration dashboard | Supporting | [dashboard.md](../applications/admin-portal/functional/dashboard.md) |
| `ADM-PRG-001` | Browse programs | Primary | [programs.md](../applications/admin-portal/functional/programs.md) |
| `ADM-PRG-002` | Create and edit programs | Primary | [programs.md](../applications/admin-portal/functional/programs.md) |
| `ADM-PRG-003` | Manage program status | Primary | [programs.md](../applications/admin-portal/functional/programs.md) |
| `ADM-SES-001` | Schedule and update a session | Supporting | [sessions.md](../applications/admin-portal/functional/sessions.md) |
| `ADM-TRN-001` | Browse and view trainers | Primary | [trainers.md](../applications/admin-portal/functional/trainers.md) |
| `ADM-TRN-002` | Create a trainer | Primary | [trainers.md](../applications/admin-portal/functional/trainers.md) |
| `ADM-TRN-003` | Edit trainer information and status | Primary | [trainers.md](../applications/admin-portal/functional/trainers.md) |

### Ionic Member Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `MEM-PRG-002` | Browse programs and courses | Primary | [programs.md](../applications/member-portal/functional/programs.md) |
| `MEM-PRG-003` | View program or course details | Primary | [programs.md](../applications/member-portal/functional/programs.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-PROGRAM-ENROLLMENT` | Primary | [program-enrollment.md](../workflows/program-enrollment.md) |
| `WF-SESSION-ATTENDANCE` | Supporting | [session-attendance.md](../workflows/session-attendance.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Batch Detail (admin) | `UI-ADM-004-01` … `UI-ADM-004-04` (4) | Primary | [batch-detail.md](../applications/admin-portal/ui/screens/batches/batch-detail.md) |
| Batch Form (admin) | `UI-ADM-005-01` … `UI-ADM-005-05` (5) | Primary | [batch-form.md](../applications/admin-portal/ui/screens/batches/batch-form.md) |
| Batch List (admin) | `UI-ADM-006-01` … `UI-ADM-006-05` (5) | Primary | [batch-list.md](../applications/admin-portal/ui/screens/batches/batch-list.md) |
| Program Form (admin) | `UI-ADM-025-01` … `UI-ADM-025-04` (4) | Primary | [program-form.md](../applications/admin-portal/ui/screens/programs/program-form.md) |
| Program List (admin) | `UI-ADM-026-01` … `UI-ADM-026-04` (4) | Primary | [program-list.md](../applications/admin-portal/ui/screens/programs/program-list.md) |
| Trainer Detail (admin) | `UI-ADM-032-01` … `UI-ADM-032-04` (4) | Primary | [trainer-detail.md](../applications/admin-portal/ui/screens/trainers/trainer-detail.md) |
| Trainer Form (admin) | `UI-ADM-033-01` … `UI-ADM-033-04` (4) | Primary | [trainer-form.md](../applications/admin-portal/ui/screens/trainers/trainer-form.md) |
| Trainer List (admin) | `UI-ADM-034-01` … `UI-ADM-034-04` (4) | Primary | [trainer-list.md](../applications/admin-portal/ui/screens/trainers/trainer-list.md) |
| Course Detail (member) | `UI-MEM-001-01` … `UI-MEM-001-04` (4) | Primary | [course-detail.md](../applications/member-portal/ui/screens/courses/course-detail.md) |
| Courses (member) | `UI-MEM-002-01` … `UI-MEM-002-03` (3) | Primary | [courses.md](../applications/member-portal/ui/screens/courses/courses.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-SEC-004`
- **Proposed (not approved):** `NFR-PRF-002`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-005 Enrollment Management](./CAP-005-enrollment-management.md) — enrollments attach members to batches
- [CAP-006 Sessions & Attendance](./CAP-006-sessions-attendance.md) — sessions belong to batches
- [CAP-002 Family & Member Management](./CAP-002-family-member-management.md) — trainers link to member profiles

## 10. Implementation Status

**Implemented.** Routes, handlers and admin/member screens exist for programs, batches and trainers. `API-PRG-*`, `API-BAT-*`, `API-TRN-*` were not individually source-checked; only `MEM-PRG-002` was. Batch schedule fields were added by a migration.

## 11. Source-Code Evidence

- `api-backend-php/api/programs/`, `batches/`, `trainers/`; `services/academics.php`; migration `20261108_add_batch_schedule_fields.sql`.
- `kcdf-admin-app/src/app/features/programs/`, `batches/`, `trainers/`; `kcdf-parents-app/src/app/courses/courses.page.ts:25`, `course-detail.page.ts:35-36`.
- Database: `programs`, `student_batches`, `batch_members`, `trainers`.

Individually source-checked requirements for this capability: `MEM-PRG-002`. All other requirements were content-reviewed for mapping but not individually checked against code.

## 12. Known Gaps

- `API-PRG-002` and `MEM-PRG-001` are absent from their sequences.
- `core/sample-content.ts` in the member app is not imported anywhere; if it is a leftover, it should not be mistaken for documented content.
- `enrollment-contract.md` notes an integration issue to resolve (see CAP-005).
