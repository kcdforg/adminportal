# Endpoints

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

## Scope

This is a current-state implementation summary intended to support planning and backlog review. It is not a signed-off technical specification or an approved production baseline.

## Verified route inventory

- `api-backend-php/api/routes.php`
- auth routes: `/api/v1/auth/login`, `/api/v1/auth/admin/login`, `/api/v1/auth/refresh`, `/api/v1/auth/logout`, `/api/v1/auth/me`
- primary domain routes for programs, batches, sessions, attendance, enrollments, payments, groups, invitations, members, families, trainers, and administrators

Technical behavior must still be extracted to formal acceptance criteria and validated with runtime and integration tests before approval as a complete baseline.