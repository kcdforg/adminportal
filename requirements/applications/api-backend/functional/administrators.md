# Administrators


## Requirement summary

- The system supports the main business flow for this domain.
- Users must be authenticated and authorized before acting on protected data.
- Valid requests should return the expected records or confirmation.
- Invalid input, missing records, and unauthorized access should be rejected cleanly.


## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### API-ADM-001 — List administrator accounts

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ADM-001`
**Actor:** Super administrators.

**Requirement:** A super administrator browses administrator login accounts.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires an authenticated account with `admin_super` permission.
- Filters the listing to `user_type = admin`; supports role, active-status, and username search filters.
- Returns paginated administrator data and pagination metadata.

**Source references:**
- `api-backend-php/api/admins/index.php`, `api-backend-php/api/admins/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ADM-002 — View administrator account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ADM-002`
**Actor:** Super administrators.

**Requirement:** A super administrator opens a selected administrator account to inspect its stored details.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires `admin_super` permission.
- Returns the requested administrator account or a 404 response when absent.

**Source references:**
- `api-backend-php/api/admins/index.php`, `api-backend-php/api/admins/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ADM-003 — Create administrator account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ADM-003`
**Actor:** Super administrators.

**Requirement:** A super administrator can create a new administrator login account with a supported role.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires `admin_super` permission.
- Accepts only `super_admin`, `program_manager`, `accounts`, or `readonly` roles.
- Requires a username of at most 100 characters and a password of at least 12 characters.
- Rejects a duplicate username, a nonexistent linked profile, or a profile already linked to a login.
- Creates an active admin login and records an activity entry inside a database transaction.

**Source references:**
- `api-backend-php/api/admins/store.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-ADM-004 — Update administrator account

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `ADM-004`
**Actor:** Super administrators.

**Requirement:** A super administrator can update the supported details of an existing administrator login account.

**Source-supported acceptance checks (static inspection; not runtime tested):**
- Requires `admin_super` permission and an existing administrator account.
- Validates any supplied role, active/inactive status, and display name.
- Updates supplied supported fields and records an activity entry within a transaction.

**Source references:**
- `api-backend-php/api/admins/update.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).