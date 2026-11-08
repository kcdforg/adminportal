# Group Events


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-EVT-001 — Create group event

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-001`
**Actor:** Group managers.

**Requirement:** A group manager with the required permissions creates an event for a group.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!isset($types[$resource])) {` at `api-backend-php/api/groups/resources.php:28`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/resources.php:21`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:305`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-EVT-002 — Update group event

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-002`
**Actor:** Group managers.

**Requirement:** A permitted group manager edits an existing event, subject to backend validations.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!isset($types[$resource])) {` at `api-backend-php/api/groups/resources.php:28`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/resources.php:21`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:305`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-EVT-003 — Register for an event

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-003`
**Actor:** Active group members.

**Requirement:** An eligible group member registers for a published group event when registration is available.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!isset($types[$resource])) {` at `api-backend-php/api/groups/resources.php:28`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/resources.php:21`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:305`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-EVT-004 — View own event registration

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-004`
**Actor:** Registered group members.

**Requirement:** An eligible member checks their own event registration status.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$row['registered_count'] = (int) $database->count('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:51`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse([` at `api-backend-php/api/groups/resources.php:145`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`. ## Unresolved verification - Source existence is not evidence of successful runtime behavior. Confirm actual frontend UX, server configuration, migrations, and automated tests.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-EVT-005 — Cancel own event registration

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `EVT-005`
**Actor:** Registered group members.

**Requirement:** A registered group member requests cancellation of their own event registration. The backend may return success without confirming a row was changed.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!isset($types[$resource])) {` at `api-backend-php/api/groups/resources.php:28`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/resources.php:21`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:305`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`. ## Unresolved verification - Source existence is not evidence of successful runtime behavior. Confirm actual frontend UX, server configuration, migrations, and automated tests.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

### API-EVT-006 — Validate event dates

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall require valid start and end dates and reject an event ending at or before its start.

**Source-observed acceptance check:** Date validation behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:206`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-007 — Validate event capacity

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject an event capacity that is not a positive integer when capacity is supplied.

**Source-observed acceptance check:** Capacity validation behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:213`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-008 — Require active membership to register

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject event registration by a user without active group membership.

**Source-observed acceptance check:** Membership behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:269`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-009 — Reject registration for unavailable events

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject registration for unpublished events or events that have already started.

**Source-observed acceptance check:** Registration window behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:274`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-010 — Prevent duplicate active registrations

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject registration when the same member already has an active registration for that event.

**Source-observed acceptance check:** Duplicate behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:279`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-011 — Enforce event registration capacity

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject registration when the number of active registrations reaches the event capacity.

**Source-observed acceptance check:** Event full behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:301`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-012 — Reactivate cancelled registrations

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reactivate a member’s cancelled registration instead of inserting another record when a prior registration exists.

**Source-observed acceptance check:** Reactivation behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:307`  
**Verification:** Source-observed; runtime unverified.

---

### API-EVT-013 — Cancel only own active registration

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall mark the requesting member’s active event registration as cancelled when cancellation is requested.

**Source-observed acceptance check:** Cancellation behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:322`  
**Verification:** Source-observed; runtime unverified.

---