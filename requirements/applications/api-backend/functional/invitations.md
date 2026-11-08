# Invitations


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-INV-001 — Create invitation

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-001`
**Actor:** Eligible parents and community administrators.

**Requirement:** An eligible user creates an invitation for a recipient.

**Source-supported acceptance checks (static inspection; runtime untested):**

- An authenticated parent or community administrator can submit a valid invitation request.
- An active invitation for the same contact is rejected with `DUPLICATE_INVITATION` (HTTP 409).
- A successful creation stores a pending invitation with an invitation code and sent timestamp.


**Source references:**
- `api-backend-php/api/invitations/index.php` (7: $jwt = checkAuth();), `api-backend-php/api/invitations/store.php` (7: $jwt = checkAuth();).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-INV-002 — List invitations

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-002`
**Actor:** Eligible parents and community administrators.

**Requirement:** An eligible user browses invitations they are permitted to manage.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/invitations/index.php:7`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/invitations/index.php:13`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/invitations/index.php` (7: $jwt = checkAuth();), `api-backend-php/api/invitations/store.php` (7: $jwt = checkAuth();).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-INV-003 — View invitation status

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-003`
**Actor:** Invitation owners and authorized administrators.

**Requirement:** An authorized user inspects the details and current status of an invitation.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/invitations/cancel.php:8`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/invitations/show-by-code.php` (see source), `api-backend-php/api/invitations/cancel.php` (8: $jwt = checkAuth();).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-INV-004 — Cancel invitation

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-004`
**Actor:** Invitation owners and authorized administrators.

**Requirement:** An authorized user cancels an invitation when its state permits cancellation.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/invitations/cancel.php:8`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!preg_match('/^[A-Za-z0-9]{12,50}$/', $code)) {` at `api-backend-php/api/invitations/show-by-code.php:9`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Invitation not found.', 404);` at `api-backend-php/api/invitations/show-by-code.php:10`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('invitations', ['status' => 'expired'], ['id' => $invitation['id'], 'status' => 'pending']);` at `api-backend-php/api/invitations/show-by-code.php:28`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/invitations/show-by-code.php` (see source), `api-backend-php/api/invitations/cancel.php` (8: $jwt = checkAuth();).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-INV-005 — Accept invitation and register account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `INV-005`
**Actor:** Invitation recipients.

**Requirement:** An invitation recipient can submit the registration details needed to accept a valid invitation.

**Source-supported acceptance checks (static inspection; runtime untested):**

- An invitation code not matching the accepted format, or not found in storage, is rejected with HTTP 404.
- Only a pending, unexpired invitation can be accepted; expired invitations are rejected with HTTP 422.
- The acceptance request is validated before account creation.
- An existing login username matching the supplied email is rejected with HTTP 409.
- Successful acceptance creates a member profile and active member login, changes the invitation status to accepted, and returns authentication tokens.


**Source references:**
- `api-backend-php/api/invitations/accept.php` (see source).

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).