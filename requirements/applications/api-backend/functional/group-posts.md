# Group Posts

## Requirement summary

This document defines the expected group posts behavior provided by the API.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

### API-POST-001 — List group announcements

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-001`
**Actor:** Group participants.

**Requirement:** An eligible participant browses announcements visible within their group.

---

### API-POST-002 — Read group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-002`
**Actor:** Group participants.

**Requirement:** An eligible participant opens a group announcement they are allowed to view.

---

### API-POST-003 — Create group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-003`
**Actor:** Group managers.

**Requirement:** A permitted group manager creates an announcement in a group.

---

### API-POST-004 — Edit group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-004`
**Actor:** Group managers.

**Requirement:** A permitted group manager changes an existing announcement.

---

### API-POST-005 — Archive group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-005`
**Actor:** Group managers.

**Requirement:** A permitted group manager archives an announcement using the supported system operation.

### API-PST-005 — Restrict content creation to group managers

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must allow group content creation only for a community administrator or a group owner, admin, or moderator.

---

### API-PST-006 — Reject content creation in inactive groups

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject creation of posts, polls, and events when the group is not active.

---

### API-PST-007 — Validate content titles

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject a missing, empty, or over-255-character group content title.

---

### API-PST-008 — Limit content body length

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject group content bodies longer than 20,000 characters.

---

### API-PST-009 — Restrict unpublished content visibility

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must exclude draft and archived group content from non-manager list results.

---

### API-PST-010 — Paginate group content listings

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must limit requested page size to between 1 and 100 items and return pagination metadata.

---

### API-PST-011 — Restrict status changes to settings managers

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must reject a group content status update unless the actor has group settings-management permission.

---

### API-PST-012 — Archive content instead of deleting

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must mark group content as archived when an authorized actor requests deletion.

---

### API-PST-013 — Authorize content editing and archiving

**Type:** Functional
**Owner:** api-backend
**Requirement:** The system must permit content editing and archiving only to a group manager or its eligible creator.

---
