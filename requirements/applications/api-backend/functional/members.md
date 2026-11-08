# Members

## Requirement summary

This document defines the expected members behavior provided by the API.

### API-MEM-001 — Browse member profiles

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-001`
**Actor:** Administrators.

**Requirement:** An administrator browses member profiles.

---

### API-MEM-002 — View member profile

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-002`
**Actor:** Administrators and member account holders.

**Requirement:** An administrator views an eligible profile; a member can view their own profile in the parent portal.

---

### API-MEM-003 — Create member profile

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-003`
**Actor:** Elevated administrators.

**Requirement:** An elevated administrator creates a member profile.

---

### API-MEM-004 — Update member profile

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-004`
**Actor:** Elevated administrators and profile owners.

**Requirement:** An administrator or profile owner edits permitted information on an existing member profile.

---

### API-MEM-005 — Create member login

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-005`
**Actor:** Authorized account managers.

**Requirement:** An eligible account manager creates login credentials linked to a member profile.

---

### API-MEM-006 — Maintain member login

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-006`
**Actor:** Authorized account managers.

**Requirement:** An eligible account manager updates supported member-login details using the available API operations.

---

### API-MEM-007 — Manage member entity relations

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `MEM-007`
**Actor:** Authorized relation managers.

**Requirement:** An eligible user can maintain the relationship between a member and an entity.

## Additional requirements

### API-MEM-012 — Restrict member profile creation

**Requirement:** Only elevated administrators can create member profiles through the member creation operation.

### API-MEM-011 — Reject duplicate member email on creation

**Requirement:** The system rejects a new member profile when its email is already used by another profile.

### API-MEM-008 — Create member profiles as active

**Requirement:** New member profiles are stored with active status.

### API-MEM-009 — Restrict member profile updates

**Requirement:** The system checks member edit permission before changing a member profile.

### API-MEM-010 — Reject duplicate member email on update

**Requirement:** The system rejects changing a member email to one belonging to a different profile.
