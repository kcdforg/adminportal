# Architecture

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

This is a current-state implementation summary for planning and review. It is not a signed-off technical specification.

## Verified admin-portal source inventory

The admin portal app references the following source assets and route wiring that were inspected in the repository:

- `kcdf-admin-app/src/app/app.routes.ts`
- `kcdf-admin-app/src/app/app.config.ts`
- `kcdf-admin-app/src/app/core/guards/auth.guard.ts`
- `kcdf-admin-app/src/app/core/guards/role.guard.ts`
- `kcdf-admin-app/src/app/core/services/auth.service.ts`

## Source-checked implementation notes

- The admin app uses route guards and role-based access checks for protected screens.
- The app’s auth service is wired to the backend auth endpoints defined in `api-backend-php/api/routes.php`.
- The backend distinguishes member and admin portal authentication; the admin portal requirements must reflect backend semantics, not just UI labels.

Technical behavior must still be extracted to formal acceptance criteria and validated with runtime and integration tests before approval as a complete baseline.