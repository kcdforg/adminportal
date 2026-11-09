# Invitations

## Requirement summary

This document defines the expected invitations behavior provided by the API.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

### API-INV-001 — Create invitation

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-001`
**Actor:** Eligible parents and community administrators.

**Requirement:** An eligible user creates an invitation for a recipient.

---

### API-INV-002 — List invitations

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-002`
**Actor:** Eligible parents and community administrators.

**Requirement:** An eligible user browses invitations they are permitted to manage.

---

### API-INV-003 — View invitation status

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-003`
**Actor:** Invitation owners and authorized administrators.

**Requirement:** An authorized user inspects the details and current status of an invitation.

---

### API-INV-004 — Cancel invitation

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-004`
**Actor:** Invitation owners and authorized administrators.

**Requirement:** An authorized user cancels an invitation when its state permits cancellation.

---

### API-INV-005 — Accept invitation and register account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-005`
**Actor:** Invitation recipients.

**Requirement:** An invitation recipient can submit the registration details needed to accept a valid invitation.
