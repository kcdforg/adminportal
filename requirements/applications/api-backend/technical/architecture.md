# Architecture

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

This is a current-state implementation summary for planning and review. It is not a signed-off technical specification.

## Verified source inventory

The backend route registry is declared in `api-backend-php/api/routes.php` and includes the following groups verified against the repository as of 2026-11-08:

- Authentication: `/api/v1/auth/login`, `/api/v1/auth/admin/login`, `/api/v1/auth/refresh`, `/api/v1/auth/logout`, `/api/v1/auth/me`
- Programs: list/create/show/update/status
- Batches: list/create/show/update/members/sessions/session creation
- Sessions: show/update/lock/attendance and attendance creation
- Attendance: patch update by attendance ID
- Enrollments: list/create/show/cancel
- Payments: list/create/show/update/family payments
- Groups: list/create/categories/member-candidates/show/update/members/join/leave/member role and group resources
- Invitations: list/create/cancel/show-by-code/accept
- Members: list/create/show/update/login management/entity relations
- Families: list/create/show/update/member management
- Trainers: list/create/show/update/delete and trainer relations
- Administrators: list/create/show/update/delete and admin-level management routes

## Source-checked implementation notes

- JWT validation is implemented in `api-backend-php/services/auth.php`.
- The login handlers in `api-backend-php/api/auth/login.php` and `api-backend-php/api/auth/admin-login.php` enforce required `username` and `password` fields before calling `authenticatePortalLogin`.
- The refresh flow in `api-backend-php/api/auth/refresh.php` checks for missing `refresh_token`, validates a refresh token type, verifies a stored unrevoked token, rotates it, and issues replacement credentials inside a transaction.
- `api-backend-php/api/auth/me.php` validates the bearer token, resolves the active profile, and returns the authenticated user context.

Technical behavior must still be extracted to formal acceptance criteria and verified with runtime and integration testing before approval as a complete baseline.