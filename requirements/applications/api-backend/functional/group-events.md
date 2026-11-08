# Group Events

## Requirement summary

This document defines the expected group events behavior provided by the API.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

### API-EVT-001 — Create group event

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-001`
**Actor:** Group managers.

**Requirement:** A group manager with the required permissions creates an event for a group.

---

### API-EVT-002 — Update group event

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-002`
**Actor:** Group managers.

**Requirement:** A permitted group manager edits an existing event, subject to system validations.

---

### API-EVT-003 — Register for an event

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-003`
**Actor:** Active group members.

**Requirement:** An eligible group member registers for a published group event when registration is available.

---

### API-EVT-004 — View own event registration

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-004`
**Actor:** Registered group members.

**Requirement:** An eligible member checks their own event registration status.

---

### API-EVT-005 — Cancel own event registration

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-005`
**Actor:** Registered group members.

**Requirement:** A registered group member can cancel their own active event registration when cancellation is permitted.

### API-EVT-006 — Validate event dates

**Type:** Functional
**Owner:** api-backend
**Requirement:** An event must have valid start and end dates, and its end time must be later than its start time.

---

### API-EVT-007 — Validate event capacity

**Type:** Functional
**Owner:** api-backend
**Requirement:** When a capacity is supplied, it must be a positive whole number.

---

### API-EVT-008 — Require active membership to register

**Type:** Functional
**Owner:** api-backend
**Requirement:** Only active group members may register for a group event.

---

### API-EVT-009 — Reject registration for unavailable events

**Type:** Functional
**Owner:** api-backend
**Requirement:** Members cannot register for unpublished events or events that have already started.

---

### API-EVT-010 — Prevent duplicate active registrations

**Type:** Functional
**Owner:** api-backend
**Requirement:** A member cannot hold duplicate active registrations for the same event.

---

### API-EVT-011 — Enforce event registration capacity

**Type:** Functional
**Owner:** api-backend
**Requirement:** Registration is unavailable once an event’s capacity has been reached.

---

### API-EVT-012 — Reactivate cancelled registrations

**Type:** Functional
**Owner:** api-backend
**Requirement:** If a member registers again after cancelling, the system restores the existing registration rather than creating a duplicate.

---

### API-EVT-013 — Cancel only own active registration

**Type:** Functional
**Owner:** api-backend
**Requirement:** Cancelling an event registration changes only the requesting member’s active registration to cancelled.

---
