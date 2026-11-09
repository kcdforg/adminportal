# Capability Traceability and Gaps

Maps every existing requirement to capabilities in [index.md](./index.md) and records gaps, discrepancies and review items. Application requirement files are unchanged and remain authoritative; this file only references them. Individual capability files group the same mapping per capability.

## 1. Method and counts

- **Inventory (recalculated from the repository):** 460 requirement headings in application documents — 264 functional and 196 UI — plus 8 workflow documents and 10 technical documents (no IDs). The earlier figure of 460 headings is confirmed; the earlier "115 documents" counted 39 functional, 58 UI, 8 workflow and 10 technical files.
- **Mapping basis:** the mapping was rebuilt from the requirement text, not file names: every functional requirement's title and statement was read; each UI document's headings, actions and navigation targets were read; workflows were mapped from their "Participating requirements" lists.
- **UI documents** consist of generic sub-requirements per screen (screen structure, controls, actions, navigation, states). They are mapped per screen, and each sub-requirement shares the screen's mapping.
- **Source verification** was limited to targeted checks (listed per requirement as "Source"). It is not a full verification of every requirement.
- **Corrections to the earlier mapping** are listed in section 7.

## 2. Functional requirements → capabilities

Relationship: **Primary** defines the capability; **Supporting** serves it but is owned by another capability; **Cross-cutting** applies across capabilities (listed under its home capability); **Uncertain** needs review. Verification: "Content" = mapping verified from requirement text; "Source" = additionally spot-checked in code (reference given). Requirement titles are omitted here; see the linked document.

### CAP-001 — [Identity, Authentication & Access Control](./CAP-001-identity-authentication-access-control.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-AUTH-001` | [admin-portal/functional/authentication.md](../applications/admin-portal/functional/authentication.md) | Primary | Content |
| `ADM-AUTH-007` | [admin-portal/functional/authentication.md](../applications/admin-portal/functional/authentication.md) | Cross-cutting | Content |
| `ADM-AUTH-008` | [admin-portal/functional/authentication.md](../applications/admin-portal/functional/authentication.md) | Cross-cutting | Content + Source (`kcdf-admin-app/src/app/app.routes.ts (roleGuard)`) |
| `ADM-FAM-009` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content + Source (`kcdf-admin-app/.../family-detail.component.ts:17,133`) |
| `API-ADM-001` | [api-backend/functional/administrators.md](../applications/api-backend/functional/administrators.md) | Primary | Content |
| `API-ADM-002` | [api-backend/functional/administrators.md](../applications/api-backend/functional/administrators.md) | Primary | Content |
| `API-ADM-003` | [api-backend/functional/administrators.md](../applications/api-backend/functional/administrators.md) | Primary | Content + Source (`api-backend-php/api/admins/store.php:67`) |
| `API-ADM-004` | [api-backend/functional/administrators.md](../applications/api-backend/functional/administrators.md) | Primary | Content |
| `API-AUTH-003` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Cross-cutting | Content |
| `API-AUTH-004` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-005` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content + Source (`api-backend-php/api/auth/logout.php:33`) |
| `API-AUTH-006` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-007` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-008` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-009` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-010` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-011` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-012` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content + Source (`api-backend-php/api/auth/refresh.php`) |
| `API-AUTH-013` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content + Source (`api-backend-php/api/auth/refresh.php`) |
| `API-AUTH-014` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-015` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-AUTH-016` | [api-backend/functional/authentication.md](../applications/api-backend/functional/authentication.md) | Primary | Content |
| `API-INV-005` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Supporting | Content |
| `API-MEM-005` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content + Source (`api-backend-php/api/members/login-store.php:73`) |
| `API-MEM-006` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `MEM-AUTH-002` | [member-portal/functional/authentication.md](../applications/member-portal/functional/authentication.md) | Primary | Content |
| `MEM-AUTH-009` | [member-portal/functional/authentication.md](../applications/member-portal/functional/authentication.md) | Primary | Content |
| `MEM-AUTH-010` | [member-portal/functional/authentication.md](../applications/member-portal/functional/authentication.md) | Primary | Content |

### CAP-002 — [Family & Member Management](./CAP-002-family-member-management.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-DSH-001` | [admin-portal/functional/dashboard.md](../applications/admin-portal/functional/dashboard.md) | Supporting | Content |
| `ADM-FAM-001` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-002` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-003` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-004` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-005` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-006` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-007` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-008` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-FAM-009` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Supporting | Content + Source (`kcdf-admin-app/.../family-detail.component.ts:17,133`) |
| `ADM-FAM-010` | [admin-portal/functional/families.md](../applications/admin-portal/functional/families.md) | Primary | Content |
| `ADM-MBR-001` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `ADM-MBR-002` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `ADM-MBR-003` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `ADM-MBR-004` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `ADM-MBR-005` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `ADM-MBR-006` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `ADM-MBR-007` | [admin-portal/functional/members.md](../applications/admin-portal/functional/members.md) | Primary | Content |
| `API-ENR-005` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Supporting | Content |
| `API-ENR-006` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Supporting | Content + Source (`api-backend-php/api/enrollments/store.php:22`) |
| `API-ENT-001` | [api-backend/functional/entities.md](../applications/api-backend/functional/entities.md) | Primary | Content |
| `API-ENT-002` | [api-backend/functional/entities.md](../applications/api-backend/functional/entities.md) | Primary | Content |
| `API-ENT-003` | [api-backend/functional/entities.md](../applications/api-backend/functional/entities.md) | Primary | Content |
| `API-ENT-004` | [api-backend/functional/entities.md](../applications/api-backend/functional/entities.md) | Primary | Content |
| `API-FAM-001` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-002` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-003` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-004` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-005` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-006` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-019` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-007` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-008` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-009` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-010` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-011` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-012` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content + Source (`api-backend-php/api/families/members-store.php:30`) |
| `API-FAM-013` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-014` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content + Source (`api-backend-php/api/families/members-store.php:67`) |
| `API-FAM-015` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-016` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-017` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-FAM-018` | [api-backend/functional/families.md](../applications/api-backend/functional/families.md) | Primary | Content |
| `API-GRP-021` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Supporting | Content |
| `API-GRP-026` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Supporting | Content |
| `API-INV-005` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Supporting | Content |
| `API-MEM-001` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-002` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-003` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-004` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-005` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Supporting | Content + Source (`api-backend-php/api/members/login-store.php:73`) |
| `API-MEM-006` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Supporting | Content |
| `API-MEM-007` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-012` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-011` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-008` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-009` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-MEM-010` | [api-backend/functional/members.md](../applications/api-backend/functional/members.md) | Primary | Content |
| `API-TRN-003` | [api-backend/functional/trainers.md](../applications/api-backend/functional/trainers.md) | Supporting | Content |
| `MEM-FAM-001` | [member-portal/functional/family.md](../applications/member-portal/functional/family.md) | Primary | Content |
| `MEM-FAM-002` | [member-portal/functional/family.md](../applications/member-portal/functional/family.md) | Primary | Content |
| `MEM-PRO-001` | [member-portal/functional/profile.md](../applications/member-portal/functional/profile.md) | Primary | Content |
| `MEM-PRO-002` | [member-portal/functional/profile.md](../applications/member-portal/functional/profile.md) | Primary | Content |

### CAP-003 — [Invitations & Member Onboarding](./CAP-003-invitations-member-onboarding.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `API-INV-001` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Primary | Content |
| `API-INV-002` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Primary | Content |
| `API-INV-003` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Primary | Content |
| `API-INV-004` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Primary | Content |
| `API-INV-005` | [api-backend/functional/invitations.md](../applications/api-backend/functional/invitations.md) | Primary | Content |

### CAP-004 — [Program, Batch & Trainer Management](./CAP-004-program-batch-trainer-management.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-BAT-001` | [admin-portal/functional/batches.md](../applications/admin-portal/functional/batches.md) | Primary | Content |
| `ADM-BAT-002` | [admin-portal/functional/batches.md](../applications/admin-portal/functional/batches.md) | Primary | Content |
| `ADM-BAT-003` | [admin-portal/functional/batches.md](../applications/admin-portal/functional/batches.md) | Primary | Content |
| `ADM-DSH-001` | [admin-portal/functional/dashboard.md](../applications/admin-portal/functional/dashboard.md) | Supporting | Content |
| `ADM-PRG-001` | [admin-portal/functional/programs.md](../applications/admin-portal/functional/programs.md) | Primary | Content |
| `ADM-PRG-002` | [admin-portal/functional/programs.md](../applications/admin-portal/functional/programs.md) | Primary | Content |
| `ADM-PRG-003` | [admin-portal/functional/programs.md](../applications/admin-portal/functional/programs.md) | Primary | Content |
| `ADM-SES-001` | [admin-portal/functional/sessions.md](../applications/admin-portal/functional/sessions.md) | Supporting | Content |
| `ADM-TRN-001` | [admin-portal/functional/trainers.md](../applications/admin-portal/functional/trainers.md) | Primary | Content |
| `ADM-TRN-002` | [admin-portal/functional/trainers.md](../applications/admin-portal/functional/trainers.md) | Primary | Content |
| `ADM-TRN-003` | [admin-portal/functional/trainers.md](../applications/admin-portal/functional/trainers.md) | Primary | Content |
| `API-BAT-001` | [api-backend/functional/batches.md](../applications/api-backend/functional/batches.md) | Primary | Content |
| `API-BAT-002` | [api-backend/functional/batches.md](../applications/api-backend/functional/batches.md) | Primary | Content |
| `API-BAT-003` | [api-backend/functional/batches.md](../applications/api-backend/functional/batches.md) | Primary | Content |
| `API-BAT-004` | [api-backend/functional/batches.md](../applications/api-backend/functional/batches.md) | Primary | Content |
| `API-BAT-005` | [api-backend/functional/batches.md](../applications/api-backend/functional/batches.md) | Primary | Content |
| `API-PRG-001` | [api-backend/functional/programs.md](../applications/api-backend/functional/programs.md) | Primary | Content |
| `API-PRG-003` | [api-backend/functional/programs.md](../applications/api-backend/functional/programs.md) | Primary | Content |
| `API-PRG-004` | [api-backend/functional/programs.md](../applications/api-backend/functional/programs.md) | Primary | Content |
| `API-PRG-005` | [api-backend/functional/programs.md](../applications/api-backend/functional/programs.md) | Primary | Content |
| `API-SES-001` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Supporting | Content |
| `API-SES-003` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Supporting | Content |
| `API-TRN-001` | [api-backend/functional/trainers.md](../applications/api-backend/functional/trainers.md) | Primary | Content |
| `API-TRN-002` | [api-backend/functional/trainers.md](../applications/api-backend/functional/trainers.md) | Primary | Content |
| `API-TRN-003` | [api-backend/functional/trainers.md](../applications/api-backend/functional/trainers.md) | Primary | Content |
| `API-TRN-004` | [api-backend/functional/trainers.md](../applications/api-backend/functional/trainers.md) | Primary | Content |
| `MEM-PRG-002` | [member-portal/functional/programs.md](../applications/member-portal/functional/programs.md) | Primary | Content + Source (`kcdf-parents-app/src/app/courses/courses.page.ts:25`) |
| `MEM-PRG-003` | [member-portal/functional/programs.md](../applications/member-portal/functional/programs.md) | Primary | Content |

### CAP-005 — [Enrollment Management](./CAP-005-enrollment-management.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-BAT-003` | [admin-portal/functional/batches.md](../applications/admin-portal/functional/batches.md) | Supporting | Content |
| `ADM-ENR-001` | [admin-portal/functional/enrollments.md](../applications/admin-portal/functional/enrollments.md) | Primary | Content |
| `ADM-ENR-002` | [admin-portal/functional/enrollments.md](../applications/admin-portal/functional/enrollments.md) | Primary | Content |
| `ADM-ENR-003` | [admin-portal/functional/enrollments.md](../applications/admin-portal/functional/enrollments.md) | Primary | Content |
| `ADM-RPT-003` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Supporting | Content |
| `API-BAT-005` | [api-backend/functional/batches.md](../applications/api-backend/functional/batches.md) | Supporting | Content |
| `API-ENR-001` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-002` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-003` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-004` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-005` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-006` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content + Source (`api-backend-php/api/enrollments/store.php:22`) |
| `API-ENR-007` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content + Source (`api-backend-php/api/enrollments/store.php:30`) |
| `API-ENR-008` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-009` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content + Source (`api-backend-php/api/enrollments/store.php:36`) |
| `API-ENR-010` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content + Source (`api-backend-php/api/enrollments/store.php:45`) |
| `API-ENR-011` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-012` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-013` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-014` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content |
| `API-ENR-015` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Primary | Content + Source (`api-backend-php/api/enrollments/index.php:26`) |
| `API-PAY-008` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Supporting | Content |
| `API-PAY-010` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Supporting | Content + Source (`api-backend-php/services/payments.php:214`) |
| `API-PAY-013` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Supporting | Content + Source (`api-backend-php/services/payments.php:214`) |

### CAP-006 — [Sessions & Attendance](./CAP-006-sessions-attendance.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-RPT-001` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Supporting | Content |
| `ADM-SES-001` | [admin-portal/functional/sessions.md](../applications/admin-portal/functional/sessions.md) | Primary | Content |
| `ADM-SES-002` | [admin-portal/functional/sessions.md](../applications/admin-portal/functional/sessions.md) | Primary | Content |
| `ADM-SES-003` | [admin-portal/functional/sessions.md](../applications/admin-portal/functional/sessions.md) | Primary | Content |
| `API-ATT-001` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-002` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-003` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-004` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-005` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-006` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content + Source (`api-backend-php/api/attendance/patch.php:13`) |
| `API-ATT-007` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-008` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-009` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-010` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content |
| `API-ATT-011` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Primary | Content + Source (`api-backend-php/api/attendance/patch.php:31`) |
| `API-SES-001` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Primary | Content |
| `API-SES-002` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Primary | Content |
| `API-SES-003` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Primary | Content |
| `API-SES-004` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Primary | Content |
| `API-SES-005` | [api-backend/functional/sessions.md](../applications/api-backend/functional/sessions.md) | Primary | Content |

### CAP-007 — [Payments](./CAP-007-payments.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-DSH-001` | [admin-portal/functional/dashboard.md](../applications/admin-portal/functional/dashboard.md) | Supporting | Content |
| `ADM-PAY-001` | [admin-portal/functional/payments.md](../applications/admin-portal/functional/payments.md) | Primary | Content |
| `ADM-PAY-002` | [admin-portal/functional/payments.md](../applications/admin-portal/functional/payments.md) | Primary | Content |
| `ADM-PAY-003` | [admin-portal/functional/payments.md](../applications/admin-portal/functional/payments.md) | Primary | Content |
| `ADM-RPT-002` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Supporting | Content |
| `API-ENR-010` | [api-backend/functional/enrollments.md](../applications/api-backend/functional/enrollments.md) | Supporting | Content + Source (`api-backend-php/api/enrollments/store.php:45`) |
| `API-PAY-001` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-002` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-003` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-004` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-005` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-006` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-007` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-008` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-009` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-010` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content + Source (`api-backend-php/services/payments.php:214`) |
| `API-PAY-011` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content |
| `API-PAY-012` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content + Source (`api-backend-php/services/payments.php:78`) |
| `API-PAY-013` | [api-backend/functional/payments.md](../applications/api-backend/functional/payments.md) | Primary | Content + Source (`api-backend-php/services/payments.php:214`) |

### CAP-008 — [Group Management & Membership](./CAP-008-group-management-membership.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-GRP-001` | [admin-portal/functional/groups.md](../applications/admin-portal/functional/groups.md) | Primary | Content |
| `ADM-GRP-002` | [admin-portal/functional/groups.md](../applications/admin-portal/functional/groups.md) | Primary | Content |
| `ADM-GRP-003` | [admin-portal/functional/groups.md](../applications/admin-portal/functional/groups.md) | Primary | Content |
| `API-EVT-001` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Supporting | Content |
| `API-EVT-008` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Supporting | Content |
| `API-POL-001` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Supporting | Content |
| `API-POL-007` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Supporting | Content |
| `API-POST-003` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Supporting | Content |
| `API-PST-005` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Supporting | Content |
| `API-PST-006` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Supporting | Content |
| `API-PST-013` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Supporting | Content |
| `API-GRP-001` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-002` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-003` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-004` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-005` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-006` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-007` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-008` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-009` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-010` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-011` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-012` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content + Source (`api-backend-php/api/groups/join.php:9`) |
| `API-GRP-013` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-014` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-015` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content + Source (`api-backend-php/api/groups/remove-member.php:13`) |
| `API-GRP-016` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-017` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-018` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-019` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-020` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-021` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-022` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content + Source (`api-backend-php/api/groups/remove-member.php:13`) |
| `API-GRP-023` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-033` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-034` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-026` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-027` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-028` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-029` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-030` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content + Source (`api-backend-php/api/groups/join.php:9`) |
| `API-GRP-031` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-032` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-024` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `API-GRP-025` | [api-backend/functional/groups.md](../applications/api-backend/functional/groups.md) | Primary | Content |
| `MEM-GRP-001` | [member-portal/functional/groups.md](../applications/member-portal/functional/groups.md) | Primary | Content |
| `MEM-GRP-002` | [member-portal/functional/groups.md](../applications/member-portal/functional/groups.md) | Primary | Content |
| `MEM-GRP-003` | [member-portal/functional/groups.md](../applications/member-portal/functional/groups.md) | Primary | Content + Source (`kcdf-parents-app/src/app/groups/group-detail.page.ts:274,283`) |

### CAP-009 — [Group Content (Posts, Events, Polls)](./CAP-009-group-content.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `API-EVT-001` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-002` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-003` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-004` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-005` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-006` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-007` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content + Source (`api-backend-php/api/groups/resources.php:210`) |
| `API-EVT-008` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-009` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-010` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-011` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-012` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-EVT-013` | [api-backend/functional/group-events.md](../applications/api-backend/functional/group-events.md) | Primary | Content |
| `API-POL-001` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-002` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-003` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-004` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-005` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content + Source (`api-backend-php/api/groups/resources.php:170`) |
| `API-POL-006` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-007` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-008` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-009` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-010` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-011` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-012` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POL-013` | [api-backend/functional/group-polls.md](../applications/api-backend/functional/group-polls.md) | Primary | Content |
| `API-POST-001` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-POST-002` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-POST-003` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-POST-004` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-POST-005` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-005` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-006` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-007` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content + Source (`api-backend-php/api/groups/resources.php:107`) |
| `API-PST-008` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-009` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-010` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content + Source (`api-backend-php/api/groups/resources.php:116`) |
| `API-PST-011` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-012` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `API-PST-013` | [api-backend/functional/group-posts.md](../applications/api-backend/functional/group-posts.md) | Primary | Content |
| `MEM-POST-001` | [member-portal/functional/posts.md](../applications/member-portal/functional/posts.md) | Primary | Content + Source (`kcdf-parents-app/src/app/posts/posts.page.ts`) |
| `MEM-POST-002` | [member-portal/functional/posts.md](../applications/member-portal/functional/posts.md) | Primary | Content |

### CAP-010 — [Notifications](./CAP-010-notifications.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-NOT-001` | [admin-portal/functional/notifications.md](../applications/admin-portal/functional/notifications.md) | Primary | Content |
| `ADM-NOT-002` | [admin-portal/functional/notifications.md](../applications/admin-portal/functional/notifications.md) | Primary | Content |
| `ADM-NOT-003` | [admin-portal/functional/notifications.md](../applications/admin-portal/functional/notifications.md) | Primary | Content |
| `API-NOT-001` | [api-backend/functional/notifications.md](../applications/api-backend/functional/notifications.md) | Primary | Content |
| `API-NOT-002` | [api-backend/functional/notifications.md](../applications/api-backend/functional/notifications.md) | Primary | Content |
| `API-NOT-003` | [api-backend/functional/notifications.md](../applications/api-backend/functional/notifications.md) | Primary | Content |
| `API-NOT-004` | [api-backend/functional/notifications.md](../applications/api-backend/functional/notifications.md) | Primary | Content |
| `API-NOT-005` | [api-backend/functional/notifications.md](../applications/api-backend/functional/notifications.md) | Primary | Content |
| `API-NOT-006` | [api-backend/functional/notifications.md](../applications/api-backend/functional/notifications.md) | Primary | Content |

### CAP-011 — [Reporting, Dashboard & Audit](./CAP-011-reporting-dashboard-audit.md)

| Requirement ID | Document | Relationship | Verification |
|---|---|---|---|
| `ADM-LOG-001` | [admin-portal/functional/audit-logs.md](../applications/admin-portal/functional/audit-logs.md) | Primary | Content |
| `ADM-LOG-002` | [admin-portal/functional/audit-logs.md](../applications/admin-portal/functional/audit-logs.md) | Primary | Content |
| `ADM-DSH-001` | [admin-portal/functional/dashboard.md](../applications/admin-portal/functional/dashboard.md) | Primary | Content |
| `ADM-RPT-001` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Primary | Content |
| `ADM-RPT-002` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Primary | Content |
| `ADM-RPT-003` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Primary | Content |
| `ADM-RPT-004` | [admin-portal/functional/reports.md](../applications/admin-portal/functional/reports.md) | Primary | Content |
| `API-LOG-001` | [api-backend/functional/activity-logs.md](../applications/api-backend/functional/activity-logs.md) | Primary | Content |
| `API-LOG-002` | [api-backend/functional/activity-logs.md](../applications/api-backend/functional/activity-logs.md) | Primary | Content |
| `API-LOG-003` | [api-backend/functional/activity-logs.md](../applications/api-backend/functional/activity-logs.md) | Primary | Content |
| `API-LOG-004` | [api-backend/functional/activity-logs.md](../applications/api-backend/functional/activity-logs.md) | Primary | Content |
| `API-ATT-010` | [api-backend/functional/attendance.md](../applications/api-backend/functional/attendance.md) | Supporting | Content |

## 3. UI requirements → capabilities

Screen-level mapping; every sub-requirement of a screen (`UI-…-NN`, titled Screen structure / Input controls / User actions / Navigation / Conditional UI states) shares the screen's mapping.

| Capability | Screen document | UI IDs (count) | Relationship | Verification |
|---|---|---|---|---|
| CAP-001 | [admin-portal/ui/screens/auth/login.md](../applications/admin-portal/ui/screens/auth/login.md) | `UI-ADM-003-01` … `UI-ADM-003-08` (8) | Primary | Content |
| CAP-001 | [admin-portal/ui/screens/families/family-detail.md](../applications/admin-portal/ui/screens/families/family-detail.md) | `UI-ADM-010-01` … `UI-ADM-010-04` (4) | Supporting | Content |
| CAP-001 | [admin-portal/ui/screens/families/member-login-form.md](../applications/admin-portal/ui/screens/families/member-login-form.md) | `UI-ADM-012-01` … `UI-ADM-012-04` (4) | Primary | Content |
| CAP-001 | [member-portal/ui/screens/login/login.md](../applications/member-portal/ui/screens/login/login.md) | `UI-MEM-007-01` … `UI-MEM-007-04` (4) | Primary | Content |
| CAP-001 | [member-portal/ui/screens/me/profile.md](../applications/member-portal/ui/screens/me/profile.md) | `UI-MEM-008-01` … `UI-MEM-008-04` (4) | Supporting | Content |
| CAP-002 | [admin-portal/ui/screens/families/family-detail.md](../applications/admin-portal/ui/screens/families/family-detail.md) | `UI-ADM-010-01` … `UI-ADM-010-04` (4) | Primary | Content |
| CAP-002 | [admin-portal/ui/screens/families/family-form.md](../applications/admin-portal/ui/screens/families/family-form.md) | `UI-ADM-013-01` … `UI-ADM-013-04` (4) | Primary | Content |
| CAP-002 | [admin-portal/ui/screens/families/family-list.md](../applications/admin-portal/ui/screens/families/family-list.md) | `UI-ADM-014-01` … `UI-ADM-014-04` (4) | Primary | Content |
| CAP-002 | [admin-portal/ui/screens/families/family-member-form.md](../applications/admin-portal/ui/screens/families/family-member-form.md) | `UI-ADM-011-01` … `UI-ADM-011-04` (4) | Primary | Content |
| CAP-002 | [admin-portal/ui/screens/families/member-login-form.md](../applications/admin-portal/ui/screens/families/member-login-form.md) | `UI-ADM-012-01` … `UI-ADM-012-04` (4) | Supporting | Content |
| CAP-002 | [admin-portal/ui/screens/members/member-detail.md](../applications/admin-portal/ui/screens/members/member-detail.md) | `UI-ADM-018-01` … `UI-ADM-018-04` (4) | Primary | Content |
| CAP-002 | [admin-portal/ui/screens/members/member-form.md](../applications/admin-portal/ui/screens/members/member-form.md) | `UI-ADM-019-01` … `UI-ADM-019-04` (4) | Primary | Content |
| CAP-002 | [admin-portal/ui/screens/members/member-list.md](../applications/admin-portal/ui/screens/members/member-list.md) | `UI-ADM-020-01` … `UI-ADM-020-04` (4) | Primary | Content |
| CAP-002 | [member-portal/ui/screens/family/family.md](../applications/member-portal/ui/screens/family/family.md) | `UI-MEM-003-01` … `UI-MEM-003-04` (4) | Primary | Content |
| CAP-002 | [member-portal/ui/screens/me/profile.md](../applications/member-portal/ui/screens/me/profile.md) | `UI-MEM-008-01` … `UI-MEM-008-04` (4) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/batches/batch-detail.md](../applications/admin-portal/ui/screens/batches/batch-detail.md) | `UI-ADM-004-01` … `UI-ADM-004-04` (4) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/batches/batch-form.md](../applications/admin-portal/ui/screens/batches/batch-form.md) | `UI-ADM-005-01` … `UI-ADM-005-05` (5) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/batches/batch-list.md](../applications/admin-portal/ui/screens/batches/batch-list.md) | `UI-ADM-006-01` … `UI-ADM-006-05` (5) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/programs/program-form.md](../applications/admin-portal/ui/screens/programs/program-form.md) | `UI-ADM-025-01` … `UI-ADM-025-04` (4) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/programs/program-list.md](../applications/admin-portal/ui/screens/programs/program-list.md) | `UI-ADM-026-01` … `UI-ADM-026-04` (4) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/trainers/trainer-detail.md](../applications/admin-portal/ui/screens/trainers/trainer-detail.md) | `UI-ADM-032-01` … `UI-ADM-032-04` (4) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/trainers/trainer-form.md](../applications/admin-portal/ui/screens/trainers/trainer-form.md) | `UI-ADM-033-01` … `UI-ADM-033-04` (4) | Primary | Content |
| CAP-004 | [admin-portal/ui/screens/trainers/trainer-list.md](../applications/admin-portal/ui/screens/trainers/trainer-list.md) | `UI-ADM-034-01` … `UI-ADM-034-04` (4) | Primary | Content |
| CAP-004 | [member-portal/ui/screens/courses/course-detail.md](../applications/member-portal/ui/screens/courses/course-detail.md) | `UI-MEM-001-01` … `UI-MEM-001-04` (4) | Primary | Content |
| CAP-004 | [member-portal/ui/screens/courses/courses.md](../applications/member-portal/ui/screens/courses/courses.md) | `UI-MEM-002-01` … `UI-MEM-002-03` (3) | Primary | Content |
| CAP-005 | [admin-portal/ui/screens/enrollments/enrollment-form.md](../applications/admin-portal/ui/screens/enrollments/enrollment-form.md) | `UI-ADM-008-01` … `UI-ADM-008-04` (4) | Primary | Content |
| CAP-005 | [admin-portal/ui/screens/enrollments/enrollment-list.md](../applications/admin-portal/ui/screens/enrollments/enrollment-list.md) | `UI-ADM-009-01` … `UI-ADM-009-04` (4) | Primary | Content |
| CAP-005 | [admin-portal/ui/screens/reports/enrollment-report.md](../applications/admin-portal/ui/screens/reports/enrollment-report.md) | `UI-ADM-028-01` … `UI-ADM-028-04` (4) | Supporting | Content |
| CAP-006 | [admin-portal/ui/screens/batches/batch-detail.md](../applications/admin-portal/ui/screens/batches/batch-detail.md) | `UI-ADM-004-01` … `UI-ADM-004-04` (4) | Supporting | Content |
| CAP-006 | [admin-portal/ui/screens/reports/attendance-report.md](../applications/admin-portal/ui/screens/reports/attendance-report.md) | `UI-ADM-027-01` … `UI-ADM-027-04` (4) | Supporting | Content |
| CAP-006 | [admin-portal/ui/screens/sessions/session-attendance.md](../applications/admin-portal/ui/screens/sessions/session-attendance.md) | `UI-ADM-030-01` … `UI-ADM-030-04` (4) | Primary | Content |
| CAP-006 | [admin-portal/ui/screens/sessions/session-form.md](../applications/admin-portal/ui/screens/sessions/session-form.md) | `UI-ADM-031-01` … `UI-ADM-031-04` (4) | Primary | Content |
| CAP-007 | [admin-portal/ui/screens/payments/payment-form.md](../applications/admin-portal/ui/screens/payments/payment-form.md) | `UI-ADM-023-01` … `UI-ADM-023-04` (4) | Primary | Content |
| CAP-007 | [admin-portal/ui/screens/payments/payment-list.md](../applications/admin-portal/ui/screens/payments/payment-list.md) | `UI-ADM-024-01` … `UI-ADM-024-04` (4) | Primary | Content |
| CAP-007 | [admin-portal/ui/screens/reports/payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Supporting | Content |
| CAP-008 | [admin-portal/ui/screens/groups/group-detail.md](../applications/admin-portal/ui/screens/groups/group-detail.md) | `UI-ADM-015-01` … `UI-ADM-015-05` (5) | Primary | Content |
| CAP-008 | [admin-portal/ui/screens/groups/group-form-dialog.md](../applications/admin-portal/ui/screens/groups/group-form-dialog.md) | `UI-ADM-016-01` … `UI-ADM-016-04` (4) | Primary | Content |
| CAP-008 | [admin-portal/ui/screens/groups/group-list.md](../applications/admin-portal/ui/screens/groups/group-list.md) | `UI-ADM-017-01` … `UI-ADM-017-04` (4) | Primary | Content |
| CAP-008 | [member-portal/ui/screens/groups/group-detail.md](../applications/member-portal/ui/screens/groups/group-detail.md) | `UI-MEM-004-01` … `UI-MEM-004-05` (5) | Primary | Content |
| CAP-008 | [member-portal/ui/screens/groups/groups.md](../applications/member-portal/ui/screens/groups/groups.md) | `UI-MEM-005-01` … `UI-MEM-005-03` (3) | Primary | Content |
| CAP-008 | [member-portal/ui/screens/home/home.md](../applications/member-portal/ui/screens/home/home.md) | `UI-MEM-006-01` … `UI-MEM-006-04` (4) | Uncertain | Content |
| CAP-009 | [admin-portal/ui/screens/groups/group-detail.md](../applications/admin-portal/ui/screens/groups/group-detail.md) | `UI-ADM-015-01` … `UI-ADM-015-05` (5) | Supporting | Content |
| CAP-009 | [member-portal/ui/screens/groups/group-detail.md](../applications/member-portal/ui/screens/groups/group-detail.md) | `UI-MEM-004-01` … `UI-MEM-004-05` (5) | Supporting | Content |
| CAP-009 | [member-portal/ui/screens/home/home.md](../applications/member-portal/ui/screens/home/home.md) | `UI-MEM-006-01` … `UI-MEM-006-04` (4) | Uncertain | Content |
| CAP-009 | [member-portal/ui/screens/posts/post-detail.md](../applications/member-portal/ui/screens/posts/post-detail.md) | `UI-MEM-009-01` … `UI-MEM-009-04` (4) | Primary | Content |
| CAP-009 | [member-portal/ui/screens/posts/posts.md](../applications/member-portal/ui/screens/posts/posts.md) | `UI-MEM-010-01` … `UI-MEM-010-02` (2) | Primary | Content |
| CAP-010 | [admin-portal/ui/screens/notifications/notification-list.md](../applications/admin-portal/ui/screens/notifications/notification-list.md) | `UI-ADM-021-01` … `UI-ADM-021-04` (4) | Primary | Content |
| CAP-010 | [admin-portal/ui/screens/notifications/send-notification.md](../applications/admin-portal/ui/screens/notifications/send-notification.md) | `UI-ADM-022-01` … `UI-ADM-022-05` (5) | Primary | Content |
| CAP-011 | [admin-portal/ui/screens/audit-logs/audit-log-list.md](../applications/admin-portal/ui/screens/audit-logs/audit-log-list.md) | `UI-ADM-001-01` … `UI-ADM-001-04` (4) | Primary | Content |
| CAP-011 | [admin-portal/ui/screens/audit-logs/log-diff-dialog.md](../applications/admin-portal/ui/screens/audit-logs/log-diff-dialog.md) | `UI-ADM-002-01` … `UI-ADM-002-03` (3) | Primary | Content |
| CAP-011 | [admin-portal/ui/screens/features/dashboard.md](../applications/admin-portal/ui/screens/features/dashboard.md) | `UI-ADM-007-01` … `UI-ADM-007-02` (2) | Primary | Content |
| CAP-011 | [admin-portal/ui/screens/reports/attendance-report.md](../applications/admin-portal/ui/screens/reports/attendance-report.md) | `UI-ADM-027-01` … `UI-ADM-027-04` (4) | Primary | Content |
| CAP-011 | [admin-portal/ui/screens/reports/enrollment-report.md](../applications/admin-portal/ui/screens/reports/enrollment-report.md) | `UI-ADM-028-01` … `UI-ADM-028-04` (4) | Primary | Content |
| CAP-011 | [admin-portal/ui/screens/reports/payment-report.md](../applications/admin-portal/ui/screens/reports/payment-report.md) | `UI-ADM-029-01` … `UI-ADM-029-04` (4) | Primary | Content |

## 4. Workflows → capabilities

| Workflow | Capabilities | Basis |
|---|---|---|
| [WF-AUTHENTICATION](../workflows/authentication.md) | CAP-001 (Primary) | Participating-requirements list in the workflow |
| [WF-FAMILY-REGISTRATION](../workflows/family-registration.md) | CAP-002 (Primary), CAP-001 (Supporting) | Participating-requirements list in the workflow |
| [WF-GROUP-CONTENT](../workflows/group-content.md) | CAP-009 (Primary), CAP-008 (Supporting) | Participating-requirements list in the workflow |
| [WF-GROUP-MEMBERSHIP](../workflows/group-membership.md) | CAP-008 (Primary) | Participating-requirements list in the workflow |
| [WF-INVITATIONS](../workflows/invitations.md) | CAP-003 (Primary), CAP-002 (Supporting), CAP-001 (Supporting) | Participating-requirements list in the workflow |
| [WF-PAYMENTS-AND-NOTIFICATIONS](../workflows/payments-and-notifications.md) | CAP-007 (Primary), CAP-010 (Primary) | Participating-requirements list in the workflow |
| [WF-PROGRAM-ENROLLMENT](../workflows/program-enrollment.md) | CAP-004 (Primary), CAP-005 (Primary) | Participating-requirements list in the workflow |
| [WF-SESSION-ATTENDANCE](../workflows/session-attendance.md) | CAP-006 (Primary), CAP-004 (Supporting) | Participating-requirements list in the workflow |

## 5. Technical documents

| Document | Capability | Relationship |
|---|---|---|
| [admin-portal/technical/authentication.md](../applications/admin-portal/technical/authentication.md) | CAP-001 | Primary |
| [api-backend/technical/authentication.md](../applications/api-backend/technical/authentication.md) | CAP-001 | Primary |
| [member-portal/technical/authentication.md](../applications/member-portal/technical/authentication.md) | CAP-001 | Primary |
| [api-backend/technical/enrollment-contract.md](../applications/api-backend/technical/enrollment-contract.md) | CAP-005 | Primary |
| [admin-portal/technical/api-integration.md](../applications/admin-portal/technical/api-integration.md) | — | Cross-cutting (architecture, endpoints, navigation or integration) |
| [admin-portal/technical/architecture.md](../applications/admin-portal/technical/architecture.md) | — | Cross-cutting (architecture, endpoints, navigation or integration) |
| [api-backend/technical/architecture.md](../applications/api-backend/technical/architecture.md) | — | Cross-cutting (architecture, endpoints, navigation or integration) |
| [api-backend/technical/endpoints.md](../applications/api-backend/technical/endpoints.md) | — | Cross-cutting (architecture, endpoints, navigation or integration) |
| [member-portal/technical/architecture.md](../applications/member-portal/technical/architecture.md) | — | Cross-cutting (architecture, endpoints, navigation or integration) |
| [member-portal/technical/navigation.md](../applications/member-portal/technical/navigation.md) | — | Cross-cutting (architecture, endpoints, navigation or integration) |

## 6. Shared UI/layout requirements (intentionally not assigned to a capability)

Each document was reviewed individually. Layouts are referenced once from [index.md](./index.md); they are not repeated in capability files.

| Document | IDs (count) | Classification | Reason |
|---|---|---|---|
| [admin-portal/ui/layouts/main-layout.md](../applications/admin-portal/ui/layouts/main-layout.md) | `UI-ADM-LAY-MAI-001` (1) | Referenced via common layout | Layout; includes sign-out, so it also supports CAP-001 |
| [admin-portal/ui/layouts/sidebar.md](../applications/admin-portal/ui/layouts/sidebar.md) | `UI-ADM-LAY-SID-001` (1) | Referenced via common layout | Layout; role-based navigation (supports NFR-SEC-003 presentation only) |
| [member-portal/ui/layouts/app-shell.md](../applications/member-portal/ui/layouts/app-shell.md) | `UI-MEM-LAY-001` (1) | Referenced via common layout | Layout shell |
| [member-portal/ui/layouts/tabs/tabs.md](../applications/member-portal/ui/layouts/tabs/tabs.md) | `UI-MEM-011-01` … `UI-MEM-011-04` (4) | Referenced via common layout | Layout; navigates to home, groups, courses, posts and profile areas |
| [admin-portal/ui/shared/confirm-dialog.md](../applications/admin-portal/ui/shared/confirm-dialog.md) | `UI-ADM-SHR-CON-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/data-table.md](../applications/admin-portal/ui/shared/data-table.md) | `UI-ADM-SHR-DAT-001` … `UI-ADM-SHR-DAT-002` (2) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/icon.md](../applications/admin-portal/ui/shared/icon.md) | `UI-ADM-SHR-ICO-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/loading-overlay.md](../applications/admin-portal/ui/shared/loading-overlay.md) | `UI-ADM-SHR-LOD-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/page-header.md](../applications/admin-portal/ui/shared/page-header.md) | `UI-ADM-SHR-PGH-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/status-badge.md](../applications/admin-portal/ui/shared/status-badge.md) | `UI-ADM-SHR-STA-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/tailwind-dialog-host.md](../applications/admin-portal/ui/shared/tailwind-dialog-host.md) | `UI-ADM-SHR-DHO-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/tailwind-modal.md](../applications/admin-portal/ui/shared/tailwind-modal.md) | `UI-ADM-SHR-MOD-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/tailwind-paginator.md](../applications/admin-portal/ui/shared/tailwind-paginator.md) | `UI-ADM-SHR-PAG-001` (1) | Cross-cutting component | Reusable component |
| [admin-portal/ui/shared/toast-region.md](../applications/admin-portal/ui/shared/toast-region.md) | `UI-ADM-SHR-TOA-001` (1) | Cross-cutting component | Reusable component |

## 7. Corrections to the earlier mapping

| # | Earlier mapping | Corrected mapping | Reason (requirement content) |
|---|---|---|---|
| 1 | `UI-MEM-006` (member Home, 4 requirements) → CAP-002 | CAP-008 and CAP-009, relationship **Uncertain** | The screen offers "Find your community", "Recent updates" and navigates to `/groups` and `/posts`; it contains no profile/family content. |
| 2 | `ADM-FAM-009` → CAP-002 | CAP-001 Primary, CAP-002 Supporting | Requirement is about creating, updating and deactivating member logins. |
| 3 | `API-MEM-005`, `API-MEM-006` → CAP-002 | CAP-001 Primary, CAP-002 Supporting | Both manage login credentials linked to a profile. |
| 4 | `WF-FAMILY-REGISTRATION` → CAP-002, CAP-003 | CAP-002 Primary, CAP-001 Supporting | The workflow lists family, member, admin-login and member-login requirements and no invitation requirement. |
| 5 | `WF-INVITATIONS` → CAP-003 | CAP-003 Primary; CAP-002, CAP-001 Supporting | The workflow also lists family, member and authentication requirements. |
| 6 | `WF-SESSION-ATTENDANCE` → CAP-006 | CAP-006 Primary, CAP-004 Supporting | The workflow includes batch requirements (`API-BAT-*`). |
| 7 | `WF-GROUP-CONTENT` → CAP-009 | CAP-009 Primary, CAP-008 Supporting | The workflow includes `API-GRP-001..011`. |
| 8 | Group detail UI (`UI-ADM-015`, `UI-MEM-004`) → CAP-008 only | CAP-008 Primary, CAP-009 Supporting | The screens list posts, events and polls actions. |

Supporting relationships added for requirements that touch a second capability (e.g. `API-ENR-010`, `API-PAY-010`, `API-SES-003`, `ADM-DSH-001`, report requirements) are additions, not corrections.

## 8. Requirements without a confident mapping

| Item | Status |
|---|---|
| `UI-MEM-006-01..04` (member Home) | Mapped as **Uncertain** to CAP-008 and CAP-009; an aggregate landing screen. Decide whether a home/dashboard belongs to a capability. |
| 18 shared layout/component requirements (section 6) | Intentionally not assigned. |
| `ADM-AUTH-007`, `ADM-AUTH-008`, `API-AUTH-003` | Mapped to CAP-001 as **Cross-cutting**: they gate every protected capability. |
| 6 technical documents (architecture, endpoints, navigation, API integration) | Cross-cutting; not assigned. |

## 9. Requirement-document inconsistencies (existing text unchanged)

| # | Finding | Location |
|---|---|---|
| 1 | ID gaps: `API-AUTH-001/002`, `API-PRG-002`, `API-PST-001..004`, `ADM-AUTH-002..006`, `MEM-AUTH-001, 003..008`, `MEM-PRG-001`. Do not reuse without a decision. | functional docs |
| 2 | One file mixes prefixes: `API-POST-001..005` and `API-PST-005..013`. | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| 3 | `API-GRP-033`, `034` appear before `026`; `API-FAM-019` before `007`; `API-MEM-012`, `011` before `008..010`. Numbering is not in document order. | groups.md, families.md, members.md |
| 4 | Near-duplicates (**existing but duplicated**): `API-GRP-012`/`030`, `013`+`014`/`031`, `015`/`032`, `016`/`024`, `017`/`025`. Retained as separate requirements. | [groups.md](../applications/api-backend/functional/groups.md) |
| 5 | UI IDs are screen-level with sub-IDs (`UI-ADM-003-01..08`); a UI file therefore shows one prefix and several headings. | UI docs |
| 6 | `API-ATT-004..011` and `API-ENR-004..015` are validation details of broader requirements; kept atomic. | attendance.md, enrollments.md |

## 10. Documentation gaps (behavior found in source, no requirement)

| # | Gap | Evidence | Capability |
|---|---|---|---|
| 1 | Member notifications screen | route `notifications` in `kcdf-parents-app/src/app/app.routes.ts` | CAP-010 |
| 2 | Member invitation acceptance screen | route `invitations/:code`; `kcdf-parents-app/src/app/invitations/` | CAP-003 |
| 3 | Member voting and event registration are described only in `UI-MEM-004` criteria; no `MEM-*` functional requirement | `group-detail.page.ts:302`; `core/member-auth.service.ts:386-394` | CAP-009 |
| 4 | Admin posts/events/polls management is only listed as UI actions in `UI-ADM-015`; no `ADM-*` functional requirement | `group-detail.component.ts` `openContentDialog` | CAP-009 |
| 5 | Entities and member-entity relations have API requirements but no UI requirement or screen | `api/routes.php` | CAP-002 |

(Correction to the earlier version of this file: gap 4 previously claimed no admin UI requirement mentions content management; `UI-ADM-015` does list those actions.)

## 11. Source observations (not confirmed defects)

| # | Observation | Classification |
|---|---|---|
| 1 | The admin app has no route for `/admins`, `/invitations`, `/entities`. | API-only functionality; product decision needed. |
| 2 | No OTP or self-registration code exists in `api-backend-php/`. | Context only; nothing implemented. |
| 3 | `notifications.type` accepts `push`/`email` without a delivery integration. | Unknown delivery. |
| 4 | Payments are manually recorded; no gateway. | Consistent with `API-PAY-*`. |
| 5 | `kcdf-parents-app/src/app/core/sample-content.ts` is not imported anywhere. | Possible dead file. |
| 6 | `API-ENR-007` says "open for enrollment"; code allows batch status `upcoming` or `active` (`api/enrollments/store.php:30`). | Wording to confirm. |
| 7 | The member app has no enrollment, payment or attendance screens although the API allows primary members to enroll. | Documented API behavior without member UI. |

## 12. Ambiguous capability boundaries and overlaps

- **CAP-001 vs CAP-002:** member login accounts (credentials) vs member profiles; mapped as Primary/Supporting as in section 7.
- **CAP-004 vs CAP-006:** sessions belong to batches; session scheduling is CAP-006 Primary with CAP-004 Supporting.
- **CAP-005 vs CAP-007:** enrollment fee/payment state is split (`API-ENR-010` vs `API-PAY-010`, `-013`).
- **CAP-008 vs CAP-009:** content rules depend on group roles; `API-PST-005`, `-006`, `-013` carry CAP-008 as Supporting.
- **CAP-010:** no payment-to-notification trigger is documented although the workflow groups both.
- Trainers sit in CAP-004; they could become their own capability if the area grows (not proposed now).

## 13. NFR integration review

- Every capability file links baseline NFRs separately from proposed NFRs; no new NFR was created and no NFR text or status was changed.
- Questionable classifications in [../non-functional/index.md](../non-functional/index.md) (not changed here): `NFR-SEC-003`, `NFR-REL-002` and `NFR-PRF-001` are listed in the baseline although marked **Partial** (full coverage unverified); `NFR-MNT-002` mixes a verified script set with an unverified process.
- `NFR-OBS-002` says "about 45 handlers call `logActivity`" while some record a null actor (`api/enrollments/cancel.php:27`); accuracy of actor capture is unverified.
- Missing or unapproved targets remain **TBD — requires approval** (performance, availability, RPO/RTO, browsers, accessibility level).
