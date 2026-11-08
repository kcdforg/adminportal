# Dashboard, reports and audit — technical specification

**Evidence status:** Static client/backend/schema/test-source trace only. No report calculations, runtime results or product requirements are inferred.

| ID | Current behavior and interfaces | Status / boundary |
|---|---|---|
| DASH-001 | Admin dashboard calls family, batch and payment list services with minimal page sizes and reads `meta.total`; notification display is a placeholder. Source pages: `kcdf-admin-app/src/app/features/dashboard/dashboard.component.ts`; services for families/batches/payments. Backend list routes/handlers exist, but payment listing requires accounts/super-admin and can conflict with other dashboard roles. | **Partial**; response availability not executed. |
| AUD-001 | Write handlers call `services/identity.php::logActivity`; `GET /api/v1/activity-logs` in `api/activity-logs/index.php` is super-admin-only, newest-first, paginated and filters by server fields. Schema `activity_logs`. Admin UI's date filter is named differently and actor filter is absent. No update/delete route. | **Partial** integration; backend path exists. |

## Unassigned report UI

Admin report pages call attendance, enrollment and payment report paths, but no matching handler or registered route was found in `api-backend-php/api/routes.php`; those paths are also absent from `api-backend-php/swagger/openapi.json`. The pages (`features/reports/*`) are **partial UI surfaces with missing backend implementation**, not evidence for calculations, authorization, exports or response shape. No new report requirement is created.

## Evidence sources

- Client: `kcdf-admin-app/src/app/features/{dashboard,reports,audit-logs}/`; `core/services/{family,batch,payment,activity-log}.service.ts`.
- Backend: `api-backend-php/api/{families/index.php,batches/index.php,payments/index.php,activity-logs/index.php}`; `services/identity.php::logActivity`; `api/routes.php`.
- Schema: `api-backend-php/database/schema.sql` (`activity_logs` plus data sources for dashboard).
- API route and OpenAPI details: `docs/architecture/api-contract-audit.md`.

## Unresolved matters

Notification-count meaning, dashboard failure handling, report calculations/authorization, audit UI filter design and dashboard payment access are unresolved. No report contract or policy is asserted. Phase 05 audit: `baseline-workflow/analysis/requirements-accuracy-audit.md`.
