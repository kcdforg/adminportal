# Slim Backend — Full Code Review Findings

**Scope:** `kcdf-api-backend` (Slim Framework 4)  
**Date:** 2026-07-11  
**Method:** Static analysis only (app not run; `composer.lock` deleted / `vendor` may be incomplete)  
**Application files modified during review:** None  

**Fix status (2026-07-11):** Implemented F01–F14, F16–F20 in code. **F15** (cross-module `DB::table` / repo debt) deferred as L effort. Existing DBs must run `database/patches/2026-07-11-family-members-status.sql`.

Interactive summary canvas (IDE): open beside chat if available.

---

## 1. Executive summary

**Overall health: Critical**

### Top 5 production blockers

1. **JWT middleware broken** — no `SignedWith`/`StrictValidAt`, and `claims()->all()` values have `->getValue()` called on them (Lcobucci 5 already unwraps) → exceptions → `401` on essentially all protected routes after a successful login.
2. **Forced debug** in `public/index.php` (`display_errors`, `APP_DEBUG=true`) leaks stack traces regardless of `.env`.
3. **Public `GET /api/v1/auth/test-config`** exposes JWT secret length and the `Authorization` header.
4. **Invitation accept path** uses `Firebase\JWT` (not in `composer.json`) and `array $config` without a DI factory → accept-invite is unresolvable / broken.
5. **`admin_readonly` can mutate** families/members/entities because `RequireAdminMiddleware` gates writes that have no service-level policy.

Problems are mostly **Slim DIY glue** (JWT, DI, bootstrap, debug leftovers). Domain policies for Family/Batch/Payment ownership look comparatively solid once JWT claims actually reach services.

---

## 2. Findings table

| ID | Severity | Area | File(s) | Problem | Recommended fix | Effort |
|----|----------|------|---------|---------|-----------------|--------|
| F01 | P0 | Auth/JWT | `JwtAuthMiddleware.php` | `validate($token)` with zero constraints — signature/expiry never checked | Add `SignedWith` + `StrictValidAt`; reject `type=refresh` on API routes | S |
| F02 | P0 | Auth/JWT | `JwtAuthMiddleware.php:64-68` | `$claim->getValue()` invalid on Lcobucci 5 unwrapped values → 401 storm | Assign claim values directly; normalize `roles`/`family_ids` | S |
| F03 | P0 | Ops/Debug | `public/index.php:5-8,29-31` | Forced `display_errors` + `APP_DEBUG=true` | Remove TEMPORARY blocks; honor `.env` | S |
| F04 | P0 | Security | `Auth/routes.php:16-24` | Public `/auth/test-config` | Delete endpoint | S |
| F05 | P0 | Auth/DI | `InvitationService.php`, `composer.json`, `container.php` | Firebase JWT missing; `array $config` not wired | Reuse AuthService/Lcobucci; register DI factory | M |
| F06 | P1 | Auth | `JwtAuthMiddleware.php` | Writes to `/tmp/jwt_debug.log` | Remove; use Monolog if needed | S |
| F07 | P1 | Authz | `RequireAdminMiddleware` + Family/Member/Entity services | `admin_readonly` can create/update | Narrow middleware or add policies | S |
| F08 | P1 | Auth | `AuthService::refresh` | No `type=refresh` check; revoke+reissue not transactional | Assert type; `DB::transaction` | S |
| F09 | P1 | CORS | `CorsMiddleware.php:34-42` | Any `*.kcdfindia.com` + credentials | Explicit whitelist only | S |
| F10 | P1 | Soft delete | `FamilyService`, Entity relations | Hard `delete()` on join rows | Soft-status updates | M |
| F11 | P2 | Architecture | Core vs Notifications `ActivityLogService` | Naming collision (writer vs reader) | Rename reader | S |
| F12 | P2 | IDOR/info | `EntityService::list/show` | Any JWT user sees full entity catalog | Restrict/scope | M |
| F13 | P2 | Auth | AuthService + InvitationService | Dual JWT issuers | Single issuance path | M |
| F14 | P2 | Middleware | `container.php` RoleMiddleware | Bare resolve → empty roles → always 403 | Prefer typed admin middleware | S |
| F15 | P2 | Conventions | Multiple services | `DB::table` in services; cross-module repos | Repos + service boundaries | L |
| F16 | P2 | Ops | `composer.lock` | Lockfile deleted | Regenerate and commit | S |
| F17 | P3 | Payments | Routes vs `PaymentPolicy` | `RequireAdmin` wider than accounts/super | Align middleware | S |
| F18 | P3 | Quality | `tests/` | No tests despite phpunit | Minimal smoke suite | M |
| F19 | P3 | Installer | `Installer.php` | Admin bootstrap not one transaction | Wrap creates | S |
| F20 | P4 | Bootstrap | `bootstrap/app.php` | Misleading CORS order comment | Fix comment | S |

**Severity:** `P0` Critical / `P1` High / `P2` Medium / `P3` Low / `P4` Nit  
**Effort:** `S` (&lt;2h) / `M` (half day) / `L` (1–2 days) / `XL` (multi-day)

---

## 3. Detailed findings (P0–P2)

### F01 / F02 — JWT middleware unsafe and currently broken

**Evidence** (`kcdf-api-backend/src/Middleware/JwtAuthMiddleware.php`):

```php
$config = Configuration::forSymmetricSigner(
    new Sha256(),
    InMemory::plainText($secret)
);

$parsedToken = $config->parser()->parse($token);
// ...
if (!$config->validator()->validate($parsedToken)) {
    // ...
}
foreach ($claims->all() as $name => $claim) {
    if (!isset($payload[$name])) {
        $payload[$name] = $claim->getValue();
    }
}
```

**Impact:** Login can succeed; `/auth/me`, Families, Batches, etc. return `UNAUTHENTICATED`. Same empty-constraint pattern exists in `AuthService::refresh` (hash store still helps refresh somewhat). Fixing only F02 without F01 would accept forged tokens.

**Fix steps:**

1. Validate with `SignedWith` + `StrictValidAt` (clock skew via leeway).
2. Reject refresh tokens (`type=refresh`) on API routes.
3. Set `$payload[$name] = $claim` (already unwrapped in Lcobucci 5).
4. Ensure `roles` / `family_ids` are arrays.

**Regression check:** Login → `GET /auth/me` 200 → forged token 401 → expired token 401 → families list with valid token.

---

### F03 — Production debug forced on

**Evidence** (`kcdf-api-backend/public/index.php`):

```php
ini_set('display_errors', '1');
// ...
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
```

**Impact:** `bootstrap/app.php` enables ErrorMiddleware display from `APP_DEBUG` → stack traces / paths leak.

**Fix:** Delete both TEMPORARY blocks; rely on `.env`.

**Regression check:** With `APP_DEBUG=false`, force a 500 and confirm no stack trace in response body.

---

### F04 — Debug endpoint public

**Evidence** (`kcdf-api-backend/src/Modules/Auth/routes.php`):

```php
$auth->get('/test-config', function ($request, $response) {
    $data = [
        'jwt_secret_set' => !empty($_ENV['JWT_SECRET']),
        'jwt_secret_length' => strlen($_ENV['JWT_SECRET'] ?? ''),
        'auth_header' => $request->getHeaderLine('Authorization'),
    ];
```

**Impact:** Recon + possible token exfiltration via logs/proxies.

**Fix:** Remove the route entirely.

**Regression check:** `GET /api/v1/auth/test-config` → 404.

---

### F05 / F13 — Invitation accept auth path broken / divergent

**Evidence:**

- `InvitationService` uses `Firebase\JWT\JWT` but `composer.json` only requires `lcobucci/jwt`.
- Constructor requires `array $config`; only `AuthService`, `JwtAuthMiddleware`, `CorsMiddleware` are registered with `'config'` in `container.php`.
- Parallel HS256 issuance with a different library than login.

**Impact:** Invite accept fails at DI resolve and/or missing class; tokens diverge from login path.

**Fix:** Delegate token issuance to `AuthService` (or shared helper); add explicit DI factory; do not add Firebase unless intentional.

**Regression check:** Accept invite → usable access token → `GET /auth/me` 200.

---

### F06 — Debug log to world-readable path

**Evidence:** Multiple `file_put_contents('/tmp/jwt_debug.log', ...)` in `JwtAuthMiddleware` (lines 28–73).

**Impact:** Sensitive auth diagnostics on a world-readable path.

**Fix:** Remove; use Monolog at debug level if needed.

---

### F07 — `admin_readonly` write privilege

**Evidence:** `RequireAdminMiddleware` allows `admin_readonly`. Used on `POST /members`, `POST /families`, `PUT /entities/{id}`, etc. `FamilyService::create`, `MemberService::create`, `EntityService::update` have **no policy** — middleware alone grants writes.

**Impact:** Readonly admins can mutate core records.

**Fix:** Use elevated middleware for mutations, or policies that exclude `admin_readonly`.

**Regression check:** `admin_readonly` cannot `POST /families` or `POST /members`.

---

### F08 — Refresh rotation gaps

**Evidence** (`AuthService::refresh`): Revoke then reissue without transaction; no assert that JWT `type === 'refresh'`. AuthService also validates refresh JWT without constraints (mitigated by hash lookup).

**Impact:** Race on concurrent refresh; weaker type gating.

**Fix:** Assert `type`; wrap revoke + insert in `DB::transaction`; add `SignedWith`/`StrictValidAt` on refresh parse too.

**Regression check:** Refresh → new pair; old refresh rejected; concurrent double-refresh fails closed.

---

### F09 — CORS credentials + broad subdomain allowlist

**Evidence** (`CorsMiddleware.php`): Any `*.kcdfindia.com` origin gets `Access-Control-Allow-Credentials: true`.

**Impact:** Compromised/future subdomain can call API with credentials from a browser.

**Fix:** Prefer explicit `CORS_ALLOWED_ORIGINS` only.

---

### F10 — Hard deletes vs convention

**Evidence:** `FamilyService::removeMember` → `$membership->delete()`; entity relations hard-deleted. Groups/enrollments correctly use status soft-updates.

**Impact:** Violates soft-delete via status ENUM convention; harder audit/recovery.

**Fix:** Status-based soft remove for join rows.

---

### F11 — ActivityLogService naming

**Evidence:** `App\Core\ActivityLogService` writes; Notifications module class reads.

**Impact:** Naming collision / DI confusion (not duplicate writers).

**Fix:** Rename reader to `ActivityLogQueryService`.

---

### F12 — Entity catalog oversharing

**Evidence:** `GET /entities`, `GET /entities/{id}`: JWT only; no policy — any parent/trainer sees full directory.

**Impact:** Information disclosure beyond need-to-know.

**Fix:** Restrict catalog or scope by role.

---

### F14 — RoleMiddleware empty roles factory

**Evidence:** `container.php` registers `RoleMiddleware` with `[]` allowed roles.

**Impact:** Resolving bare `RoleMiddleware::class` always 403 (fail-closed, not open). Real risk is over-broad Admin middleware (F07).

**Fix:** Prefer typed admin middleware; never resolve bare `RoleMiddleware::class` on routes.

---

### F15 — Convention debt

**Evidence:** `DB::table` in Auth/Invitation/Notification services; cross-module repos (e.g. BatchPolicy → TrainerRepo).

**Impact:** Harder to reason about boundaries; drifts from backend conventions.

**Fix:** Move DB access to repositories; cross-module via services. Effort L — schedule after P0/P1.

---

### F16 — Deleted composer.lock

**Evidence:** Git status shows `composer.lock` deleted.

**Impact:** Non-reproducible deploys / CI drift.

**Fix:** Regenerate and commit lockfile.

---

## 4. Known hotspots (prompt §I)

| Hotspot | Status |
|---------|--------|
| PHP-DI `JwtAuthMiddleware` `InvalidDefinition` | **Fixed** in current `container.php` (factory present) |
| Login OK, Families/Batches `UNAUTHENTICATED` | **Still broken** — F02 |
| Forced debug in `index.php` | **Still broken** — F03 |
| JWT claim issuer vs middleware | **Still broken** — F01/F02 |
| `RoleMiddleware` empty roles factory | Fail-closed — F14 |

---

## 5. Architecture / lagging patterns

| Pattern | Recommendation |
|---------|----------------|
| Hand-rolled Capsule + PHP-DI factories for every `array $config` consumer | **Fix in Slim** |
| No migrations (`schema.sql` only); deleted lockfile | **Fix in Slim** |
| Dual JWT libraries / duplicated issuance | **Fix in Slim** (single helper) |
| Cross-module repository imports; custom per-module validators | Debt; defer large cleanup |
| Almost no automated tests | **Fix in Slim** with a small smoke suite |
| Laravel would reduce JWT/authz glue | **Not required** if Slim JWT + DI are fixed |

---

## 6. Suggested fix plan (Slim stay, ~1 week)

| Day | Focus |
|-----|--------|
| **1** | F01–F04, F06 — JWT middleware, remove debug/test-config; smoke login→me→families |
| **2** | F05, F13, F16 — Invitation → AuthService/Lcobucci + DI; regenerate `composer.lock` |
| **3** | F07, F09, F17 — readonly write gates; CORS whitelist; payment middleware align |
| **4** | F08, F10 — refresh transaction + type; soft-delete join rows |
| **5** | F11, F12, F18 — rename ActivityLog reader; entity scoping; PHPUnit smoke |

---

## 7. Smoke test checklist

1. Login → tokens + `profile.roles`
2. `GET /api/v1/auth/me` with access token
3. Refresh → new pair; old refresh rejected
4. Logout → refresh rejected
5. Own family `200`; other family `403`
6. Batches scoped; unauthorized batch `403`
7. Forged / expired JWT → `401`
8. `GET /auth/test-config` → `404` after removal
9. `APP_DEBUG=false` → no stack trace on 500
10. Invite accept creates user + usable access token
11. `admin_readonly` cannot `POST /families`

---

## 8. Out of scope / deferred

- Full `schema.sql` ↔ model field audit
- Swagger accuracy vs all routes
- Rate limiting / login brute-force
- N+1 across list endpoints
- Product question: should parents see children’s batches if not enrolled
- Runtime verification (vendor/lock incomplete at review time)

---

## Verdict

Do not treat this API as production-ready until **F01–F05** are fixed. Domain authorization patterns are better than the Slim auth glue currently wrapping them.

Source prompt: `prompts/slim-backend-full-review.md`
