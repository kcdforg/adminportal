# Sessions and Attendance

This document covers scheduling batch sessions and recording attendance.

## Status

Working draft. Confirm attendance statuses and correction/locking policies.

### ADM-SES-001 — Schedule and update a session

**Actor:** Administrators with session-management access.

**Requirement:** An authorized administrator can schedule a session for a batch and update its supported details.

**Acceptance criteria:**
- The session is associated with a valid batch.
- Required date and session information are validated.
- The saved session is visible in the batch’s session list.

### ADM-SES-002 — Record attendance

**Actor:** Staff with attendance-recording access.

**Requirement:** An authorized user can record attendance for eligible members of a session.

**Acceptance criteria:**
- The user can distinguish members with recorded attendance from those not yet recorded.
- Attendance is saved against the correct session and member.
- The user receives clear confirmation or validation feedback.

### ADM-SES-003 — Review, correct, and lock attendance

**Actor:** Users with attendance-management access.

**Requirement:** An authorized user can review attendance, correct it when permitted, and lock a completed record if required.

**Acceptance criteria:**
- Saved attendance remains visible when the session is reopened.
- Corrections follow the agreed permissions and lock rules.
- A locked record clearly indicates that further changes are restricted.

## Open questions

- Define attendance values, late/partial attendance rules, and correction permissions.
- Confirm whether locking is required and whether a locked session can be reopened.
