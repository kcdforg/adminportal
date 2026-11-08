# Feature inventory

**Phase:** 01 — Repository discovery  
**Status:** Refreshed preliminary candidate inventory; requires user review  
**Refresh:** Static source/path checks repeated after approval of the revised Phase 01 plan; no runtime or test verification.  
**Evidence limit:** Entries below identify source-level surfaces and names, not verified end-to-end behavior or completed functionality.

## Candidate feature surfaces

| Candidate area | Backend evidence | Admin evidence | Member evidence | Primary discovery status and Phase 02 focus |
|---|---|---|---|---|
| Authentication and account access | Registered auth/admin/member routes and handlers under `api/auth/`, `api/admins/`, `api/members/` | Login route; auth service, auth/role guards and auth store | Login route and auth/API service in `src/app/core/member-auth.service.ts` | **Implemented evidence found** at source level (route/handler and client surfaces exist); runtime, policy and complete flow remain unverified. |
| Members, families, invitations and relations | Registered routes/handlers under `api/members/`, `api/families/`, `api/invitations/`, `api/entities/` | Members/families list, form and detail routes/services | Profile and family pages; member service methods | **Partial** as a combined candidate: concrete API and UI surfaces exist, but invitation and cross-layer mappings need separate tracing. |
| Programs, batches, trainers, sessions, enrollment and attendance | Registered routes/handlers under corresponding `api/` areas | Programs, batches, trainers, sessions, enrollment, attendance and report routes/services | Courses pages use `SAMPLE_COURSES`; `member-auth.service.ts` also exposes program/batch API methods | **Partial**: member course pages are sample-backed while related API methods exist; page-to-method wiring and course mapping are unverified. |
| Groups and community content | Group routes/handlers include `api/groups/resources.php` for group posts, polls and events; there is no separate `api/posts/` handler directory | Groups list/detail routes and group service | Group pages use member API service; posts pages import `SAMPLE_POSTS` | **Partial**: group API/client surfaces are present, while member post screens use sample data; relationship and end-to-end content flow require Phase 02 tracing. |
| Payments | `api/payments/` routes/handlers | Payments list/form and payment-report routes/services | No payment-named route/page observed | **Partial**: backend/admin surfaces found; member-side coverage not observed at discovery depth. |
| Notifications | `api/notifications/` routes/handlers | Notifications list/send routes and service | No notification-named route/page observed | **Implemented evidence found** for backend/admin source surfaces; recipient/client integration and runtime remain unverified. |
| Audit/activity records | `api/activity-logs/` routes/handlers | Audit-log route/feature and activity-log service | No corresponding route/page observed | **Partial**: backend/admin surfaces found; member coverage and complete data flow not established. |
| Reporting and dashboard | No route group named `reports` or `dashboard` observed | Dashboard and attendance/enrollment/payment report routes | No corresponding route/page observed | **Uncertain/not reviewed**: admin UI surfaces exist, but data sources and calculations were not examined. |
| Generic entities | `api/entities/` routes/handlers | No route named `entities` observed | No corresponding route/page observed | **Uncertain/not reviewed**: backend surface found; purpose and consumers unresolved. |
| Sample/static content | No separate content route area; group content routes are under `api/groups/` | No sample-content filename observed | `core/sample-content.ts`; courses and posts pages import sample constants | **Mock/stub** for the listed sample-backed pages only; other API client methods exist and page wiring was not traced. |

These are static discovery classifications, not claims of runtime correctness or business completeness. **Implemented evidence found** means cited route/handler/client source exists; **partial** marks a visible cross-layer gap or mixed source surface; **mock/stub** applies only to the explicitly sample-backed screens; **uncertain/not reviewed** means the available discovery evidence is insufficient. Names and structural paths alone do not establish that a feature works or is used.

## Proposed Phase 02 batches

Batch boundaries are provisional, based on the source-level surface inventory. The user should approve or revise them before Phase 02. Split any batch that proves too broad for evidence-backed review.

1. **Identity and access:** authentication, admins, members, families, invitations and entity relations.
2. **Programs and participation:** programs, batches, trainers, sessions, enrollments, attendance, reports related to participation, and member courses.
3. **Groups and community:** group categories/membership/content, member groups, posts and any backend/API correspondence.
4. **Payments and communications:** payments, notifications, reports, audit/activity records and their clients/permissions.
5. **Residual and reverse-coverage sweep:** generic entities and any remaining discovered routes, pages, services, tables, tests or API operations not covered by the preceding batches. Revisit uncertain mappings such as member courses and posts.

Phase 02 must first confirm the feature set from approved Phase 01 deliverables and may change these groupings with user approval. These batches are not authorization to analyze features now.

## Unresolved discovery questions

- Which client routes/services call which registered backend endpoints, and which route/page surfaces are only mock, partial, unused or unconnected?
- Are member `courses` represented by backend programs/batches or by another service/API?
- Do member posts use the group-scoped posts endpoints in `api/groups/resources.php`, or remain independent sample-backed screens?
- What purpose and consumers does the generic `entities` API have?
- Do backend tests cover these features, and what are the relationships among the schema, routes and test fixtures?

Resolve these from source evidence during approved Phase 02 analysis. Do not use historical prompts or requirements to answer them without current-source confirmation.
