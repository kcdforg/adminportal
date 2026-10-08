# Phase 4 — Academics and Enrollment

## Context

Phases 0–3 are complete. Port the Slim Academics module according to the
endpoint inventory. Do not implement payments, community, or notifications
in this phase.

## Scope

- Programs
- Student batches and batch members
- Batch sessions
- Attendance
- Enrollments

## Preserve

- Existing role- and ownership-filtered list/detail access.
- Program/batch/session status values and allowed transitions.
- Batch capacity checks and active-member counts.
- Session trainer defaults, attendance locking, and rules governing who can
  edit sessions or attendance.
- Bulk attendance validation and the unique session/member relationship.
- Enrollment family membership, batch eligibility, capacity and duplicate
  checks, fee snapshotting, cancellation behavior, and linked batch-member
  updates.
- Existing audit log actions and response/pagination contracts.

Use transactions for enrollment creation/cancellation and any multi-record
attendance operation. Ensure checks that prevent over-capacity or duplicate
enrollment are safe against concurrent requests where the existing database
constraints permit it; do not claim race safety based solely on an unlocked
pre-check.

Keep simple CRUD in direct endpoints. Put reusable business rules and
multi-table workflows in `services/`; use Medoo for simple queries and PDO on
the shared connection where explicit transaction or SQL control is needed.

## Exit checks

- Every academics endpoint in the inventory has been implemented.
- Tests cover batch capacity, state transitions, access scoping, attendance
  lock enforcement, bulk attendance, duplicate enrollment, cancellation, and
  rollback.
- Existing schema, endpoint envelopes, and auth claims remain compatible.
