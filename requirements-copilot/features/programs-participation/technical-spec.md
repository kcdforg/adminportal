# Programs and participation — technical specification

**Evidence status:** Static backend/client/schema/test-source trace. No successful client/API exchange or runtime result is asserted.

| ID | Current behavior and interfaces | Status / boundary |
|---|---|---|
| PROG-001 | Program list/read/create/update/status handlers in `api/programs/*.php`; `services/academics.php` validation/access; `programs` table. Admin service/forms use mismatched program field/enums and `PATCH` update while PHP registers `PUT`. | **Partial** |
| PROG-002 | Trainer list/read/create/update in `api/trainers/*.php`; trainer profile/address data and identity validation; assigned trainer access is source-limited. Admin service uses `PATCH` while server registers `PUT`. | **Partial** |
| PROG-003 | Batch list/read/create/update, member listing and sessions endpoints in `api/batches/*.php`; `canViewBatch`, `validateBatch`; `student_batches`, `batch_members`. Admin update uses `PATCH` vs `PUT`; its add-members route is unregistered. | **Partial** |
| PROG-004 | `POST /enrollments` validates family association/access, batch status/capacity and duplicates, snapshots fee and creates enrollment plus batch membership unpaid in a transaction. | **Partial**: admin form fields/statuses do not align with server. |
| PROG-005 | `PATCH /enrollments/{id}/cancel` checks access/state transactionally and updates enrollment and batch membership. | **Partial**: admin sends a different path/body. Unique member/batch keys constrain reuse after cancel; intent unresolved. |
| PROG-006 | Session creation via `POST /batches/{id}/sessions`; session read/update and separate `POST /sessions/{id}/lock`; validation/access in academics service; `batch_sessions`. | **Partial**: admin uses `PATCH` and sends lock state to update instead of the lock operation. |
| PROG-007 | Attendance reads/writes on `/sessions/{id}/attendance`, per-record update at `/attendance/{id}`; server validates `records`, status, eligibility and lock state; `attendance` table. | **Partial**: admin body key/shape differs (`attendance` vs server `records`). |

## Interfaces and source

- Route registry: `api-backend-php/api/routes.php`; handlers `api/programs/`, `api/trainers/`, `api/batches/`, `api/sessions/`, `api/attendance/`, `api/enrollments/`.
- Validation/access helpers: `api-backend-php/services/academics.php`, `services/identity.php`; schema `database/schema.sql` (`programs`, `trainers`, `student_batches`, `batch_members`, `batch_sessions`, `attendance`, `enrollments`).
- Client: admin `core/services/{program,trainer,batch,enrollment,session,attendance}.service.ts` and corresponding feature forms/lists/detail components. Member `courses/` reads sample data and is not evidence of program/enrollment integration.
- Registered-route/OpenAPI and request contract details: `docs/architecture/api-contract-audit.md`.

## Unresolved matters

Do not equate sample courses with program/batch records or infer cancelled-enrollment reuse intent. Server/client mismatches remain findings, not replacement contracts. See provisional Phase 05 audit at `baseline-workflow/analysis/requirements-accuracy-audit.md`.
