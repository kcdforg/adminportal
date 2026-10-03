# Migration Prompt — Slim API to Plain PHP

## Objective

Migrate the existing REST API in `kcdf-api-backend/` from Slim Framework to a
simple, framework-free PHP API in the existing, currently empty
`api-baciend-php/` directory.

This is an **API-only migration**. Do not build a PHP web application now.
`web/` is explicitly out of scope and may be added later. Do not create a
`public/` directory; API entry-point and routing code belong under `api/`.

Implement the migration, not just a plan or scaffold. Keep the old backend
available as a read-only reference and do not modify it.

## Mandatory constraints

- Use plain PHP; do not use Slim or another framework.
- Do not use Composer: do not run Composer, add Composer manifests or lock
  files, or rely on `vendor/autoload.php`.
- Keep application code and third-party code separate.
- Manually install only the requested third-party libraries into
  `libraries/`, including any required runtime dependencies:
  - FastRoute — routing
  - Medoo — ordinary database access
  - Respect Validation — input validation
  - firebase/php-jwt — JWT handling
  - Monolog — logging
- Pin and document the exact versions, sources, and licenses of downloaded
  libraries. Include required transitive runtime dependencies without
  introducing a package manager. Do not edit third-party library source.
- Use PDO directly when Medoo is unsuitable, especially for transactions or
  complex SQL. Medoo and PDO must share the same configured connection where
  transaction consistency matters.
- Do not create unnecessary architectural layers such as controllers,
  repositories, models, DTOs, factories, or containers. Add a small helper only
  when it is genuinely needed and improves clarity.
- Do not change either frontend, the existing Slim API, the database schema, or
  unrelated project files. Treat all existing worktree changes as pre-existing;
  preserve them.

## Target structure

Keep the project simple and place all API-specific HTTP code in `api/`:

```text
api-baciend-php/
├── api/
│   ├── index.php             # API front controller / entry point
│   └── ...                   # routes and HTTP-specific code
├── services/                 # reusable business logic; no HTTP dependencies
├── libraries/                # manually downloaded third-party code only
├── config/                   # application and database configuration
├── storage/                  # logs and other runtime-generated files
└── bootstrap.php             # configuration, autoloading, DB, logging, errors
```

Do not add `web/` or `public/`. Preserve the exact destination directory name
`api-baciend-php`.

The API entry point must support the existing `/api/v1` URL prefix and route
requests to the appropriate handlers. Configure or document the minimum
web-server rewrite and access-denial rules needed for this layout. In
particular, prevent direct web access to `config/`, `libraries/`, `services/`,
and `storage/`; only API requests should be served by the API entry point.
Do not expose secrets, logs, or PHP source as downloadable files.

## Architecture

Keep dependencies flowing in this direction:

```text
API -> Services -> Database
```

- `api/` handles HTTP method/path matching, request parsing, authentication
  integration, boundary validation, status codes, and response serialization.
- `services/` owns business rules and operations shared by API endpoints. It
  must not read HTTP globals, parse headers, emit responses, use sessions, or
  depend on JWT/library-specific objects.
- Services receive ordinary typed values and an authenticated identity/context
  as needed. Keep authorization rules that protect domain operations in the
  service layer; HTTP middleware alone must not be the only enforcement.
- Bootstrap initializes configuration, a simple application autoloader,
  database connections, libraries, logging, and common error handling. It
  must not contain business logic.
- Keep API response helpers and routing small and explicit. Do not build a
  custom framework or dependency-injection container.

API authentication uses JWT. PHP sessions and web authentication are out of
scope until a web application is requested.

## Source of truth and compatibility

Before implementation, inspect the complete existing backend, including:

- `kcdf-api-backend/routes/` and all module route files
- all controllers, services, middleware, validators, policies, and database
  access code
- `kcdf-api-backend/database/schema.sql`
- `kcdf-api-backend/.env.example` and configuration
- `docs/02-api-conventions.md` and relevant module documentation
- frontend API usage in `kcdf-admin-app/` and `kcdf-parents-app/` where needed
  to verify actual request/response expectations

Treat the existing API behavior and documented contract as the compatibility
baseline. Preserve:

- all currently implemented endpoints, HTTP methods, paths, and `/api/v1`
  prefix
- request parameter names and accepted content types
- success/error response envelope, status codes, pagination, and filtering
- authentication, token refresh/logout behavior, role checks, and access
  scoping
- the existing MySQL schema and data; do not drop, recreate, or silently alter
  database tables

Do not mistake documented-but-unimplemented endpoints for implemented
functionality. Inventory the actual routes and implementation, and report any
gaps clearly. Do not silently omit endpoints or return fake success responses.
Do not blindly reproduce security bugs from the old implementation: fix
security defects that are directly encountered during migration while
preserving intended behavior. Document any unavoidable behavior change.

## Implementation requirements

### Database and configuration

- Use the existing database schema and environment variable names where
  practical; provide a safe `.env.example` in the new project, never real
  credentials.
- Do not commit secrets. Fail clearly at startup when required configuration
  is missing or invalid.
- Use PDO exception mode, prepared statements, and explicit transactions where
  operations need atomicity.
- Keep SQL parameterized. Validate dynamic identifiers such as sort columns
  against explicit allowlists.
- Handle database connection failures visibly in logs while returning a
  generic, non-sensitive error to API clients.

### Routing, validation, and responses

- Use FastRoute for matching methods and paths; map not-found and
  method-not-allowed cases to appropriate JSON responses.
- Parse JSON request bodies safely and report malformed JSON as a client error.
- Use Respect Validation for boundary/input validation, while keeping domain
  invariants and business rules in services.
- Preserve the documented response envelope and existing endpoint-specific
  response shapes.
- Ensure errors never expose stack traces, SQL, credentials, tokens, or
  internal filesystem paths in production responses.

### Authentication and authorization

- Use firebase/php-jwt with explicit algorithm allowlisting and signature,
  expiry, and token-type validation.
- Validate access tokens on protected routes and reject refresh tokens as
  access tokens.
- Preserve the existing access/refresh token behavior and revocation model.
- Enforce role and resource-level access checks consistently; do not trust
  client-supplied identity or ownership fields.
- Keep JWT secrets outside source control. Do not log authorization headers,
  raw tokens, passwords, or sensitive personal data.
- Apply suitable CORS allowlisting from configuration; never reflect arbitrary
  origins when credentials are involved.

### Logging and errors

- Use Monolog for structured application/error logging.
- Keep logs under `storage/` and ensure the directory is not publicly
  downloadable.
- Log unexpected exceptions with context that excludes secrets and sensitive
  request data. Return a stable generic 500 response to clients.
- Do not use broad catches that turn failures into success-shaped responses.

## Validation and completion criteria

Do not declare the migration complete until:

1. The actual existing route inventory has been compared with the new
   implementation and every currently implemented route is accounted for.
2. Existing database tables and data remain untouched by the migration.
3. All PHP files pass `php -l` using the available PHP runtime.
4. Each route has an appropriate success/error path verified. Add lightweight
   tests or repeatable PHP CLI smoke checks without introducing Composer or a
   framework-based test dependency.
5. Authentication checks cover a valid token, invalid signature, expired
   token, wrong token type, missing token, and insufficient authorization.
6. Database-backed checks cover representative reads, writes, validation
   failures, and a transaction/rollback path when applicable. If the database
   is unavailable, state which checks could not be run; do not claim they
   passed.
7. The API can be served through its entry point with the required rewrite,
   and non-API project folders cannot be downloaded over HTTP.
8. Documentation in the new project explains setup, library versions/licenses,
   environment configuration, database requirements, local serving, URL
   rewriting/access protection, and the verification steps.

At the end, report:

- files and modules implemented
- endpoint coverage and any source gaps
- compatibility changes and security fixes
- exact commands/checks run and their results
- any remaining limitations or deployment steps

