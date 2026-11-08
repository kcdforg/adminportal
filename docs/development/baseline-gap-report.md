# Baseline gap report

**Evidence status:** Source-verified Pass 1 consolidation, static only. This report does not implement a fix, choose policy, or amend requirements. See `baseline-workflow/analysis/requirements-accuracy-audit.md`; its correction proposals remain unresolved.

## Implementation and integration gaps

| Area / IDs | Source-verified gap | Evidence / trace |
|---|---|---|
| API description coverage | 110 registered route operations (including 15 generated group-resource operations); OpenAPI contains 89 matching method/path operations and omits 21. No extra OpenAPI operations were found. | `api-backend-php/api/routes.php`, `api-backend-php/swagger/openapi.json`; full operation inventory and count method in `docs/architecture/api-contract-audit.md`. |
| AUTH-005 | Logout can return success when no matching eligible stored refresh-token row exists; code does not require an affected row. | `api/auth/logout.php`, `services/auth.php`, `schema.sql`; Phase 05 labels requirement contradicted. |
| AUTH-002, AUTH-004 | Source checks an existing member profile link but does not establish active-profile validation; refresh update does not establish concurrent single-use rotation. | `services/identity.php`, `api/auth/refresh.php`; keep eligibility/rotation policy unresolved. |
| FAM-001–FAM-004 | List/create/edit/view permissions differ by operation; generalized actor wording does not describe each operation boundary. | Member/family handlers and `services/identity.php` helpers; admin/member client coverage is partial. |
| FAM-006 / ENT-002 | Two requirement IDs map to the same entity-relation handlers, helper and schema. | `api/members/entity-relations*.php`, `canManageMemberRelations`, `entity_member_relations`; Phase 05 proposes clarifying scope, not changing IDs here. |
| INV-001 | Invitation handler permits community admins while writing inviter profile ID into a profile-keyed foreign-key column; standalone admin identity has no profile ID. | `api/invitations/store.php`, `schema.sql` (`invitations`, `member_profiles`); source mismatch, product/design decision unresolved. |
| INV-002 | Acceptance flow does not visibly match submitted contact to invitation target and does not visibly add family membership. | `api/invitations/accept.php`, invitation helpers; no target/family policy inferred. |
| PROG-001–007 | Admin requests differ from registered server interface: program/trainer/batch/session update verbs, missing batch member-add route, enrollment cancellation path/body, session lock path, and attendance payload key. Program/enrollment form fields/statuses also differ. | Admin core academic services/forms vs `api/routes.php`, handlers and `services/academics.php`; details in `requirements/features/programs-participation/technical-spec.md`. |
| PAY-001–003 | Client payment list date filters and form fields differ from server names/requirements; class-fee request omits required enrollment ID; no client payment-update call was found. | `payment.service.ts`, payment list/form vs `api/payments/*.php`, `services/payments.php`. |
| NOTIF-001–003 | No member notification page was found; server lists by profile ID without explicit member-portal check. Admin send/broadcast UI path and fields do not match server routes/body. Handler persistence does not establish provider delivery. | `api/notifications/*.php`, community helper, notification client/service; no external provider flow evidenced. |
| DASH-001 | Dashboard reads family, batch and payment list totals; payment list is accounts/super-admin limited, potentially unavailable to other dashboard roles. Notification count is placeholder. | `dashboard.component.ts`, services and payment access helper; no dashboard runtime result. |
| AUD-001 | Admin audit UI date/actor filters differ from server filters; source has GET listing and activity write helper, no update/delete route. | `activity-log.service.ts`, audit log component, `api/activity-logs/index.php`, `services/identity.php`. |
| GRP-001, GRP-003 | Member list can include public groups without imposing active status; client exposes only a subset of registered membership actions. | `api/groups/index.php`, community helpers, admin/member group pages. |
| GRP-004–006 | PHP implements group posts/polls/events and actions, but 21 content/member-candidate operations are omitted from OpenAPI; selected tests do not establish end-to-end content handling. | Resource loop, `api/groups/resources.php`, schema content tables, OpenAPI and test inventory. |
| Reporting (DASH-001 boundary) | Admin attendance/enrollment/payment report pages call paths with no matching registered PHP handler or OpenAPI operation. Calculation, authorization and export behavior are absent/unverified. | `kcdf-admin-app/src/app/features/reports/`, `api/routes.php`, `swagger/openapi.json`; no report requirement inferred. |
| Client coverage (INV/ENT, AUTH, GRP, PAY/NOTIF) | No bounded app consumer found for invitation/entity/relation or admin-account-management operations; member app stores refresh token but has no automatic refresh/retry; no member notification page; sample courses/posts are not backend program/enrollment or group-content integration. `/auth/me` is consumed by the admin client. | Admin and Ionic pages/services, guards and models; Phase 05 reverse-coverage review. |

## Unresolved policy or product decisions (not implementation prescriptions)

The following are carried forward without choosing a desired behavior:

- AUTH-005: whether requirement wording should reflect current logout behavior, or a separately authorized implementation change should enforce stored-row existence.
- AUTH-002/AUTH-004: whether inactive/suspended profiles may authenticate and whether refresh rotation must be strictly single-use under concurrency.
- Role policy: `admin_readonly` scope; per-operation member/family actors; notification profile access and dashboard/payment access.
- Invitation: eligible inviter identity model, target-contact matching and family-join behavior.
- Family and entity relations: FAM-006/ENT-002 duplicate coverage/ownership.
- Academic lifecycle: canceled-enrollment reuse under unique keys; intended client contract, fields/statuses and attendance/session flow.
- Group visibility/status for public inactive/archived groups and whether/how content routes should be published in OpenAPI.
- Payment `refunded` value, notification zero-recipient behavior, notification-count meaning and any external provider/delivery expectation.
- Report-page intent/calculation/auth scope; no behavior is asserted from page labels.
- Admin-account API behavior and `/auth/me` response are now recorded as source-observed AUTH-006/AUTH-007 capabilities; admin-account UI/external use remains unconfirmed. Sample-backed member content remains an exception pending approved product scope.

These are not accepted contracts. Proposals in the Phase 05 audit remain provisional and do not authorize requirements edits or source changes.

## Test and verification limits

The relevant PHP tests were inspected as source only: `phase1-smoke.php`, `phase5-payments.php`, `phase6-community-notifications.php`, `phase7-parity.php`, `phase8-auth-account-types.php`, `phase9-family-read.php`, and `phase9-groups-roles.php`. Existing source assertions do not cover every requirement or establish UI interoperability. **No tests were run.** No builds, migrations, package commands, formatters or runtime calls were run. No secrets or personal data were inspected.

See each feature's `implementation-map.md` and `test-cases.md` for ID-level tracing and proposed tests; proposed tests are not executed.
