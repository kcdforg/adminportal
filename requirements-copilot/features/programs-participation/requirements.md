# Programs and participation requirements

**Status:** Provisional as-is reconstruction from Phase 02 source tracing. No tests or runtime behavior were verified.

## Requirements

### PROG-001 — Maintain programs

- **Actor:** Authenticated caller for reads; elevated admin for create/update/status changes.
- **Requirement:** The API shall list/read programs and permit authorized maintenance of program records.
- **Acceptance criteria (source-derived):**
  - Create validates program name, supported type and fee; updates/status changes validate supported values.
  - Read/list requires authentication and uses source-defined status/type filtering and pagination.
  - Writes are restricted to elevated admin roles and record the observed activity log.
- **Evidence:** `api/programs/*.php`; `services/academics.php`, `services/identity.php`; `schema.sql` (`programs`).
- **Status:** Backend source observed; Angular program fields/enums and update verb do not match the backend contract.

### PROG-002 — Maintain trainers

- **Actor:** Admin or assigned trainer for permitted reads/updates; elevated admin for creation.
- **Requirement:** The API shall list, create, read and update trainer records linked to member profiles.
- **Acceptance criteria (source-derived):**
  - Trainer creation validates identity data and links an eligible active profile.
  - A trainer can read/update their own record only within the source-limited update fields; elevated admins may manage trainers.
  - Trainer/address writes follow the observed transaction.
- **Evidence:** `api/trainers/{index,show,store,update}.php`; `schema.sql` (`trainers`, `addresses`).
- **Status:** Backend source observed; Angular update verb mismatch.

### PROG-003 — Maintain batches

- **Actor:** Elevated admin for maintenance; authenticated roles receive source-limited batch visibility.
- **Requirement:** The API shall create/read/update batches associated with a program and optional trainer.
- **Acceptance criteria (source-derived):**
  - Batch validation checks program, name, capacity, status and date constraints.
  - Batch list/detail access is restricted according to admin, assigned trainer and active/pending enrollment context.
  - Create/update requires elevated-admin access.
- **Evidence:** `api/batches/*.php`; `services/academics.php`; `schema.sql` (`student_batches`, `batch_members`).
- **Status:** Backend source observed; admin client requests an unregistered member-add route and expects different list metadata in some paths.

### PROG-004 — Enroll an eligible family member

- **Actor:** Elevated admin or eligible active family primary.
- **Requirement:** The API shall enroll an active family member into an eligible batch while checking capacity and duplicate membership.
- **Acceptance criteria (source-derived):**
  - Family/member association and caller access are validated.
  - Batch must be upcoming/active and not full.
  - Creation snapshots the fee, records the enrollment and batch membership, and initializes payment status as unpaid within a transaction.
  - Duplicate enrollment is rejected.
- **Evidence:** `api/enrollments/store.php`; `services/academics.php`; `schema.sql` (`enrollments`, `batch_members`).
- **Status:** Backend source observed; admin form includes unsupported fields and mismatched status values.

### PROG-005 — Cancel enrollment

- **Actor:** Caller authorized for the enrollment.
- **Requirement:** The API shall cancel an eligible enrollment and mark the associated batch membership dropped.
- **Acceptance criteria (source-derived):**
  - Access and current state are rechecked under the handler's transaction.
  - Already-cancelled enrollment is rejected.
  - Successful cancellation updates enrollment and batch membership state.
- **Evidence:** `api/enrollments/cancel.php`; `api/routes.php`; `schema.sql` (`enrollments`, `batch_members`).
- **Status:** Backend source observed; admin service calls a different route/payload. Unique member/batch constraints may prevent re-enrollment; intended policy is unresolved.

### PROG-006 — Manage sessions and attendance locks

- **Actor:** Elevated admin for session creation/management; assigned trainer or permitted admin for attendance-related access.
- **Requirement:** The API shall create sessions under a batch, permit supported session updates, and expose a distinct attendance-lock operation.
- **Acceptance criteria (source-derived):**
  - Session data is validated against batch/trainer/time rules.
  - Locking uses `POST /sessions/{id}/lock` and records the locked state.
  - Access follows session/batch permission helpers.
- **Evidence:** `api/batches/session-store.php`, `api/sessions/{show,update,lock}.php`; `services/academics.php`; schema (`batch_sessions`).
- **Status:** Backend source observed; client calls a different session-lock path.

### PROG-007 — Record and read attendance

- **Actor:** Authorized trainer/admin for writes; authorized caller for reads.
- **Requirement:** The API shall record attendance for eligible active batch members in an unlocked session and provide session/member attendance reads.
- **Acceptance criteria (source-derived):**
  - Writes require the server payload shape `records` with attendance status fields.
  - Invalid members/statuses and locked sessions are rejected.
  - Reads return records associated with the session/member data.
- **Evidence:** `api/sessions/attendance.php`, `attendance-store.php`, `api/attendance/patch.php`; `services/academics.php`; schema (`attendance`).
- **Status:** Backend source observed; Angular payload and response-shape expectations differ from the backend.

## Not converted into requirements

Member course pages render `SAMPLE_COURSES`; program/batch service methods had no page call sites in the bounded source review. No requirement is asserted that a course equals a program, batch or enrollment. Admin report pages call unregistered attendance/enrollment report paths; no report calculation requirement is inferred.
