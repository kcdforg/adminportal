# Notifications

## Requirement summary

This document defines the expected notifications behavior provided by the API.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

### API-NOT-001 — Read own notifications

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-001`
**Actor:** Authenticated users.

**Requirement:** An authenticated user retrieves notifications belonging to their account.

---

### API-NOT-002 — Mark notification as read

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-002`
**Actor:** Authenticated users.

**Requirement:** An authenticated user marks an eligible notification as read.

---

### API-NOT-003 — Archive notification

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-003`
**Actor:** Authenticated users.

**Requirement:** An authenticated user archives an eligible notification.

---

### API-NOT-004 — Send direct notification

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-004`
**Actor:** Authorized notification senders.

**Requirement:** An authorized sender can submit a direct notification through the system send operation.

---

### API-NOT-005 — Broadcast notifications

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-005`
**Actor:** Privileged notification senders.

**Requirement:** A privileged sender may submit a notification broadcast to a selected audience through the system broadcast operation.

---

### API-NOT-006 — Support notification sending from the admin portal

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `NOT-006`
**Actor:** Authorized administrators.

**Requirement:** The system must support direct and broadcast notification requests from the administration portal using a consistent request contract.

**Acceptance criteria:**
- Direct and broadcast requests use a clearly defined set of required fields and recipient rules.
- The portal and service agree on the request shape and the expected success or failure response.
- A send action is not shown as successful unless the system accepts it.

## Open questions

- Confirm supported notification channels, recipient selection, and whether broadcasts require approval.
- Verify that the administration portal can successfully send both direct and broadcast notifications.
