# Attendance

## Requirement summary

This document defines the expected attendance behavior provided by the API.

### API-ATT-001 — Read session attendance

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ATT-001`
**Actor:** Authorized attendance viewers.

**Requirement:** A permitted user can inspect attendance recorded for a selected session.

---

### API-ATT-002 — Record session attendance

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ATT-002`
**Actor:** Authorized attendance recorders.

**Requirement:** A permitted user can record attendance for members of a session’s batch.

---

### API-ATT-003 — Update an attendance record

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ATT-003`
**Actor:** Authorized attendance editors.

**Requirement:** A permitted user can correct an existing attendance entry when the session permits editing.

## Additional requirements

### API-ATT-004 — Require attendance record for update

**Requirement:** The attendance update operation returns not found when the attendance record does not exist.

### API-ATT-005 — Authorize attendance updates

**Requirement:** The attendance update operation rejects actors without access to the associated session and batch.

### API-ATT-006 — Reject updates to locked attendance

**Requirement:** The attendance update operation rejects updates when the associated session has attendance locked.

### API-ATT-007 — Require attendance status on update

**Requirement:** The attendance update operation requires attendance status in the request.

### API-ATT-008 — Restrict attendance status values

**Requirement:** The attendance update operation accepts only present, absent, late or excused as attendance status.

### API-ATT-009 — Restrict updated attendance fields

**Requirement:** Attendance updates may change only the attendance status and remarks.

### API-ATT-010 — Record attendance update actor

**Requirement:** An attendance update records who made the change and when it was made.

### API-ATT-011 — Recheck lock inside attendance transaction

**Requirement:** Session access and lock status are checked again when an attendance change is saved.
