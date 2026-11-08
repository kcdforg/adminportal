# Slim Backend — Full Code Review (Issues, Gaps, Fixes)

## Role

You are a senior PHP backend reviewer auditing `kcdf-api-backend` (Slim Framework 4 REST API for the KCDF Parents platform).

Your job is to **review only** (do not implement fixes unless the user explicitly asks after the report). Produce a clear, prioritized findings report with concrete fix recommendations.

---

## Scope

Review the entire backend under:

`/Users/apple/development/php/kcdf-parents/kcdf-api-backend`

Include:

- `public/`, `bootstrap/`, `config/`, `routes/`, `src/`, `database/`, `scripts/`
- `composer.json`, `.env.example` (do **not** print secrets from `.env`)
- Installer (`src/Install/`, `public/install/`)
- Swagger pipeline (`scripts/generate-swagger.php`, `routes/documentation.php`)

Exclude:

- `vendor/`
- frontend apps (`kcdf-admin-app`, `kcdf-parents-app`) except where API contract mismatch is evident from backend code

Also read and judge against:

- `kcdf-api-backend/.cursor/rules/backend-conventions.mdc`
- Parent docs under `docs/` (module docs if present)
- Original build prompts under `prompts/phase-*.md` for intended design vs actual code

---

## How to work

1. Inventory structure (modules, middleware, routes, DI, schema).
2. Trace critical paths end-to-end: boot → middleware → route → controller → service → repository → model.
3. Prefer evidence: cite file paths and short code snippets / line ranges.
4. Distinguish **bugs** vs **debt** vs **design mismatches** vs **security**.
5. Do not rewrite the app in the report. Recommend the smallest correct fix per finding.
6. If something looks intentional, say so; do not invent issues.

---

## Review checklist (cover all)

### A. Bootstrap, DI, config

- [ ] `public/index.php` boot order (lock file, dotenv, container, Slim, routes)
- [ ] Temporary/debug leftovers (`display_errors`, forced `APP_DEBUG`, debug file writes)
- [ ] PHP-DI: every constructor dependency that cannot be autowired has an explicit factory in `config/container.php`
- [ ] Classes taking `array $config` (or similar) are fully registered
- [ ] Capsule/Eloquent boot happens once and before first DB use
- [ ] Env keys used in code exist in `.env.example` with safe defaults documented
- [ ] Helper collisions / dangerous helpers (e.g. `now()`)

### B. Auth & JWT (highest priority)

- [ ] Login issues access + refresh correctly; claims include intended fields (`profile_id`, `sub`, `username`, `roles`, `family_ids`, etc.)
- [ ] Refresh rotation / revocation (hashed store, reuse detection if any)
- [ ] Logout invalidates refresh
- [ ] `JwtAuthMiddleware`: missing/invalid token → consistent 401 envelope
- [ ] JWT secret missing/mismatch fails loudly in logs (not only silent 401)
- [ ] Same secret and algorithm used for issue and verify (`AuthService` vs middleware)
- [ ] Token expiry / clock skew handling
- [ ] Role middleware (`RequireAdmin*`, `RoleMiddleware`) vs Policy classes — overlaps, gaps, bypasses
- [ ] Public routes truly public; protected routes always behind JWT
- [ ] Invitation accept / register paths do not leak privilege

### C. Middleware & HTTP

- [ ] CORS origins, preflight OPTIONS, credentials headers
- [ ] Middleware order (body parsing, routing, CORS, error middleware)
- [ ] Error middleware: production vs debug leakage
- [ ] Consistent JSON Content-Type and response envelope via `BaseController`
- [ ] No raw `json_encode` / ad-hoc error shapes outside standards

### D. Architecture & conventions

Compare every module against backend conventions:

- Thin controllers (no business logic / no DB)
- Business logic only in Services
- DB only in Repositories (flag `DB::table` / `DB::raw` / cross-module repo imports)
- Policies called before mutations
- Activity logging on mutations
- No cross-module repository access
- Standard error codes (`VALIDATION_FAILED`, `UNAUTHENTICATED`, `UNAUTHORIZED`, `NOT_FOUND`, `DUPLICATE_ENTRY`, etc.)

### E. Module health (per module)

For **Auth, Families, Academics, Payments, Community, Notifications**:

- [ ] Routes complete vs docs/prompts; dead routes; missing auth/role gates
- [ ] Validators cover required fields and edge cases
- [ ] Policies match intended roles (parent / trainer / admin tiers)
- [ ] Soft-delete / status enum conventions respected (no hard deletes of core records)
- [ ] Pagination meta consistent
- [ ] N+1 queries or obvious performance issues
- [ ] Duplicated logic (e.g. two ActivityLog services)

### F. Database & schema

- [ ] Models match `database/schema.sql` (tables, FKs, casts, fillable/guarded)
- [ ] Missing indexes for common filters
- [ ] Dangerous mass-assignment surfaces
- [ ] Transactions around multi-step writes (enrollments, invitations, payments, attendance)

### G. Security

- [ ] Password hashing / verify
- [ ] Secrets not logged; no tokens in responses beyond auth endpoints
- [ ] Path traversal / install wizard locked after install
- [ ] SQL injection via raw queries
- [ ] IDOR: can user A access family/batch/payment of user B?
- [ ] Debug endpoints (`/auth/test-config` or similar) — remove or gate
- [ ] File writes to world-readable paths (e.g. `/tmp/jwt_debug.log`)

### H. Ops, install, docs, quality

- [ ] Installer safety and idempotency
- [ ] Swagger generation accuracy vs real routes
- [ ] Logging usable in production (Monolog path, levels)
- [ ] Absence of tests — recommend minimal smoke suite
- [ ] Composer platform PHP version vs actual runtime
- [ ] Dead code, TODOs, TEMPORARY comments, unused deps

### I. Known hotspots (verify explicitly)

These recently caused production pain — confirm fixed or still broken:

1. PHP-DI: `JwtAuthMiddleware` / any middleware needing `$config` not registered → `InvalidDefinition`
2. Login works but Families/Batches return `UNAUTHENTICATED` (token not sent, secret mismatch, claim parse failure, middleware not applied)
3. Forced debug / error display in `public/index.php`
4. JWT claim extraction differences between issuer and middleware
5. `RoleMiddleware` default empty roles factory vs per-route instantiation

---

## Output format (required)

Write the report as markdown with these sections:

### 1. Executive summary

- Overall health: Healthy / Needs work / Critical
- Top 5 issues blocking production reliability
- Whether problems are mostly Slim DIY glue vs domain logic

### 2. Findings table

For each finding:

| ID | Severity | Area | File(s) | Problem | Recommended fix | Effort |
|----|----------|------|---------|---------|-----------------|--------|

Severity: `P0` Critical / `P1` High / `P2` Medium / `P3` Low / `P4` Nit  
Effort: `S` (<2h) / `M` (half day) / `L` (1–2 days) / `XL` (multi-day)

### 3. Detailed findings

For each `P0`–`P2` item:

- Evidence (path + short snippet or behavior)
- Impact (who breaks, which endpoints)
- Exact fix steps (files to change, approach)
- Regression check (how to verify)

### 4. Architecture / lagging patterns

List outdated or fragile patterns (hand-rolled DI config, Capsule vs full Laravel, no migrations, no tests, custom validators vs a shared validation layer, duplicated JWT issue paths, etc.) and whether to **fix in Slim** or **defer to a Laravel migration**.

### 5. Suggested fix plan (Slim stay)

Ordered 1-week plan: day-by-day or P0→P3 sequence. No Laravel migration in this plan unless a finding truly cannot be fixed in Slim.

### 6. Smoke test checklist

Minimal manual/API checks after fixes (login, refresh, me, families list, batches list, one mutation, unauthorized case).

### 7. Out of scope / deferred

Items noticed but not fully audited.

---

## Rules

- Be specific; cite paths.
- Do not dump huge code blocks.
- Do not recommend a full Laravel rewrite inside this review unless asked; you may note “Laravel would eliminate X” briefly under lagging patterns.
- Do not modify application files during the review pass.
- If you cannot run the app, say so and base findings on static analysis + trace reasoning.
- Never print real JWT secrets, DB passwords, or `.env` values.

---

## Start command

Begin by listing `src/Modules/*`, `src/Middleware/*`, `config/*`, and grepping for:

- `TEMPORARY|FIXME|TODO|jwt_debug|display_errors|APP_DEBUG`
- `DB::raw|DB::table`
- `withAttribute\('jwt_payload'`
- `InvalidDefinition|get\('config'\)`
- classes with `array $config` in constructors

Then produce the full report in the format above.
