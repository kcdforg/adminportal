# Enrollment API — source-observed contract

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Status:** Static source review; not an integration or runtime test.

| Method | Path | Handler |
|---|---|---|
| GET | `/api/v1/enrollments` | `api/enrollments/index.php` |
| POST | `/api/v1/enrollments` | `api/enrollments/store.php` |
| GET | `/api/v1/enrollments/{id}` | `api/enrollments/show.php` |
| PATCH | `/api/v1/enrollments/{id}/cancel` | `api/enrollments/cancel.php` |

Source: `api-backend-php/api/routes.php`.

## Known integration discrepancy — INT-005

The Admin Portal `EnrollmentService.cancel(id)` sends `PATCH /enrollments/${id}` with `{status: 'cancelled'}`. The backend route requires `/enrollments/{id}/cancel`. The Angular API service's base URL may supply `/api/v1`; it does **not** resolve the missing `/cancel` path segment. This is a confirmed static contract mismatch, not a runtime test.

Source: `kcdf-admin-app/src/app/core/services/enrollment.service.ts` and `api-backend-php/api/routes.php`.

## Creation contract

`family_id`, `member_id`, `batch_id` are required numeric values. The handler stores enrollment and batch membership transactionally, with `status=active` and `payment_status=unpaid`. Other request fields are not presumed persisted merely because the frontend model includes them.

Source: `api-backend-php/api/enrollments/store.php`.