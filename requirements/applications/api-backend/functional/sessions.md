# Sessions

## Requirement summary

This document defines the expected sessions behavior provided by the API.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

### API-SES-001 — Browse batch sessions

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-001`
**Actor:** Authorized batch viewers.

**Requirement:** An eligible user browses sessions associated with a batch.

---

### API-SES-002 — View session details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-002`
**Actor:** Authorized batch viewers.

**Requirement:** An eligible user opens a particular batch session.

---

### API-SES-003 — Schedule batch session

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-003`
**Actor:** Elevated administrators and authorized editors.

**Requirement:** An eligible user creates a session for an existing batch.

---

### API-SES-004 — Update batch session

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-004`
**Actor:** Elevated administrators and authorized editors.

**Requirement:** An eligible user changes the supported details of an existing session.

---

### API-SES-005 — Lock session attendance

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `SES-005`
**Actor:** Authorized attendance managers.

**Requirement:** An authorized manager can lock a session’s attendance to prevent further changes.
