# Functional requirements index

**Phase:** 04 — Functional requirements reconstruction  
**Evidence status:** Provisional as-is reconstruction from current source and Phase 02 analyses. Requirements do not constitute user-approved business policy or runtime verification.

## Requirement sets

| Feature | Requirements | IDs | Source status / important boundary |
|---|---|---|---|
| [Authentication and access](./features/authentication-access/requirements.md) | Admin/member authentication, token lifecycle, current-account context, admin login-account management and API authorization boundaries | AUTH-001–AUTH-007 | Backend source observed; admin-account UI is unconnected; client refresh behavior differs between apps; intended policy remains unverified. |
| [Members and families](./features/members-families/requirements.md) | Profiles, member login accounts, families, family membership and access | FAM-001–FAM-006 | Backend source observed; FAM-006 is a traceability cross-reference to ENT-002, not a separate capability; client coverage is partial in places. |
| [Invitations and entities](./features/invitations-entities/requirements.md) | Invitation lifecycle, entity records and member/entity relations | INV-001–INV-003, ENT-001–ENT-002 | Backend source observed; no app consumer was found in the bounded client review. |
| [Programs and participation](./features/programs-participation/requirements.md) | Programs, trainers, batches, enrollments, sessions and attendance | PROG-001–PROG-007 | Backend source observed; admin API contracts have mismatches; member courses are sample-backed. |
| [Groups and community](./features/groups-community/requirements.md) | Group discovery, membership/candidate discovery, announcements, polls and events | GRP-001–GRP-006 | Backend and group-detail flows source-observed; standalone posts use samples; OpenAPI omits content operations. |
| [Payments and communications](./features/payments-communications/requirements.md) | Payments/refunds and notification list/send/broadcast behavior | PAY-001–PAY-004, NOTIF-001–NOTIF-003 | Backend source observed; admin request/list mismatches; external delivery unverified. |
| [Dashboard, reports and audit](./features/reporting-audit/requirements.md) | Dashboard data requests, report UI surfaces and activity log | DASH-001, AUD-001 | Dashboard/audit source observed; report pages have no matching backend routes in the checked source. |

## Disposition of other discovered surfaces

- **Sample-backed member courses and standalone posts:** documented as mock/sample behavior, not assigned a backend functional requirement. No source evidence establishes that these pages implement program enrollment or group announcements.
- **Admin report pages:** UI calls are recorded in the reporting/audit set as partial surfaces. Since no matching API handler was found, no report calculation or data contract is asserted as a requirement.
- **Unconnected invitation/entity/admin-account APIs:** their backend operations are captured below, but no consumer or intended external workflow is inferred.
- **Unresolved policy:** code-observed role behavior, invitation contact matching/family linkage, group visibility, cancelled-enrollment reuse, and notification delivery are explicitly left open.

## Reverse-coverage exceptions and open capability decisions

- **Admin-account management:** AUTH-006 records the observed `/admins` API as an API-only capability. No app consumer was found; intended UI/external use remains unconfirmed.
- **Current-account endpoint:** AUTH-007 records the observed `/auth/me` response consumed by the admin client; a member-client consumer was not found.
- **Group member candidates:** Included explicitly in GRP-003 acceptance criteria and API evidence.
- **Member/entity relations:** ENT-002 is the canonical behavior; FAM-006 remains only as a stable cross-reference to avoid dropping existing traceability.
- **Sample-backed member courses and standalone posts, report pages without backend handlers, member notification UI/automatic refresh, and provider delivery:** remain exceptions pending product direction. No desired behavior is inferred from sample data, page labels, or backend route existence.

## ID and evidence rules

IDs are unique within this index and the linked files. Keep them stable; append new IDs rather than renumbering. “Observed” means current source declares or performs the behavior, not that it was tested or accepted as intended policy. “Partial/mismatched,” “sample/mock,” and “unverified” mark gaps and are not rewritten as desired future behavior.

There are 40 stable IDs across seven feature sets; one pair (FAM-006/ENT-002) describes a single capability and is explicitly cross-referenced to avoid duplicate scope. Phase 02 analyses and source paths in each set are evidence references. Tests were read but not run. No requirement asserts runtime success or product-owner approval.
