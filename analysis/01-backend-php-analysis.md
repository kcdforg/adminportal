# Backend (api-backend-php) — Analysis & Improvement Report

Benchmarks used: OWASP ASVS 4.0 / API Security Top 10 (2023), PSR-1/4/12, PHP-FIG, Clean Architecture/SOLID, 12-Factor App, REST/OpenAPI 3, ISO/IEC 25010 (maintainability, reliability, security).

Scope observed: ~95 endpoint files in `api/`, 7 files in `services/`, `http/helpers.php`, `config/`, `database/schema.sql` + 6 migrations, 7 ad-hoc test scripts, Swagger `openapi.json`. Libraries are vendored manually in `libraries/` (no Composer).

> Analysis is based on a structural review and sampling of code (router, payments endpoint, helpers, config, schema), not a line-by-line audit. Items marked "verify" need confirming.

## 1. Summary scorecard

| Area | Rating (1–5) | Comment |
|---|---|---|
| Readability | 3.5 | Consistent endpoint flow (auth → validate → act → log → respond); endpoints are short. Raw SQL inside endpoints reduces clarity. |
| Architecture | 2.5 | Deliberately "procedural, no layers" (see `REFACTOR_NOTES.md`). Fine for small scale but business rules are scattered between endpoints and `services/*.php`. |
| Security | 3 | Good basics (JWT, prepared statements, CORS allow-list, secrets in `.env`, gitignored). Missing rate limiting, security headers, token-storage hardening checks. |
| Testability | 2 | 7 phase-named scripts, no runner, no CI, DB tests can't run in all envs. |
| Maintainability / Ops | 2.5 | No Composer, vendored libs (manual updates, no CVE scanning), no migration runner, `installed.lock` files in repo storage. |
| API design | 3.5 | Uniform success/error envelope, route table, OpenAPI exists. Need versioning, pagination contract, idempotency. |

## 2. Architecture findings

| # | Finding | Evidence | Recommendation | Priority |
|---|---|---|---|---|
| A1 | No layering: endpoints mix HTTP parsing, authorization, SQL, business rules and audit logging. | `api/payments/store.php` contains raw `SELECT/INSERT` with PDO + business logic + logging. `repositories/` dir is empty. | Introduce a thin layering *without* a framework: `Domain/Service` (rules) ← `Repository` (SQL) ← `Endpoint` (HTTP only). Can be done with plain classes and PSR-4 autoload; endpoints stay one-file-per-route. Revisit the "no classes" decision — it blocks unit testing. | High |
| A2 | Functions in global namespace (`checkAuth`, `getJsonBody`, …), 161 global functions across services/helpers; collision & discoverability risk. | `services/*.php`, `http/helpers.php` | Namespace + classes (`Kcdf\Http\Request`, `Response`, `Auth\Guard`). | Med |
| A3 | Endpoints rely on implicit globals (`$bootstrap`, `$routeParams`) and `require` of the handler file. | `api/index.php` | Replace with a `Request` object passed to a handler callable; makes testing without HTTP possible. | Med |
| A4 | Dependency management is manual (`libraries/` copies of FastRoute, Medoo, JWT, Monolog, Respect). | `libraries/README.md`, no `composer.json` | Adopt Composer (`composer.lock`, `composer audit`), PSR-4 autoloading, remove custom `config/autoload.php`. | High |
| A5 | Mixed DB access styles: Medoo *and* raw PDO in the same flow; sqlite branches in production code (`$database->type === 'sqlite'`). | payments store | Pick one data access approach; isolate DB-dialect differences in the repository; run tests against the same engine as production (MySQL) via docker. | Med |
| A6 | Authorization is checked ad hoc per endpoint (`isAccountsAdmin($jwt)` etc.); easy to forget. | each endpoint | Declarative route metadata (`['GET','/payments','payments/index.php', ['roles'=>['accounts_admin','super_admin']]]`) enforced centrally in the dispatcher (deny-by-default, ASVS 4.1). | High |
| A7 | Large "grab-bag" files (`api/groups/resources.php` 568 lines, `services/identity.php` 847 lines, `community-notifications.php` 529). | wc output | Split by use case / aggregate; target < 300 lines per file, cyclomatic complexity < 10. | Med |

## 3. Security findings (OWASP API Top 10 / ASVS)

| # | Finding | Recommendation | Priority |
|---|---|---|---|
| S1 | No rate limiting / lockout found on `api/auth/*` (API4:2023, ASVS 2.2). | Per-IP + per-account throttling (DB or APCu table), exponential delay, audit failed logins. | High |
| S2 | CORS sends `Allow-Credentials: true` together with a list of origins; OK, but ensure list never contains `*`/null and that prod `.env` is restricted. | Validate at boot; fail startup if `*` in prod. | Med |
| S3 | No security headers (`X-Content-Type-Options`, `Cache-Control: no-store` for auth responses, `Strict-Transport-Security` if served directly). | Add in the front controller. | Med |
| S4 | JWT: confirm algorithm pinned (HS256 from env), `kid`/rotation not supported, secret strength not validated, access TTL value to verify. Refresh tokens are rotated (good) — verify reuse detection revokes the whole family. | Enforce min secret length at boot; consider RS256/EdDSA; add refresh-token reuse detection. | High |
| S5 | `APP_DEBUG` flag exists; make sure stack traces never reach responses when true in shared envs. | Return `error_id` and log details only. | Med |
| S6 | `install/index.php` (670 lines) shipped inside the deploy tree; lock files in `storage/`. | Remove from production artifact or block at web server; one-time CLI installer instead. | High |
| S7 | Input validation uses Respect\Validation per endpoint; ensure *every* write endpoint validates and unknown fields are ignored (mass assignment, API6). | Central schema per endpoint (generated from OpenAPI). | Med |
| S8 | Pagination limits: `LIMIT` handling in list endpoints should clamp max page size (API4). | Central `paginate()` helper with max 100. | Med |
| S9 | Object-level authorization (BOLA, API1): member endpoints must verify the family/member belongs to the caller. | Add automated negative tests per member route (IDOR matrix). | High |
| S10 | Audit logs capture before/after values — confirm PII (phone, address) masking policy and retention. | Define retention + masking in requirements. | Low |
| S11 | Dependency CVEs cannot be scanned in vendored form (e.g. Monolog 2.10, Respect 2.2). | Composer + `composer audit` in CI. | High |

## 4. Functionality / API design

1. **Versioning**: routes are unversioned. Add `/v1` prefix (or header) before external mobile releases are in the field.
2. **Standard pagination/filter/sort contract** (`page`, `per_page`, `sort`, `q`) plus response `meta` — document once in OpenAPI and reuse.
3. **Idempotency keys** for payment creation (`Idempotency-Key`) to prevent double charge on mobile retries.
4. **Money handling**: `number_format((float) $amount …)` uses floats. Use integer minor units or `DECIMAL` + string arithmetic (bcmath).
5. **Soft-delete & status transitions**: formalise state machines (enrollment, payment, invitation) in one place with allowed transitions.
6. **Notifications**: community notifications service is large; move to queued/outbox pattern so request latency and failures are decoupled.
7. **Health endpoint**: split `liveness` vs `readiness` (DB check).
8. **OpenAPI**: `swagger/openapi.json` is hand-maintained — risk of drift. Generate or lint it in CI (Spectral) and run contract tests (Schemathesis/Dredd) against it; share generated TS types with both front-ends.
9. **Error codes**: one unauthorized-vs-forbidden convention (payments uses `UNAUTHORIZED` with HTTP 403; should be `FORBIDDEN`).
10. **Time**: `date('Y-m-d H:i:s')` uses server timezone; store UTC, set `date_default_timezone_set('UTC')`, send ISO-8601 with offset.

## 5. Database

- 40 enum/tinyint columns and ~73 key/index definitions in `schema.sql` — review for covering indexes on list filters (family_id, status, created_at).
- Migrations are dated SQL files with no runner or version table → adopt Phinx/Doctrine Migrations or a minimal `schema_migrations` table with a CLI.
- Add FK constraints/`ON DELETE` policy review and unique constraints for business keys (e.g. payment `transaction_reference` per method).
- Backups, retention, and character-set (`utf8mb4`) to be stated as technical requirements.

## 6. Quality, testing & delivery

| Gap | Recommendation |
|---|---|
| Tests are scripts named by "phase" (phase1…phase9) | Move to PHPUnit; name by feature; target ≥ 70% line coverage on services, 100% of routes covered by at least a happy + auth-failure test. |
| No static analysis | Add PHPStan (level 6→8), PHP-CS-Fixer/PHPCS (PSR-12), Rector for modernisation. |
| No CI | GitHub Actions: lint → phpstan → phpunit (MySQL service) → `composer audit` → OpenAPI lint. |
| Logging | Monolog present; add request-id correlation, JSON format, no PII/tokens in logs. |
| Config | `.env` is gitignored (good). Provide `.env.example`; validate required keys at boot. |
| `phase7-parity.php` asserts 397 assertions over 77 routes (route parity) — keep, convert to contract test. |

## 7. Readability

- Keep the short endpoint style but extract SQL into named functions/repositories (`getPayment`, `lockPaymentRow` already hint at this).
- Replace `!empty($x) ? $x : null` patterns with a `nullIfEmpty()` helper (note `!empty('0')` bug: value "0" treated as empty).
- Add `declare(strict_types=1)` (already present) and parameter/return types on all functions.
- Remove magic strings (roles, statuses) → enums (PHP 8.1 backed enums).
- Document conventions in a short `CONTRIBUTING.md`; `REFACTOR_NOTES.md` has stale-looking content that should become an ADR.

## 8. Suggested roadmap

1. **Quick wins (1–2 wk):** rate limiting on auth, security headers, remove/lock installer, `.env.example`, error-code fix, clamp pagination, enforce JWT secret strength.
2. **Foundation (2–4 wk):** Composer + PSR-4, PHPUnit + MySQL docker, PHPStan, CI, migration runner.
3. **Structure (4–8 wk):** Request/Response objects, central role-based route guard, repositories for payments/enrollments/groups first, enums, money as integer/decimal.
4. **Contracts (ongoing):** OpenAPI as source of truth, generated clients/types, contract tests, API versioning, idempotency keys.
