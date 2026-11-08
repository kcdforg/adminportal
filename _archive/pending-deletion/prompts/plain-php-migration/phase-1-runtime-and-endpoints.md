# Phase 1 — Plain-PHP Runtime and Direct Endpoints

## Context

Phase 0 and `api-backend-php/MIGRATION_INVENTORY.md` are complete. Implement
the shared runtime in `api-backend-php/`. The Slim source is read-only.

## Constraints

- Plain PHP only. No Composer, Slim, another framework, controller/repository/
  model/DTO layers, or dependency-injection container. FastRoute v1.3.0 is
  allowed only for HTTP method/path matching and dispatch to endpoint files.
- Keep the existing target layout: `api/`, `services/`, `libraries/`,
  `config/`, and `storage/`. Do not add `public/`.
- Every API operation must resolve to a direct endpoint PHP file under `api/`.
- Do not edit the source API or database schema.
- Use installed libraries as-is; do not edit third-party source.

## Implement

1. Inspect the installed third-party code and licenses in `libraries/`.
   Implement a small explicit autoloader for the installed namespaces and
   required file-based bootstrap(s). Do not depend on `vendor/autoload.php`.
   Load FastRoute v1.3.0's `src/functions.php` explicitly and map its
   `FastRoute\` namespace to `libraries/fastroute-1.3.0/src/`. Confirm the
   selected versions' PHP requirements and required extensions. Record all
   manually installed library names, versions, source URLs, and licenses in a
   concise `api-backend-php/libraries/README.md`.
2. Complete `config/config.php` and `config/init.php`:
   - environment-based database, JWT, CORS, and logging configuration
   - fail clearly on missing/invalid required production settings
   - initialize one PDO connection and Medoo on that same PDO connection
   - configure JSON content type and small JSON input/output helpers
   - configure validation, logging, and common exception-to-JSON handling
   - do not put domain/business rules in initialization
3. Add `api/index.php` as the single HTTP front controller and a small explicit
   route registry under `api/`. Use FastRoute v1.3.0 to map every inventoried
   HTTP method and existing `/api/v1` path to a fixed endpoint PHP file.
   Do not derive include paths from the URL, route parameters, or other
   request-controlled data. Pass matched route parameters to the included
   endpoint through a clearly documented local variable or request context.
   Correctly handle FastRoute's not-found, method-not-allowed, and found
   results as JSON responses, including the `Allow` header where appropriate.
4. Add a root `.htaccess` for Apache with a single catch-all rewrite for
   `/api/v1/*` to `api/index.php`, preserving the original path, query string,
   method, and request body. Exempt the actual front-controller target from
   rewrite loops. Deny direct web access to `config/`, `libraries/`,
   `services/`, and `storage/`; deny hidden files and direct requests for
   endpoint PHP files. Do not expose source or logs. Return JSON 404 for
   unknown routes and disable directory listings.
5. Define and document boundary behavior: JSON-only request bodies, malformed
   JSON response, unsupported method response, content type, CORS allowlist,
   stable error envelope, and production-safe 500 response.
6. Implement a small health endpoint only if it is explicitly listed in the
   migration inventory; do not invent endpoints.
7. Add focused tests or executable smoke checks for autoloading, FastRoute
   method/path matching, parameter transfer, safe fixed endpoint resolution,
   404/405 behavior, rewrite forwarding, blocked internal directories,
   malformed JSON, and JSON response formatting.

## Exit checks

- FastRoute maps all inventoried existing paths and methods to fixed endpoint
  scripts without changing public URLs.
- Apache forwards API requests through one rewrite to `api/index.php`; the
  PHP router dispatches to the endpoint file.
- Internal directories and hidden files are not web-accessible.
- Medoo and PDO share a connection for transaction-sensitive operations.
- Errors are JSON and do not expose secrets, SQL, stack traces, or paths.
- No Composer files, framework, dynamic include paths, or unnecessary
  architecture were added.
