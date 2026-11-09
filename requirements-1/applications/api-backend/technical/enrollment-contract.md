# Enrollment API Contract

This document records the intended request paths and data contract for enrollment operations.

## API operations

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/v1/enrollments` | List enrollments |
| POST | `/api/v1/enrollments` | Create an enrollment |
| GET | `/api/v1/enrollments/{id}` | View an enrollment |
| PATCH | `/api/v1/enrollments/{id}/cancel` | Cancel an enrollment |

## Creation data

Enrollment creation requires `family_id`, `member_id`, and `batch_id`. A successful enrollment is associated with the selected family member and batch. The system initializes its payment state according to the agreed enrollment and fee rules.

## Integration issue to resolve

The Admin Portal cancellation action must call the cancellation path and provide the data expected by the API. Align the portal request and API contract, then verify the cancellation flow end to end before treating it as complete.
