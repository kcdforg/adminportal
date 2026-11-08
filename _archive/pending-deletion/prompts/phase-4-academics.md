# Phase 4 — Academics Module

## Context

You are continuing development of `kcdf-api-backend` (Slim Framework 4).
Phases 1, 2, and 3 are complete (schema, auth, families/members).

This phase implements the **Academics module** covering:
- Programs (class/workshop/camp/event definitions)
- Student Batches (scheduled runs of a program)
- Batch Members (who is in each batch)
- Batch Sessions (individual sessions within a batch)
- Attendance (per-session attendance marking)
- Enrollments (family enrolls a member into a batch)

Do NOT implement Payments, Community, or Notifications in this phase.

---

## Module Location

```
src/Modules/Academics/
├── Controllers/
│   ├── ProgramController.php
│   ├── BatchController.php
│   ├── SessionController.php
│   ├── AttendanceController.php
│   └── EnrollmentController.php
├── Services/
│   ├── ProgramService.php
│   ├── BatchService.php
│   ├── SessionService.php
│   ├── AttendanceService.php
│   └── EnrollmentService.php
├── Repositories/
│   ├── ProgramRepository.php
│   ├── BatchRepository.php
│   ├── BatchMemberRepository.php
│   ├── SessionRepository.php
│   ├── AttendanceRepository.php
│   └── EnrollmentRepository.php
├── Models/
│   ├── Program.php
│   ├── StudentBatch.php
│   ├── BatchMember.php
│   ├── BatchSession.php
│   ├── Attendance.php
│   └── Enrollment.php
├── DTOs/
│   ├── CreateProgramDTO.php
│   ├── CreateBatchDTO.php
│   ├── CreateSessionDTO.php
│   ├── AttendanceRecordDTO.php
│   └── CreateEnrollmentDTO.php
├── Validators/
│   ├── ProgramValidator.php
│   ├── BatchValidator.php
│   ├── SessionValidator.php
│   ├── AttendanceValidator.php
│   └── EnrollmentValidator.php
├── Policies/
│   ├── BatchPolicy.php
│   ├── SessionPolicy.php
│   ├── AttendancePolicy.php
│   └── EnrollmentPolicy.php
└── routes.php
```

---

## Endpoints to Implement

### Programs
- GET /api/v1/programs — All authenticated, filter: status, program_type
- POST /api/v1/programs — Admin (super_admin, program_manager) only
- GET /api/v1/programs/{id} — All authenticated
- PUT /api/v1/programs/{id} — Admin only
- PATCH /api/v1/programs/{id}/status — Admin only (activate/archive)

### Batches
- GET /api/v1/batches — Role-filtered (admin=all, trainer=own, family=enrolled)
- POST /api/v1/batches — Admin only
- GET /api/v1/batches/{id} — Admin, assigned trainer, or enrolled member
- PUT /api/v1/batches/{id} — Admin only
- GET /api/v1/batches/{id}/members — Admin or batch trainer
- GET /api/v1/batches/{id}/sessions — Admin, trainer, or enrolled member; filter: status, session_date range
- POST /api/v1/batches/{id}/sessions — Admin only

### Sessions
- GET /api/v1/sessions/{id} — Admin, trainer, or enrolled member
- PUT /api/v1/sessions/{id} — Admin (all fields) or trainer of session (topics_covered, homework, notes only)
- POST /api/v1/sessions/{id}/lock — Admin (super_admin, program_manager) only
- GET /api/v1/sessions/{id}/attendance — Admin or trainer of session
- POST /api/v1/sessions/{id}/attendance — Admin or trainer of session (bulk submit)

### Attendance
- PATCH /api/v1/attendance/{id} — Admin or trainer of session (if not locked)

### Enrollments
- GET /api/v1/enrollments — Admin sees all; primary family member sees own family
- POST /api/v1/enrollments — Admin or primary family member (own family only)
- GET /api/v1/enrollments/{id} — Admin, primary family member of family, or enrolled member
- PATCH /api/v1/enrollments/{id}/cancel — Admin or primary family member of family

---

## Business Rules to Enforce

### Programs
1. Archiving a program does not affect active batches

### Batches
2. If capacity is set, reject enrollment when batch is full (count active batch_members >= capacity)
3. Batch status transitions: upcoming → active → completed; cancelled from any state
4. Trainer assignment on batch is optional

### Sessions
5. When trainer_id is null on a session, default to batch.trainer_id
6. `attendance_locked` can only be set to true, never back to false (except by super_admin)
7. Session status transitions: scheduled → completed/cancelled/postponed

### Attendance
8. Reject marking attendance if session.attendance_locked = true → return ATTENDANCE_LOCKED error
9. Reject marking attendance for a member not in batch_members for that batch
10. Reject if trainer marking attendance for a session they are not assigned to (unless admin)
11. Bulk attendance POST: upsert behavior (insert if no record exists, update if it does)
12. Set marked_by_member_id and marked_at on each record

### Enrollments
13. Reject duplicate enrollment: same member + batch → return ENROLLMENT_EXISTS error
14. Reject enrollment in a batch with status = completed or cancelled
15. Reject enrollment if batch is full → return BATCH_FULL error
16. On enrollment creation: set fee_amount = program.fee_amount (snapshot at time of enrollment)
17. On enrollment creation: also create a batch_members record (member added to batch)
18. On enrollment cancellation: set batch_members.status = 'dropped'

---

## Attendance Bulk Submit Format

```
POST /api/v1/sessions/{id}/attendance
{
  "records": [
    { "member_id": 10, "attendance_status": "present", "remarks": null },
    { "member_id": 11, "attendance_status": "absent", "remarks": "Informed via phone" }
  ]
}
```

Response: 200 with count of records saved.

---

## Validation Rules

Programs:
- program_name: required, string, max 255
- program_type: required, one of: class, workshop, camp, event
- fee_amount: required, numeric, min 0, max 2 decimal places

Batches:
- program_id: required, must exist
- batch_name: required, string, max 255
- capacity: optional, integer min 1
- trainer_id: optional, must exist in trainers table if provided
- end_date: must be after start_date if both provided

Sessions:
- session_date: required, valid date
- session_type: required, one of: regular, special, exam, workshop
- end_time: must be after start_time if both provided

Attendance:
- attendance_status: required, one of: present, absent, late, excused
- member_id: must be in batch_members for the session's batch

Enrollments:
- family_id: required, must exist
- member_id: required, must be a member of the given family
- batch_id: required, batch must be upcoming or active

---

## Rules

- All access control enforced in Policy classes
- Services throw typed exceptions: UnauthorizedException, NotFoundException, ValidationException, BusinessRuleException
- No raw SQL — all DB access through Repositories
- Paginate all list endpoints
- Register routes in src/Modules/Academics/routes.php and include in routes/api.php
