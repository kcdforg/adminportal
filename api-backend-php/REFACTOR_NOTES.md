# Procedural API Refactor

This version keeps the API framework-free and procedural by design.

## Endpoint rules

- One HTTP endpoint remains one PHP file.
- Endpoints receive route parameters through `$routeParams`.
- Authenticated endpoints call `checkAuth()` directly.
- `$database = $bootstrap['database'];` is used directly.
- Business flow stays in the endpoint: authorize -> read input -> validate -> database/business rules -> log -> response.
- Shared functions are limited to reusable infrastructure and genuinely shared operations.
- Function names use camelCase.
- No controllers, repositories, dependency injection, or application service classes were introduced.

## Removed complexity

- Removed `kcdf_identity_context()` from endpoint startup.
- Removed `kcdf_identity_run()` endpoint wrappers.
- Removed per-endpoint generic exception wrappers; the front controller now handles uncaught endpoint exceptions centrally.
- Removed `$_SERVER['ROUTE_PARAMS']`; route parameters are passed directly as `$routeParams`.
- Removed the `ProfileRepository` application class and replaced it with procedural functions.
- Removed the `kcdf_*` function naming scheme.
- Consolidated common HTTP/auth/database helper functions under camelCase names.

## Validation performed

- All PHP files in `api`, `services`, `http`, `config`, and `repositories` pass `php -l`.
- Existing route parity test passes: 397 assertions across 77 routes.
- Database-backed smoke tests could not be executed in this environment because the PHP SQLite PDO driver is not installed.
