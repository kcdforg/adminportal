# Deployment and Cutover

This runtime is a candidate replacement for the Slim API, not a production
readiness declaration. Keep the Slim deployment available as the rollback
target until the plain-PHP runtime has passed staging acceptance.

## Runtime requirements

- Apache 2.4 or compatible with `mod_rewrite` enabled and `AllowOverride
  FileInfo Options` for this directory. The runtime depends on the `END`
  rewrite flag and the `.htaccess` file.
- PHP 8.1 or later with PDO and `pdo_mysql`. Enable the `json`, `hash`, and
  `openssl` extensions; use a maintained PHP build with its standard secure
  random-number implementation.
- MySQL 8 with a setup account allowed to create a database and tables. The
  web installer creates a fresh database and imports the bundled schema; it
  refuses to use an existing database.
- The web process must be able to create `storage/`, write the backend-root
  `.env`, and append logs under `storage/logs/`. Grant write access only to
  the web-service account.

## Document root and rewrite rules

Prefer setting Apache's `DocumentRoot` to the `api-backend-php/` directory
itself. Do not expose the repository root or the parent directory. If the
document root must remain the parent directory, the backend can instead be
served from its folder path (for example `/api-backend-php/`); keep `.htaccess`
in the backend directory so its rules apply there. The `.htaccess`:

- denies access to dotfiles and the private `config/`, `database/`, `http/`,
  `install/`, `libraries/`, `repositories/`, `services/`, `storage/`, and
  `tests/` paths;
- denies requests made directly to PHP files below `api/`, while permitting an
  original `/api/v1/...` request to be internally rewritten to `api/index.php`;
- sends every `/api/v1` request through that single front controller and
  preserves its query string; forwards the `Authorization` header for PHP
  authentication after internal rewrites.

The app is not served by Apache as a root/site fallback: requests outside
`/api/v1` are not API routes. FastRoute treats a trailing slash as a distinct
path, so a registered endpoint with an added trailing slash returns 404.
Unknown `/api/v1` paths return the JSON 404 envelope; an unsupported method on
a known path returns JSON 405 and an `Allow` header.

## API reference

Interactive Swagger UI is available at `/swagger/` if the backend is the
document root (for example `https://api.xyz.com/swagger/`), or under the
backend folder path if it is a subdirectory (for example
`https://api.xyz.com/api-backend-php/swagger/`). Its OpenAPI 3.0 document is
served alongside it. Swagger UI uses a relative server URL so API requests
follow the same installation path, ending in `/api/v1` (for example
`https://api.xyz.com/api-backend-php/api/v1/auth/login`). The front controller
removes the backend's mount path before matching routes, so the same endpoints
work whether the backend is at the document root or inside a subfolder. The
UI's CSS and
JavaScript are loaded from jsDelivr, so browsers need access to that CDN to
render the interactive page. Protected operations use HTTP bearer
authentication with the JWT access token returned by login. Login and
invitation lookup/acceptance do not require that access token; refresh and
logout use the authentication requirements shown in the specification.

Example virtual-host directory configuration (adjust paths and TLS to the
target host):

```apache
<Directory "/srv/www/api-backend-php">
    Require all granted
    AllowOverride FileInfo Options
</Directory>
```

`apachectl -t` checks the main Apache configuration only; it does not prove
that a deployed virtual host honors this `.htaccess`. Verify the deployed
document root and rewrite/access behavior with the staging checks below.

## Environment

Set variables in the service environment where possible. The optional local
`.env` file accepts `KEY=value` entries and JSON-quoted string values; do not
commit it or put secrets in source control. The `.htaccess` denies direct
dotfile requests, but environment injection is preferred.

| Variable | Purpose | Production guidance |
| --- | --- | --- |
| `APP_NAME` | Runtime name | Optional |
| `APP_ENV` | Environment label | Set to `production` |
| `APP_DEBUG` | Enables Medoo query logging | Set to `false` |
| `DB_TYPE` | PDO driver | Use `mysql` |
| `DB_HOST`, `DB_PORT` | Database host and port | Set to the private database endpoint |
| `DB_DATABASE` | Existing database name | Use the approved production database |
| `DB_USERNAME`, `DB_PASSWORD` | Database credentials | Use a least-privilege service account |
| `JWT_SECRET` | HMAC signing secret | Supply a unique secret of at least 32 characters; never reuse a development value |
| `JWT_ALGORITHM` | HMAC algorithm | Use `HS256`, `HS384`, or `HS512` consistently with existing tokens |
| `JWT_ACCESS_TTL` | Access-token lifetime in seconds | Coordinate with consumers |
| `JWT_REFRESH_TTL` | Refresh-token lifetime in seconds | Coordinate with consumers |
| `CORS_ALLOWED_ORIGINS` | Comma-separated browser origins | List only the exact deployed admin and parent origins |
| `LOG_LEVEL` | Application log threshold | Use an operationally appropriate level, normally `warning` or `error` |

The default JWT secret is empty and startup validation fails until a strong
secret is configured. The current code defaults `APP_DEBUG` to `true` when it
is unset; production configuration must explicitly set `APP_DEBUG=false`.

## First-time web installation

Use the browser installer only for a new deployment with an unused database
name. It creates the database and schema, creates the first super-admin
profile/login, generates a random JWT secret, writes production settings to
`.env`, and creates `storage/installed.lock`. During setup it asks for both
the initial super-admin credentials and a regular user's credentials. The
regular user is created as a `member_profiles` record plus a `user_logins`
record directly by the installer; the Members API only creates profiles and
does not create login credentials. This member has no admin role or family
membership; assign family access separately after installation.

1. Deploy the complete backend, including `database/schema.sql`, and configure
   Apache's document root to this backend directory.
2. The root `.htaccess` blocks public access to `install/` by default. For
   first-time setup only, temporarily remove `install` from its private-path
   deny rule, run the installer over HTTPS from an operator-only network, then
   restore the deny rule immediately. Remote installation requires HTTPS;
   HTTP is accepted only from the local machine. The installer lock also
   blocks repeat setup, but is not a substitute for denying access to
   `install/`.
3. Enter a MySQL account allowed to create a database, exact frontend origins,
   and the initial super-admin credentials.
4. After setup completes, verify `/api/v1/auth/login` and confirm that
   `/install/` and all private directories return HTTP 403 before exposing
   the API publicly. The lock file blocks repeat setup; the installer will not
   overwrite `.env` or use an existing database.

The bundled install schema contains no `DROP TABLE` statements. Do not use
the installer to migrate an existing Slim database or any database containing
production data. For an existing installation, perform a separately reviewed
schema/data migration and configure the backend environment manually.

## Creating a login for an existing member

Member creation (`POST /api/v1/members`) creates a profile only. To provision
login credentials later, an authenticated admin (super admin, program
manager, or accounts admin; not read-only) can call:

```http
POST /api/v1/members/{profile_id}/login
Authorization: Bearer <admin-access-token>
Content-Type: application/json
```

```json
{
  "username": "member.login",
  "password": "<at-least-12-characters>"
}
```

The profile must exist and must not already have a login. Usernames must be
unique. Success returns HTTP 201 and safe login metadata only; the password
hash is never included. A duplicate login or username returns HTTP 409.

## Staging acceptance

Use a disposable database populated from the unchanged schema and test-only
data. Never use production credentials or records for these checks.

1. Run the PHP checks from the runtime root:

   ```sh
   php tests/phase1-smoke.php
   php tests/phase5-payments.php
   php tests/phase6-community-notifications.php
   php tests/phase7-parity.php
   ```

   The feature checks require PHP with PDO SQLite. Run the broader feature
   suites against a disposable MySQL database as well before cutover.
2. Configure a staging virtual host with `DocumentRoot` set to this directory,
   a production-like PHP handler, required extensions, environment variables,
   and the restricted log-directory permissions. Verify that the log is
   created and writable without making it publicly accessible.
3. Through the actual Apache virtual host (not only PHP's built-in server),
   check a REST path with a path parameter and query string, JSON request
   bodies, preflight `OPTIONS`, a trailing slash, an unknown route, and an
   unsupported method. Confirm JSON content types, status codes, response
   envelopes, parameter values, and query behavior.
4. Confirm direct requests to `/api/groups/index.php`,
   `/api/index.php`, `/config/config.php`, `/libraries/`, `/services/`, and
   `/storage/` are denied, and that unknown `/api/v1/...` routes still reach
   the JSON 404 response.
5. Exercise missing configured handler behavior in an isolated test copy (do
   not rename or remove a deployed endpoint); it must return the JSON
   `ROUTE_HANDLER_NOT_FOUND` 500 envelope.
6. Build and exercise the admin app against staging. Verify its login,
   refresh, logout, `/auth/me`, and representative list/mutation calls against
   unchanged `/api/v1` URLs. The parent app currently has the same API base URL
   in its environments but no active API service callers were found in the
   checked source tree; verify any consumer functionality introduced before
   its cutover.
7. Compare representative success and error responses with the Slim staging
   service, including role restrictions, pagination/filtering, and mutation
   effects. Require business-owner acceptance before changing traffic.

## Cutover and rollback

1. Deploy the PHP runtime to staging first and complete every acceptance item.
2. Back up the deployment configuration and confirm the Slim service remains
   deployable and its database compatibility is unchanged.
3. Switch staging traffic only after client, API, database, logging, and
   authorization checks are accepted. For production, perform a separately
   approved traffic switch with monitoring and an operator present.
4. Monitor HTTP 4xx/5xx rates, Apache/PHP errors, database errors, and
   application logs. Do not log credentials, passwords, access tokens, or
   refresh tokens.
5. On a regression, restore the prior routing/reverse-proxy target to Slim;
   do not roll back by destructively restoring a database snapshot unless the
   database owner has approved that recovery. Keep schema/data compatibility
   intact so the old service can resume handling traffic.

## Known operational differences and cutover blockers

- This runtime uses a manually maintained FastRoute registry and direct PHP
  endpoint files instead of Slim's middleware/container lifecycle.
- Apache `.htaccess` behavior, PHP extensions, environment injection, log
  permissions, CORS, and the actual DB driver are deployment-specific and
  cannot be proven by the repository-only route test.
- The current development environment has no PHP CLI or PDO SQLite, and its
  Docker daemon is unavailable. Feature tests and HTTP-level checks therefore
  remain unexecuted here.
- The admin and parent app URLs still point at the existing `/api/v1` paths,
  but neither has been exercised against this runtime in a deployed
  environment. The parent app has no active API callers in the source tree
  inspected for this phase.
- Production cutover is blocked until staging acceptance, feature tests, and
  consumer integration are verified. Keep Slim available as the rollback
  target until those checks and business-owner acceptance are complete.
