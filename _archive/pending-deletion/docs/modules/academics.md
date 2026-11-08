# Academics Module

## Scope

Manages programs, student batches, batch sessions, attendance, and enrollments.

---

## Tables

- `programs` — program/class definition
- `student_batches` — scheduled run of a program
- `batch_members` — members in a batch
- `batch_sessions` — individual sessions
- `attendance` — per-session attendance
- `enrollments` — family enrolls a member into a batch

See `docs/01-database.md` for full schema with MySQL types.

---

## Business Rules

### Programs
1. A program defines the template: name, type, fee, age group, description.
2. Program types: `class`, `workshop`, `camp`, `event`.
3. A program can have multiple batches running simultaneously or at different times.
4. Archiving a program (`status = archived`) does not affect existing active batches.

### Student Batches
5. A batch is a scheduled run of a program, assigned to a trainer.
6. A batch has an optional `capacity`. If capacity is set, enrollment is blocked when the batch is full.
7. Batch status transitions: `upcoming → active → completed`. `cancelled` is allowed from any state.
8. A batch without a trainer can still be created (trainer assignment is optional at creation).
9. Changing a batch's trainer does not retroactively change session-level `trainer_id`.

### Batch Sessions
10. Sessions belong to a batch and inherit the batch's trainer by default.
11. A session can override the batch-level trainer (for substitutions).
12. Session types: `regular`, `special`, `exam`, `workshop`.
13. Session status: `scheduled → completed` or `scheduled → cancelled` or `scheduled → postponed`.
14. When a session is `completed`, it should have `topics_covered` populated.
15. `attendance_locked = true` means no attendance can be added or modified for that session.
16. A session cannot be unlocked once locked, except by `super_admin` or `program_manager`.
17. Only completed or past sessions can be locked.

### Attendance
18. Attendance is per-session per-member. One record per (batch_session_id, member_id) pair — enforced by unique constraint.
19. Attendance can only be recorded for members who are in `batch_members` for that session's batch.
20. Attendance cannot be recorded for a locked session (`attendance_locked = true`).
21. `marked_by_member_id` records who marked the attendance (trainer or admin).
22. `marked_at` is set at the time of recording.
23. Attendance status values: `present`, `absent`, `late`, `excused`.
24. A trainer can only mark attendance for their own sessions.
25. Admin (`program_manager`, `super_admin`) can mark or edit attendance for any session.

### Enrollments
26. An enrollment links a family + member + batch together.
27. A member can only be enrolled once in a given batch (unique constraint on member_id + batch_id).
28. Only `primary` family members or admins can create enrollments.
29. `fee_amount` on the enrollment is captured at enrollment time (snapshot of program fee at that moment).
30. `payment_status` reflects the current payment state: `unpaid`, `partial`, `paid`, `refunded`.
31. `payment_status` is updated when payments are recorded against the enrollment.
32. Cancelling an enrollment (`status = cancelled`) does not automatically refund payments.
33. A student cannot be enrolled in a batch that is `completed` or `cancelled`.
34. Enrollment in a full batch (capacity reached) is rejected with error `BATCH_FULL`.

---

## Access Control Matrix

| Action | family_primary | family_normal | family_student | trainer | admin |
|---|---|---|---|---|---|
| View programs | Yes | Yes | Yes | Yes | Yes |
| Create/edit program | No | No | No | No | super/pm |
| View own batch details | Yes | Yes | Yes | Yes | Yes |
| View all batches | No | No | No | trainer's own | Yes |
| Create batch | No | No | No | No | super/pm |
| Edit batch | No | No | No | No | super/pm |
| View sessions | Yes | Yes | Yes | trainer's own | Yes |
| Create session | No | No | No | No | super/pm |
| Edit session | No | No | No | trainer's own (notes/topics) | super/pm |
| Mark attendance | No | No | No | trainer's own sessions | super/pm |
| Edit attendance | No | No | No | trainer's own (if not locked) | super/pm |
| View attendance | Yes | Yes | student's own | trainer's sessions | Yes |
| Enroll member | Yes (own family) | No | No | No | Yes |
| Cancel enrollment | Yes (own family) | No | No | No | Yes |
| View enrollments | Yes (own family) | Yes (view only) | student's own | No | Yes |

---

## API Endpoints

### Programs

#### GET /api/v1/programs
Any authenticated user. Supports filters: `status`, `program_type`.

#### POST /api/v1/programs
Admin (super_admin, program_manager) only.

**Request:**
```json
{
  "program_name": "Vedic Maths Level 1",
  "program_type": "class",
  "description": "Introductory Vedic Maths for ages 8-12",
  "age_group": "8-12",
  "fee_amount": 1500.00
}
```

#### GET /api/v1/programs/{id}
Any authenticated user.

#### PUT /api/v1/programs/{id}
Admin (super_admin, program_manager) only.

---

### Batches

#### GET /api/v1/batches
Filtered by role. Admin sees all. Trainer sees assigned batches. Family sees enrolled batches.

**Query params:** `program_id`, `status`, `trainer_id`

#### POST /api/v1/batches
Admin only.

**Request:**
```json
{
  "program_id": 3,
  "batch_name": "Vedic Maths - Batch A - 2026",
  "capacity": 25,
  "trainer_id": 2,
  "start_date": "2026-01-10",
  "end_date": "2026-06-30"
}
```

#### GET /api/v1/batches/{id}
Admin, trainer (own batches), or member enrolled in batch.

#### PUT /api/v1/batches/{id}
Admin only.

#### GET /api/v1/batches/{id}/members
Admin or trainer of the batch.

#### POST /api/v1/batches/{id}/members
Admin only. (Members added via enrollment flow.)

#### GET /api/v1/batches/{id}/sessions
Admin, trainer of batch, or enrolled member.

**Query params:** `status`, `session_date_from`, `session_date_to`

---

### Sessions

#### POST /api/v1/batches/{id}/sessions
Admin (super_admin, program_manager) only.

**Request:**
```json
{
  "session_number": 1,
  "session_title": "Introduction to Vedic Maths",
  "session_date": "2026-01-10",
  "start_time": "10:00",
  "end_time": "11:30",
  "session_type": "regular",
  "trainer_id": 2
}
```

#### GET /api/v1/sessions/{id}
Admin, trainer, or enrolled member.

#### PUT /api/v1/sessions/{id}
Admin (all fields). Trainer (own session, limited fields: `topics_covered`, `homework`, `notes` only).

#### POST /api/v1/sessions/{id}/lock
Admin (super_admin, program_manager) only. Locks attendance.

---

### Attendance

#### GET /api/v1/sessions/{id}/attendance
Admin or trainer of session. Returns all attendance records for the session.

#### POST /api/v1/sessions/{id}/attendance
Trainer (own session) or admin. Creates or bulk-updates attendance records.

**Request:**
```json
{
  "records": [
    { "member_id": 10, "attendance_status": "present", "remarks": null },
    { "member_id": 11, "attendance_status": "absent", "remarks": "Informed in advance" },
    { "member_id": 12, "attendance_status": "late", "remarks": null }
  ]
}
```

**Errors:**
- 403 — session attendance is locked
- 403 — trainer not assigned to this session
- 422 — member not in batch

#### PATCH /api/v1/attendance/{id}
Trainer (own session, not locked) or admin.

---

### Enrollments

#### GET /api/v1/enrollments
Admin sees all. Primary family member sees own family's enrollments.

**Query params:** `family_id`, `batch_id`, `status`, `payment_status`

#### POST /api/v1/enrollments
Primary family member (own family) or admin.

**Request:**
```json
{
  "family_id": 1,
  "member_id": 5,
  "batch_id": 3
}
```

**Errors:**
- 422 — member already enrolled in batch (`ENROLLMENT_EXISTS`)
- 422 — batch is full (`BATCH_FULL`)
- 422 — batch is not active or upcoming

#### GET /api/v1/enrollments/{id}
Admin, primary family member of the enrolled family, or the enrolled member themselves.

#### PATCH /api/v1/enrollments/{id}
Admin only. Update status (e.g., cancel enrollment).

---

## Validation Rules

| Field | Rule |
|---|---|
| `program_name` | required, string, max 255 |
| `program_type` | required, must be: class, workshop, camp, event |
| `fee_amount` | required, numeric, min 0, max 2 decimal places |
| `batch_name` | required, string, max 255 |
| `capacity` | optional, integer, min 1 |
| `start_date` | optional, valid date |
| `end_date` | optional, valid date, must be after start_date |
| `session_date` | required, valid date |
| `start_time` | optional, valid time format HH:MM |
| `end_time` | optional, valid time, must be after start_time |
| `session_type` | required, must be: regular, special, exam, workshop |
| `attendance_status` | required, must be: present, absent, late, excused |
| `enrollment.family_id` | required, must exist |
| `enrollment.member_id` | required, must be a member of the given family |
| `enrollment.batch_id` | required, batch must be upcoming or active |

---

## Module Folder Structure

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
└── Policies/
    ├── BatchPolicy.php
    ├── SessionPolicy.php
    ├── AttendancePolicy.php
    └── EnrollmentPolicy.php
```
