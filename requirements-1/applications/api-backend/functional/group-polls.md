# Group Polls

## Requirement summary

This document defines the expected group polls behavior provided by the API.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

### API-POL-001 — Create group poll

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-001`
**Actor:** Group managers.

**Requirement:** A permitted group manager creates a poll with options and voting settings.

---

### API-POL-002 — Update or publish group poll

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-002`
**Actor:** Group managers.

**Requirement:** A permitted group manager maintains or publishes a poll subject to supported status and voting rules.

---

### API-POL-003 — Cast or change vote

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-003`
**Actor:** Active group members.

**Requirement:** An active group member votes on a published poll using the allowed option or options.

---

### API-POL-004 — View poll results

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-004`
**Actor:** Eligible group participants.

**Requirement:** A permitted participant can request poll results subject to the poll’s visibility rules.

### API-POL-005 — Validate poll option count

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must require between 2 and 10 options when a poll is created or its options are updated.

---

### API-POL-006 — Validate individual poll options

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject poll option labels that are empty or longer than 255 characters.

---

### API-POL-007 — Require active membership to vote

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject poll votes from users who are not active members of the group.

---

### API-POL-008 — Reject voting on closed polls

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject votes on unpublished polls or polls whose closing time has passed.

---

### API-POL-009 — Validate selected vote options

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject empty selections, invalid selected poll options, and multiple selections when multiple voting is disabled.

---

### API-POL-010 — Enforce vote-change setting

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject a replacement vote when the poll does not permit vote changes.

---

### API-POL-011 — Restrict poll results visibility

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must withhold results from a non-settings-manager who has not voted when early results are disabled and the poll has not closed.

---

### API-POL-012 — Prevent editing polls after voting

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject changes to a poll after any vote has been recorded.

---

### API-POL-013 — Count poll responses by option

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must count selected selected poll options and return the total number of voting members.

---
