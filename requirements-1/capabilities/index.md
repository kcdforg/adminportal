# Capability Catalog

## Overview

A capability is a business function of the KCDF Parents platform that can span the API, admin portal and member portal. Each capability has one file that groups the existing requirements, workflows, UI documents, technical documents and NFRs that relate to it. Capability files are navigation and traceability documents; they do not contain requirement text.

## Capability Catalog

Status uses: Implemented, Partially implemented, Documented only, Proposed, Unknown (route/handler-level and targeted code checks only; see each file and [traceability.md](./traceability.md)).

| ID | Capability | Business Purpose | Applications | Status |
|---|---|---|---|---|
| CAP-001 | [Identity, Authentication & Access Control](./CAP-001-identity-authentication-access-control.md) | Sign in, session refresh, role-based access, administrator accounts and member login accounts | API, Admin, Member | Implemented |
| CAP-002 | [Family & Member Management](./CAP-002-family-member-management.md) | Families, member profiles, family-member relationships and organizations (entities) | API, Admin, Member | Partially implemented |
| CAP-003 | [Invitations & Member Onboarding](./CAP-003-invitations-member-onboarding.md) | Invite people and let them accept to create their login | API, Member | Partially implemented |
| CAP-004 | [Program, Batch & Trainer Management](./CAP-004-program-batch-trainer-management.md) | Maintain programs, batches and trainers; let members browse programs | API, Admin, Member | Implemented |
| CAP-005 | [Enrollment Management](./CAP-005-enrollment-management.md) | Enroll and cancel family members in batches | API, Admin | Partially implemented |
| CAP-006 | [Sessions & Attendance](./CAP-006-sessions-attendance.md) | Schedule sessions, record and correct attendance, lock attendance | API, Admin | Implemented |
| CAP-007 | [Payments](./CAP-007-payments.md) | Record, correct and review payments, including refunds | API, Admin | Partially implemented |
| CAP-008 | [Group Management & Membership](./CAP-008-group-management-membership.md) | Groups, categories, joining and leaving, roles and bans | API, Admin, Member | Implemented |
| CAP-009 | [Group Content (Posts, Events, Polls)](./CAP-009-group-content.md) | Announcements, events with registration, polls with voting | API, Admin, Member | Partially implemented |
| CAP-010 | [Notifications](./CAP-010-notifications.md) | In-app notifications; direct and broadcast sending | API, Admin, Member | Partially implemented |
| CAP-011 | [Reporting, Dashboard & Audit](./CAP-011-reporting-dashboard-audit.md) | Dashboard, attendance/payment/enrollment reports and activity-log review | API, Admin | Implemented |

## Capability Relationships

- CAP-001 (identity) is required by every other capability; CAP-002 (families and members) is the data foundation for CAP-003, CAP-005, CAP-007 and CAP-008.
- CAP-004 (programs/batches) → CAP-005 (enrollment) → CAP-007 (payment status); CAP-004 → CAP-006 (sessions and attendance).
- CAP-008 (groups) → CAP-009 (group content).
- CAP-011 reports on CAP-005, CAP-006, CAP-007 and audits all write operations; CAP-010 serves all capabilities.

## Shared layouts and cross-cutting documents

Not assigned to a capability (details in [traceability.md](./traceability.md), section 6):

- Layouts: [admin main layout](../applications/admin-portal/ui/layouts/main-layout.md), [sidebar](../applications/admin-portal/ui/layouts/sidebar.md), [member app shell](../applications/member-portal/ui/layouts/app-shell.md), [member tabs](../applications/member-portal/ui/layouts/tabs/tabs.md).
- Shared admin components under [applications/admin-portal/ui/shared/](../applications/admin-portal/ui/shared).
- Architecture/endpoints/navigation/integration technical documents under `applications/*/technical/`.

System-wide NFRs that apply to every capability: `NFR-SEC-004`, `NFR-SEC-005`, `NFR-SEC-006`, `NFR-OBS-003`, `NFR-MNT-001`, `NFR-MNT-002` (baseline); `NFR-SEC-010`, `NFR-SEC-012`, `NFR-AVL-001`, `NFR-MNT-003`, `NFR-CMP-001`, `NFR-ACC-001`, `NFR-OBS-004` (proposed, not approved). Definitions: [../non-functional/index.md](../non-functional/index.md).

## Traceability

- [traceability.md](./traceability.md) — requirement-to-capability mapping, corrections, gaps.
- [review-summary.md](./review-summary.md) — counts, verification and decisions needed.
- Application requirements: [../applications/](../applications/); workflows: [../workflows/](../workflows/); NFRs: [../non-functional/index.md](../non-functional/index.md), [audit](../non-functional/audit.md).

## Maintenance Rules

- Capability files group existing requirements; application requirements remain authoritative.
- Workflows describe business processes; UI requirements define screen behavior; NFRs define quality constraints.
- When a requirement is added, changed or retired, update the related capability file, [traceability.md](./traceability.md) and affected workflow references in the same change set. Never renumber requirement IDs.
- A new capability needs user approval and is only justified by a genuinely distinct business function.
- Proposed NFRs must stay marked proposed until the product owner approves them.
