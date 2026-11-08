# Group Posts


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-POST-001 — List group announcements

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-001`
**Actor:** Group participants.

**Requirement:** An eligible participant browses announcements visible within their group.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$row['registered_count'] = (int) $database->count('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:51`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse([` at `api-backend-php/api/groups/resources.php:145`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-POST-002 — Read group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-002`
**Actor:** Group participants.

**Requirement:** An eligible participant opens a group announcement they are allowed to view.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/resources.php:8`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$row['registered_count'] = (int) $database->count('group_event_registrations', [` at `api-backend-php/api/groups/resources.php:51`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse([` at `api-backend-php/api/groups/resources.php:145`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/resources.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-POST-003 — Create group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-003`
**Actor:** Group managers.

**Requirement:** A permitted group manager creates an announcement in a group.

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

---

### API-POST-004 — Edit group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-004`
**Actor:** Group managers.

**Requirement:** A permitted group manager changes an existing announcement.

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

---

### API-POST-005 — Archive group announcement

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `POST-005`
**Actor:** Group managers.

**Requirement:** A permitted group manager archives an announcement using the supported backend operation.

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

### API-PST-005 — Restrict content creation to group managers

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall allow group content creation only for a community administrator or a group owner, admin, or moderator.

**Source-observed acceptance check:** Manager authorization behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:153`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-006 — Reject content creation in inactive groups

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject creation of posts, polls, and events when the group is not active.

**Source-observed acceptance check:** Inactive group behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:156`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-007 — Validate content titles

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject a missing, empty, or over-255-character group content title.

**Source-observed acceptance check:** Invalid title behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:107`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-008 — Limit content body length

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject group content bodies longer than 20,000 characters.

**Source-observed acceptance check:** Oversized body behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:162`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-009 — Restrict unpublished content visibility

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall exclude draft and archived group content from non-manager list results.

**Source-observed acceptance check:** Non-manager listing behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:121`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-010 — Paginate group content listings

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall limit requested page size to between 1 and 100 items and return pagination metadata.

**Source-observed acceptance check:** Pagination behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:116`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-011 — Restrict status changes to settings managers

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall reject a group content status update unless the actor has group settings-management permission.

**Source-observed acceptance check:** Status change behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:459`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-012 — Archive content instead of deleting

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall mark group content as archived when an authorized actor requests deletion.

**Source-observed acceptance check:** Archive behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:560`  
**Verification:** Source-observed; runtime unverified.

---

### API-PST-013 — Authorize content editing and archiving

**Type:** Functional  
**Owner:** API Backend  
**Requirement:** The API shall permit content editing and archiving only to a group manager or its eligible creator.

**Source-observed acceptance check:** Edit permission behavior is enforced by the referenced PHP handler branch. This is source inspection, not a runtime test.

**Source:** `api-backend-php/api/groups/resources.php:442`  
**Verification:** Source-observed; runtime unverified.

---