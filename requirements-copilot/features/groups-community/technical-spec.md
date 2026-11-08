# Groups and community — technical specification

**Evidence status:** Static route/handler/client/schema/test-source trace. No runtime results or product-policy choices.

| ID | Current behavior and interfaces | Status / boundary |
|---|---|---|
| GRP-001 | `GET /groups` and `/groups/{id}` use group handlers and visibility/membership helpers; admin and Ionic list/detail surfaces exist. Member listing includes public visibility and active membership; public inactive/archived visibility is not consistently filtered. | **Partial**; visibility/status policy unresolved. |
| GRP-002 | Group/category creation and update/status handlers validate fields, category and role; category duplicate check; platform/community admin create, owner/admin update. Admin group service/forms consume registered endpoints. | **Observed** |
| GRP-003 | Member listing/add/join/leave/remove/role endpoints with membership and role checks; unique group/member relationship and membership state in schema. Admin and Ionic clients expose subsets. | **Partial**; client covers only some actions; public-join and role constraints source-defined. |
| GRP-004 | Resource loop registers GET/POST at `/groups/{group_id}/posts` and GET/PUT/DELETE per content ID; shared `api/groups/resources.php` maps posts to announcement content. `group_contents` stores content. | **Observed backend**; OpenAPI omits these operations. Sample standalone posts are unrelated. |
| GRP-005 | Poll content uses same resource handler plus vote/results routes; poll options/settings, membership, open/published state and results visibility checked in source; vote rows in `group_poll_votes`. | **Observed backend**; OpenAPI omits routes; selected tests do not establish end-to-end poll behavior. |
| GRP-006 | Event content uses resource handler and registration endpoints; registration capacity/date/ownership and state checks; `group_event_registrations` stores registration. | **Observed backend**; OpenAPI omits routes; no runtime verification. |

## Interfaces and source

- Route registry: `api-backend-php/api/routes.php` includes a loop over `posts`, `polls`, `events` (GET/POST collection; GET/PUT/DELETE content ID) and explicit poll/event actions.
- Handlers/helpers: `api-backend-php/api/groups/*.php`, especially `resources.php`; `services/community-notifications.php`.
- Schema: `group_categories`, `parent_groups`, `group_members`, `group_contents`, `group_poll_votes`, `group_event_registrations` in `api-backend-php/database/schema.sql`.
- Clients: admin `core/services/group.service.ts`, `features/groups/`; member `groups/groups.page.ts`, `groups/group-detail.page.ts`. Standalone post/home content uses `core/sample-content.ts::SAMPLE_POSTS`.
- OpenAPI omission and route enumeration: `docs/architecture/api-contract-audit.md`.

## Unresolved matters

Public inactive/archived group visibility and contract publication intent remain unresolved. Do not equate standalone sample posts with the `posts` resource or assert requirements for sample replacement. See provisional audit `baseline-workflow/analysis/requirements-accuracy-audit.md`.
