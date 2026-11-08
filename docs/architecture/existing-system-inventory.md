# Existing-system inventory

**Phase:** 01 — Repository discovery  
**Evidence status:** Static repository/source inventory; provisional  
**Refresh:** Rechecked at inventory depth after approval of the revised Phase 01 plan; no runtime or test verification.  
**Scope limit:** High-level structure and route/page/service inventory only. Detailed feature behavior, runtime behavior and deployment state have not been verified.

## Repository boundaries and baseline

The repository contains three protected applications:

- `api-backend-php/` — PHP API backend.
- `kcdf-admin-app/` — Angular admin application.
- `kcdf-parents-app/` — Ionic Angular member application.

All three were inspected read-only. No files were written, moved or deleted inside these directories.

Phase 01 pre-discovery Git status was `MM .DS_Store` and `?? baseline-workflow/`. Those changes pre-dated discovery and were preserved. A fresh status and protected-path comparison are recorded in `baseline-workflow/phases/phase-01/execution-report.md`.

## High-level component inventory

| Component | Observed structure and entry points | Supporting surfaces | Limits |
|---|---|---|---|
| PHP API | `api-backend-php/api/index.php` loads configuration/bootstrap, normalizes the request path, dispatches through FastRoute and requires the matched handler. Route declarations are in `api-backend-php/api/routes.php`. | `config/`, `http/`, `services/`, `repositories/`, `libraries/`, `database/`, `tests/`, `swagger/`, `install/`; top-level deployment and migration notes. | No PHP command or tests were run. Configuration and secret-bearing environment file contents were not read. |
| Angular admin | `kcdf-admin-app/src/app/app.routes.ts` defines lazy-loaded routes under a main layout. Auth and role guard files are present. The package manifest declares Angular 21.2 dependencies. | `core/guards/`, `core/interceptors/`, `core/models/`, `core/services/`, `core/store/`; 14 feature directories under `src/app/features/`. The package manifest defines build and test scripts. | Manifest declarations do not establish the installed/runtime version. No `*.spec.ts` files were found under `src/`; no test command was run. |
| Ionic member app | `kcdf-parents-app/src/app/app.routes.ts` defines login and guarded tab/page routes. The package manifest declares Angular 20.3, Ionic 8 and Capacitor 8 dependencies. | `core/member-auth.service.ts`, `core/sample-content.ts`, page directories for courses, family, groups, home, login, profile/me, posts and tabs; environment and Capacitor/Ionic configuration files. Two `*.spec.ts` files were found under `src/`. | Page names and `sample-content.ts` do not establish backend integration or production behavior. Tests were not run. |

## Backend route and data surface

`api-backend-php/api/routes.php` contains 97 literal `addRoute` call sites. Several calls are inside loops that register group-content operations, so this is a source call-site count, not the number of runtime routes. The handler tree contains route groups for activity logs, admins, attendance, auth, batches, enrollments, entities, families, groups, invitations, members, notifications, payments, programs, sessions and trainers. Group posts, polls and events are registered under `/api/v1/groups/{group_id}/...` and handled by `api-backend-php/api/groups/resources.php`; there is no separate `api/posts/` handler directory in the inventoried API tree.

The backend request entry is `api-backend-php/api/index.php`. Shared runtime paths observed include `config/autoload.php`, `config/config.php`, `config/init.php`, `http/helpers.php` and service files for academics, auth, community/notifications, identity, logging and payments. `config/init.php` creates a PDO connection based on configured database type and wraps it in Medoo; endpoint handlers receive/use the bootstrapped database. Configuration values and environment contents were not inspected or copied. The full behavior of the helpers/services was not analyzed.

Data, contract and test locations found:

- Schema: `api-backend-php/database/schema.sql` (header describes a MySQL 8 schema).
- Migrations: `api-backend-php/database/migrations/` (five SQL files).
- OpenAPI/Swagger: `api-backend-php/swagger/openapi.json` and `api-backend-php/swagger/index.html`.
- Backend test files: `phase1-smoke.php`, `phase5-payments.php`, `phase6-community-notifications.php`, `phase7-parity.php`, `phase8-auth-account-types.php`, `phase9-family-read.php` and `phase9-groups-roles.php` under `api-backend-php/tests/`.

These are artifact locations only. Migration contents, schema semantics, test assertions and API-spec parity have not been examined in detail.

## Client route and service surfaces

The admin route tree includes login, dashboard, members, families, trainers, programs, batches, session attendance, enrollments, payments, groups, notifications, reports and audit logs. Role metadata is present on a subset of routes. The shared `kcdf-admin-app/src/app/core/services/api.service.ts` wraps Angular `HttpClient` using the configured API URL; feature services are under `src/app/core/services/`. Relevant route and guard evidence is in `kcdf-admin-app/src/app/app.routes.ts` and `src/app/core/guards/`.

The member route tree includes login, home, groups/group detail, courses/course detail, posts/post detail, profile and family pages. `kcdf-parents-app/src/app/core/member-auth.service.ts` contains the member auth/API client surface. Its source includes calls for profile/family/groups, group resources and programs/batches. The courses list/detail and posts list/detail pages import sample constants from `core/sample-content.ts`; whether other API methods are wired into these pages was not traced. Relevant route and page evidence is in `kcdf-parents-app/src/app/app.routes.ts` and `src/app/`.

The member group pages import `MemberApiService`; the member posts pages use `SAMPLE_POSTS`, while the backend group-content API routes use `groups/resources.php`. These distinct surfaces are candidates for Phase 02 tracing, not proof of an end-to-end posts flow.

The route names suggest candidate cross-application areas, but route-name similarity is not evidence that a particular client calls a particular endpoint. API wiring, permissions, validations and outcomes remain Phase 02 work.

## Documentation and operational material

At discovery depth, repository documentation and operational areas include:

- Archived: [`00-overview.md`](../../_archive/pending-deletion/docs/00-overview.md), [`01-database.md`](../../_archive/pending-deletion/docs/01-database.md), [`02-api-conventions.md`](../../_archive/pending-deletion/docs/02-api-conventions.md).
- `docs/adr/`, `docs/deployment/`, `docs/frontend/` and `docs/modules/`.
- `devdocs/`, root prompts, `rules/` and `scripts/`.
- Backend `DEPLOYMENT.md`, `MIGRATION_INVENTORY.md` and `REFACTOR_NOTES.md`.
- Backend `.htaccess`; frontend `README.md`, Angular configuration and environment file paths; member Ionic and Capacitor configuration paths.
- Archived [`project-conventions.mdc`](../../_archive/pending-deletion/rules/project-conventions.mdc); a targeted filename search found no root `AGENTS.md`, `.github/copilot-instructions.md` or `.cursor/rules/` files at the time of this inventory.

These materials were not systematically analyzed. Historical requirements, prompts, deployment claims and migration narratives were not reconciled against source and must not be treated as current behavior.

## Preliminary architecture map

```text
Admin browser app (Angular)
  routes/pages -> core services (including shared ApiService) and interceptors -> PHP API (feature-level relationships require Phase 02 tracing)

Member app (Ionic Angular / Capacitor)
  routes/pages -> member auth/API client and sample-content files
    -> PHP API for some exposed client methods; page-to-method wiring requires Phase 02 tracing

PHP API
  api/index.php -> config/bootstrap -> FastRoute registrations in api/routes.php
    -> endpoint handler files under api/<area>/ (group content via groups/resources.php)
    -> shared helpers/services and PDO/Medoo database bootstrap (exact feature flow requires Phase 02 tracing)
  OpenAPI document: swagger/openapi.json
```

The arrows show source-level structure, not verified runtime request flows. No deployed environment or runtime integration was observed.

## Evidence limits and follow-up

- Framework versions are package-manifest declarations only.
- No build, test, migration, package manager or runtime command was run.
- Files named `.env` were excluded from content inspection; no secrets or environment values were copied.
- The member app contains both sample-backed course/post pages and API client methods for related surfaces; this inventory does not establish which pages invoke which methods.
- `api-backend-php/storage/`, installed dependencies, build output and generated web output were not analyzed for application behavior.
- Feature-level implementation, partial/mock status, API correspondence and data ownership must be established in Phase 02 from source evidence.
- The candidate feature inventory and proposed Phase 02 batches are in `docs/development/feature-inventory.md`.
