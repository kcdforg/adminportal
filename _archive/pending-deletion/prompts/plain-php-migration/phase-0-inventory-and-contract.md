# Phase 0 — Migration Inventory and API Contract

## Objective

Prepare an evidence-based migration inventory before changing application
code. The source is `kcdf-api-backend/`; the destination is the existing
`api-backend-php/`. Do not modify source backend code, database schema, or
frontend code in this phase.

## Requirements

1. Inspect all Slim route files, controllers, services, validators, policies,
   middleware, database access, tests, configuration, and
   `database/schema.sql`.
2. Inspect `docs/02-api-conventions.md`, relevant module documentation, and
   API calls from both `kcdf-admin-app/` and `kcdf-parents-app/`.
3. Produce a route-to-endpoint inventory for every actually registered route:
   - HTTP method and current `/api/v1` path
   - exact FastRoute method/path registration
   - destination direct PHP endpoint file under `api/`
   - path/query/body inputs
   - authentication and role requirements
   - success/error status and response shape
   - validation and business rules
   - database tables, transactions, and audit side effects
   - frontend/client call sites
4. Explicitly identify discrepancies between backend routes, API docs, and
   client calls. Do not silently treat undocumented or unregistered features
   as implemented.
5. Preserve the existing `/api/v1` REST paths and methods. Define a
   deterministic route-to-file mapping for FastRoute, for example:
   `GET /api/v1/families/{id}` → `api/families/show.php`.
   Record how dynamic path parameters are passed to the endpoint. The endpoint
   target must be a fixed, trusted mapping—not a path derived from request
   input.
6. Confirm the single Apache rewrite in Phase 1 can forward `/api/v1/*` to
   `api/index.php` while preserving the request method, path, query string,
   and body. Record any server deployment constraints.
7. Keep the established success/error envelope and pagination response unless
   a contract difference is explicitly recorded for review.
8. Report scope, dependencies between modules, security-sensitive behavior,
   test gaps, and a phase-by-phase acceptance checklist.

## Deliverable

Create `api-backend-php/MIGRATION_INVENTORY.md`. It is migration documentation,
not a new architectural layer. Include a complete endpoint matrix; do not
replace it with only endpoint totals.

## Exit checks

- All registered routes are represented exactly once in the inventory.
- Every route maps to an exact FastRoute registration and a named direct PHP
  endpoint destination.
- Existing URL paths and HTTP methods are preserved unless a discrepancy is
  explicitly identified and approved.
- Client call sites and known API-contract conflicts are recorded.
- No application implementation, schema, source backend, or frontend files
  were changed.
