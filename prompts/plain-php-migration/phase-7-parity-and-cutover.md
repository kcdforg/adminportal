# Phase 7 — Parity, Consumer Integration, and Cutover

## Context

Phases 0–6 are complete. Perform end-to-end migration verification for
`api-backend-php/`. Do not modify `kcdf-api-backend/` or its database schema.

## Verify API and client integration

1. Compare the final direct endpoint inventory to
   `MIGRATION_INVENTORY.md`; account for every actually registered Slim route.
2. Verify the Angular admin and parent app callers continue to use their
   existing `/api/v1` URLs and methods. Update a consumer only when Phase 0
   identified a pre-existing discrepancy or an approved contract change; do
   not change UI behavior or unrelated frontend code.
3. Verify FastRoute mappings and Apache rewrite/access-denial behavior using
   the intended deployment document root. Test existing REST-style paths,
   path parameters, query strings, JSON bodies, trailing slashes, unknown
   paths, unsupported methods, blocked internal paths, direct endpoint-file
   requests, and missing endpoint files. Do not assume PHP's built-in server
   applies `.htaccess`.
4. Verify methods, auth/roles, input validation, JSON content types, status
   codes, response envelopes, pagination, filtering, and error handling.
5. Run feature tests against a disposable/test database populated from the
   existing unchanged schema. Do not run destructive schema setup against
   shared or production data.
6. Check for raw SQL injection vectors, unsafe dynamic identifiers, secrets in
   source, accidental log exposure, and unexpected files under `libraries/`.
7. Document deployment requirements, environment variables, the single
   front-controller rewrite/access rules, log-directory permissions, rollback
   procedure, known gaps, and operational differences from Slim.

## Completion criteria

- All registered endpoints are implemented or listed as a specific,
  explicitly approved exclusion.
- All relevant automated tests pass; report commands and results.
- Both API consumers work against the preserved URLs, or cutover is explicitly
  reported as blocked.
- No unexpected changes were made to the Slim backend or schema.
- Slim remains available as the rollback target until staging acceptance.
- Update the migration inventory with final status; do not claim production
  readiness without deployment and staging verification.
