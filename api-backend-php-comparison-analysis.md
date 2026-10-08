# Migrated vs. Refactored PHP API Comparison

**Compared**

- Migrated: `./api-backend-php/`
- Refactored: `./api-backend-php-chatgpt/`

## Summary

The refactored tree retains the migrated API's route surface and, based on a
static comparison, appears to retain its principal business logic. I found no
missing route or missing endpoint file. The removed `ProfileRepository` logic
has been moved into procedural functions in `services/identity.php`; the
tagged family-members endpoint retains its authorization, active-member query,
selected fields, and nested response shape.

The refactor is generally simpler to follow at the endpoint level: it removes
repeated runtime setup and response construction, uses direct helper calls, and
centralizes uncaught endpoint exceptions. It is not uniformly clearer,
however. There are response-shape/edge-case changes, generic global function
names, a no-op in the front controller, and inconsistent indentation in the
tagged endpoint.

**Overall assessment:** no substantial business-flow omission was identified
in this static review. Treat the refactor as *largely behavior-preserving, but
not byte-for-byte API compatible*, until the noted differences are accepted
and runtime tests are run.

## Comparison evidence

- Both route registries have **77 identical method/path/handler entries**.
- The two trees contain the same **79 PHP paths under `api/`**.
- The route inventory and `.htaccess` are identical.
- The removed `repositories/ProfileRepository.php`'s profile lookup and role
  aggregation are present as `getProfileById()` and `getProfileRoles()` in the
  new `services/identity.php`.
- The refactored [`api/families/members.php`](./api-backend-php-chatgpt/api/families/members.php)
  retains the family existence check, view permission check, active
  `family_members` filter, joined profile columns, and nested member/profile
  response fields from the migrated endpoint.
- Academic, community/notification, payment, identity, and auth helpers have
  been renamed/reorganized. The reviewed helper bodies and endpoint flows
  largely preserve the existing checks and database operations.

## Differences and caveats

### 1. Login validation response details differ

In the migrated login endpoint, the validation details include both
`username` and `password` keys, with an empty array for a field that passed
validation. The refactored endpoint only includes keys that failed validation.
For example, a missing username with a supplied password no longer returns
`"password": []`.

This does not change the login decision, but it is an observable JSON contract
change. Confirm clients do not rely on the old details shape, or preserve the
old shape if strict compatibility is required. See the migrated and
refactored [`login.php`](./api-backend-php-chatgpt/api/auth/login.php).

### 2. `/auth/me` has two minor edge-case changes

- The new name construction trims an existing `name` before deciding whether
  to fall back to first/last name. If a stored name consists only of
  whitespace, the refactored version falls back to first/last name; the old
  version returned an empty name.
- The refactored roles normalization casts any roles value to an array. The
  migrated version only used roles when the claim was already an array. A
  malformed or non-array signed claim can therefore produce a different
  result; tokens issued by the current login flow use an array.

These look like sensible normalization improvements, but they are still
behavior differences. See [`me.php`](./api-backend-php-chatgpt/api/auth/me.php).

### 3. SQLite fallback depends on the storage directory existing

The migrated tree contains `storage/.gitkeep`; the refactored tree does not
contain the `storage/` directory. `config/init.php` has a SQLite fallback path
under `storage/app.sqlite`. If an installation selects SQLite and reaches that
fallback without provisioning `storage/`, PDO cannot create the database
file. The deployment guide describes MySQL as the production database and
requires provisioning writable logging storage, so this is conditional rather
than a default MySQL blocker. Consider retaining a tracked directory marker or
explicitly creating the database directory before opening SQLite.

### 4. Exception handling is centralized, with a status-mapping change

The refactor removes per-endpoint catch-and-forward wrappers and catches
uncaught endpoint exceptions in [`api/index.php`](./api-backend-php-chatgpt/api/index.php),
using [`handleException()`](./api-backend-php-chatgpt/http/helpers.php). The
new central handler preserves the prior duplicate-key and internal-error
handling and additionally maps a `RuntimeException` with code `401` to an
unauthenticated response. The previous shared identity handler treated that
uncaught case as an internal error; direct auth catches already returned 401.
This is a reasonable improvement, not lost logic.

### 5. Front-controller no-op

The refactored front controller contains `$routeParams = $routeParams;`.
It has no effect: `$routeParams` is already assigned from the matched route
immediately above it. Removing the line would make the dispatch code clearer;
it does not appear to break route parameter delivery.

## Readability and simplicity

### Improvements

- Endpoint files no longer repeat bootstrap validation and ad hoc JSON
  envelopes.
- Route parameters and shared operations have straightforward names, and
  endpoint flow is generally easier to scan.
- Removing a one-class repository for profile lookup/roles avoids a small
  layer of indirection for this procedural application.
- Central exception formatting reduces duplicated catch blocks and makes
  uncaught failures more consistent.

### Trade-offs

- Many helpers are now generic global functions (`getProfile()`, `paginate()`,
  `isParent()`, etc.) rather than namespaced or prefixed functions. That is
  concise, but increases collision risk and can make call ownership less
  obvious in a larger PHP runtime.
- Exception response behavior now depends on the central handler knowing
  domain exception classes and status conventions. That is simpler at call
  sites, but makes the front controller/helper integration important to
  preserve.
- The tagged `members.php` keeps deep indentation from the removed `try`
  wrapper, leaving its main query and mapping blocks indented farther than
  their actual nesting requires.
- The refactor is procedural and endpoint-oriented, but its shared identity
  service remains a large collection of unrelated helpers. The result is
  simpler in ceremony, not necessarily smaller in total logic.

**Readability verdict:** better overall for following individual endpoint
flows, with some consistency and global-namespace costs. Fix the no-op and
indentation before calling the refactor uniformly cleaner.

## Validation limits

This was a static comparison. The environment does not have a PHP executable,
so I could not run `php -l` or the PHP test scripts. The refactor notes state
that PHP linting and a 397-assertion route parity test passed in its original
validation environment; the checked-in phase-7 test primarily verifies route
registration, handler existence, rewrite configuration, and dispatch
parameters rather than end-to-end business behavior. Runtime database-backed
behavior therefore remains unverified here.

## Recommended follow-up

1. Decide whether login validation details must retain empty arrays for fields
   that passed.
2. Confirm the `/auth/me` normalization changes are acceptable.
3. Keep or create the SQLite storage directory if SQLite deployments are
   supported.
4. Remove the route-parameter self-assignment and reindent the family-members
   endpoint.
5. Run PHP lint and the available smoke/payment/community tests in an
   environment with the required PHP extensions, followed by API-level
   regression checks for login, refresh/logout, member/family access, payments,
   and invitation/group transactions.
