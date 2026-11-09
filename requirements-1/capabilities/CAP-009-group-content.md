# CAP-009: Group Content (Posts, Events, Polls)

## 1. Purpose

Let group managers share announcements, events and polls and let members take part.

## 2. Scope

Posts/announcements, events and registration, polls, voting and results, and the shared content rules (titles, length, visibility, archiving, pagination).

## 3. Sub-Capabilities

- Announcements/posts
- Events and registration
- Polls and voting
- Shared content rules (`API-PST-005..013`)

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-EVT-001` | Create group event | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-002` | Update group event | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-003` | Register for an event | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-004` | View own event registration | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-005` | Cancel own event registration | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-006` | Validate event dates | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-007` | Validate event capacity | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-008` | Require active membership to register | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-009` | Reject registration for unavailable events | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-010` | Prevent duplicate active registrations | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-011` | Enforce event registration capacity | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-012` | Reactivate cancelled registrations | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-EVT-013` | Cancel only own active registration | Primary | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-POL-001` | Create group poll | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-002` | Update or publish group poll | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-003` | Cast or change vote | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-004` | View poll results | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-005` | Validate poll option count | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-006` | Validate individual poll options | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-007` | Require active membership to vote | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-008` | Reject voting on closed polls | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-009` | Validate selected vote options | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-010` | Enforce vote-change setting | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-011` | Restrict poll results visibility | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-012` | Prevent editing polls after voting | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POL-013` | Count poll responses by option | Primary | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-POST-001` | List group announcements | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-POST-002` | Read group announcement | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-POST-003` | Create group announcement | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-POST-004` | Edit group announcement | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-POST-005` | Archive group announcement | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-005` | Restrict content creation to group managers | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-006` | Reject content creation in inactive groups | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-007` | Validate content titles | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-008` | Limit content body length | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-009` | Restrict unpublished content visibility | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-010` | Paginate group content listings | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-011` | Restrict status changes to settings managers | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-012` | Archive content instead of deleting | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-PST-013` | Authorize content editing and archiving | Primary | [group-posts.md](../applications/api-backend/functional/group-posts.md) |

### Ionic Member Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `MEM-POST-001` | Browse group posts | Primary | [posts.md](../applications/member-portal/functional/posts.md) |
| `MEM-POST-002` | Read a post | Primary | [posts.md](../applications/member-portal/functional/posts.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-GROUP-CONTENT` | Primary | [group-content.md](../workflows/group-content.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Group Detail (admin) | `UI-ADM-015-01` … `UI-ADM-015-05` (5) | Supporting | [group-detail.md](../applications/admin-portal/ui/screens/groups/group-detail.md) |
| Group Detail (member) | `UI-MEM-004-01` … `UI-MEM-004-05` (5) | Supporting | [group-detail.md](../applications/member-portal/ui/screens/groups/group-detail.md) |
| Home (member) | `UI-MEM-006-01` … `UI-MEM-006-04` (4) | Uncertain | [home.md](../applications/member-portal/ui/screens/home/home.md) |
| Post Detail (member) | `UI-MEM-009-01` … `UI-MEM-009-04` (4) | Primary | [post-detail.md](../applications/member-portal/ui/screens/posts/post-detail.md) |
| Posts (member) | `UI-MEM-010-01` … `UI-MEM-010-02` (2) | Primary | [posts.md](../applications/member-portal/ui/screens/posts/posts.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-REL-002`, `NFR-PRF-001`
- **Proposed (not approved):** none specific
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-008 Group Management & Membership](./CAP-008-group-management-membership.md) — content permissions depend on group membership and role

## 10. Implementation Status

**Partially implemented.** API rules for poll options, title length and pagination, and event capacity were source-checked. Admin and member clients manage content (admin `openContentDialog`; member voting and event registration). Member requirements cover only browsing/reading posts (`MEM-POST-001/002`), so voting and event registration have no member requirements.

## 11. Source-Code Evidence

- `api-backend-php/api/groups/resources.php:107,116,170,210,283,364,527`; routes under `/groups/{id}/polls|events` in `api/routes.php`; migration `20261023_group_content.sql`.
- `kcdf-admin-app/src/app/features/groups/group-detail/` (`openContentDialog`); `kcdf-parents-app/src/app/groups/group-detail.page.ts:302` (`vote`), `core/member-auth.service.ts:386-394` (event registration), `posts/`. Database: `group_contents`, `group_poll_votes`, `group_event_registrations`.

Individually source-checked requirements for this capability: `API-EVT-007`, `API-POL-005`, `API-PST-007`, `API-PST-010`, `MEM-POST-001`. All other requirements were content-reviewed for mapping but not individually checked against code.

## 12. Known Gaps

- Member voting and event registration are described only in the `UI-MEM-004` group-detail acceptance criteria, not by a `MEM-*` functional requirement (`MEM-POST-001/002` cover reading posts only).
- Admin content management appears only as actions in `UI-ADM-015`, with no `ADM-*` functional requirement.
- `API-POST-001..005` and `API-PST-005..013` use different prefixes in one file; `API-PST-001..004` are absent.
- Content creation by group managers: whether "community administrator" in `API-PST-005` equals a platform role is not defined.
