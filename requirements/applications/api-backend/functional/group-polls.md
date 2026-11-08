# Group Polls


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-POL-001 — Create group poll

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-001`
**Actor:** Group managers.

**Requirement:** A permitted group manager creates a poll with options and voting settings.

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

### API-POL-002 — Update or publish group poll

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-002`
**Actor:** Group managers.

**Requirement:** A permitted group manager maintains or publishes a poll subject to supported status and voting rules.

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

### API-POL-003 — Cast or change vote

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-003`
**Actor:** Active group members.

**Requirement:** An active group member votes on a published poll using the allowed option or options.

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

### API-POL-004 — View poll results

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POL-004`
**Actor:** Eligible group participants.

**Requirement:** A permitted participant can request poll results subject to the poll’s visibility rules.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$row['registered_count'] = (int) $database->count('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:51`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse([` at `api-backend-php/api/groups/resources.php:145`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`. ## Unresolved verification - Source existence is not evidence of successful runtime behavior. Confirm actual frontend UX, server configuration, migrations, and automated tests.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

### API-POL-005 — Validate poll option count

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall require between 2 and 10 options when a poll is created or its options are updated.

**Source-observed acceptance check:** Option count behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:468`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-006 — Validate individual poll options

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject poll option labels that are empty or longer than 255 characters.

**Source-observed acceptance check:** Option label behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:177`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-007 — Require active membership to vote

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject poll votes from users who are not active members of the group.

**Source-observed acceptance check:** Membership behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:331`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-008 — Reject voting on closed polls

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject votes on unpublished polls or polls whose closing time has passed.

**Source-observed acceptance check:** Closed poll behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:338`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-009 — Validate selected vote options

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject empty selections, invalid option indexes, and multiple selections when multiple voting is disabled.

**Source-observed acceptance check:** Vote validation behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:343`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-010 — Enforce vote-change setting

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject a replacement vote when the poll does not permit vote changes.

**Source-observed acceptance check:** Vote change behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:390`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-011 — Restrict poll results visibility

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall withhold results from a non-settings-manager who has not voted when early results are disabled and the poll has not closed.

**Source-observed acceptance check:** Result visibility behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:412`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-012 — Prevent editing polls after voting

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject changes to a poll after any vote has been recorded.

**Source-observed acceptance check:** Edit after vote behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:446`  
**Verification:** Source-observed; runtime unverified.

---

### API-POL-013 — Count poll responses by option

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall count selected option indexes and return the total number of voting members.

**Source-observed acceptance check:** Result totals behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:430`  
**Verification:** Source-observed; runtime unverified.

---