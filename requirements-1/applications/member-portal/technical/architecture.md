# Architecture

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

This is a current-state implementation summary for planning and review. It is not a signed-off technical specification.

## Verified member-portal source inventory

The member portal app references the following source assets and route wiring that were inspected in the repository:

- `kcdf-parents-app/src/app/app.routes.ts`
- `kcdf-parents-app/src/app/core/member-auth.service.ts`
- related member screens and route guards in the app shell and login screens

## Source-checked implementation notes

- The member app defines its own route structure and authentication integration points.
- The backend member login flow is separate from the admin flow and is routed through `api-backend-php/api/auth/login.php` with portal type `member`.
- Route and auth behaviors must still be validated in the running application before they are treated as approved requirements.

Technical behavior must still be extracted to formal acceptance criteria and validated with runtime and integration tests before approval as a complete baseline.