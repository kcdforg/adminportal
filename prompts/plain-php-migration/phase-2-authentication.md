# Phase 2 — Authentication and Authorization

## Context

Phases 0 and 1 are complete. Port the Slim Auth module and shared authorization
behavior into `api-backend-php/`. Use the Phase 0 endpoint inventory. Do not
implement other feature modules in this phase.

## Endpoints

Implement the inventory-mapped direct endpoint files for login, refresh,
logout, and current-user/profile retrieval. Register the existing
`/api/v1/auth/...` URLs and methods in FastRoute, mapping each route to its
fixed direct endpoint PHP file.

## Preserve

- Username/password verification using `password_verify()`.
- Active-account checks and `last_login_at` updates.
- Existing JWT claim names, role values, family IDs, and access-token
  semantics.
- Separate access-token and refresh-token lifetimes.
- Hashed refresh-token persistence, expiry checks, rotation, and revocation
  using the existing `refresh_tokens` table.
- Rejection of refresh tokens when an access token is required.
- The source distinction between unauthenticated and authenticated-but-forbidden
  requests.
- Existing login/profile/token response data and JSON envelope.

Implement `createToken()` and `authenticate()` in `config/init.php` (or a
small shared include required by it). Endpoints must not use the JWT library
directly. Keep JWT parsing, algorithm allowlisting, signing, and validation
inside the shared authentication implementation.

## Security requirements

- Fail startup clearly if the JWT secret is missing or too weak for the
  configured algorithm.
- Never log raw tokens, authorization headers, passwords, or credential
  payloads.
- Do not trust client-supplied profile IDs, roles, or family ownership.
- Use prepared statements and transaction boundaries for token rotation.
- Surface internal configuration failures in logs; return safe JSON errors.

## Tests and exit checks

Add tests for successful/failed login, inactive accounts, malformed/expired/
wrong-type tokens, role and family claims, refresh rotation, replay of a
rotated token, and logout revocation. Confirm 401 versus 403 behavior and
response compatibility with the inventory. Do not proceed until these pass.
