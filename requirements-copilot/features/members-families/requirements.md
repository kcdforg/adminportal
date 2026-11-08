# Members and families requirements

**Status:** Provisional as-is reconstruction from source; runtime, tests and intended policy are unverified.

## Requirements

### FAM-001 — Maintain member profiles

- **Actor:** Authorized admin or member with access to the profile.
- **Requirement:** The API shall support listing, creating, reading and updating member profiles through `/api/v1/members`.
- **Acceptance criteria (source-derived):**
  - Creation validates required first/last names and identity field formats/status values.
  - Updates enforce field validation and reject duplicate non-empty email values where checked.
  - Reads and updates enforce `canAccessMember` view/edit rules.
- **Evidence:** `api/routes.php`; `api/members/{index,store,show,update}.php`; identity helpers; `schema.sql` (`member_profiles`).
- **Status:** Backend source observed; app coverage is partial by operation.

### FAM-002 — Maintain families

- **Actor:** Admin or eligible family primary.
- **Requirement:** The API shall support family list/create/read/update operations and preserve family/address data relationships.
- **Acceptance criteria (source-derived):**
  - Creation requires family code and name, validates address fields, and rejects duplicate family codes.
  - Update can change family name/status and address but does not change family code.
  - List/read/edit access follows the observed admin and active-family-member rules.
- **Evidence:** `api/families/*.php`; `services/identity.php` (`canAccessFamily`); `schema.sql` (`families`, `addresses`).
- **Status:** Backend and admin/member read flows observed; runtime unverified.

### FAM-003 — Maintain family membership

- **Actor:** Elevated admin for adding; eligible family primary or admin for supported updates/removal.
- **Requirement:** The API shall add, update and remove family member relationships, including relationship type and member role.
- **Acceptance criteria (source-derived):**
  - Add accepts an existing profile or new profile details and validates relationship/member-role values.
  - Duplicate active membership and more than one active primary are rejected.
  - Removal marks membership removed instead of deleting the relationship row.
  - Membership changes and associated profile/address writes use the observed transaction boundaries.
- **Evidence:** `api/families/members-store.php`, `members-update.php`, `members-destroy.php`; `schema.sql` (`family_members`).
- **Status:** Backend source observed; admin add form uses new-profile fields; client/API behavior was not run.

### FAM-004 — Enforce family/profile access

- **Actor:** Admin, active family member, or member profile owner.
- **Requirement:** Family and profile reads/changes shall be limited by the source-defined admin, family-role and self-access checks.
- **Acceptance criteria (source-derived):**
  - Admins and permitted active family members can read family data.
  - Family edit and member edit actions apply their distinct access helpers.
  - A member's self/profile access is checked separately from access to another family member.
- **Evidence:** `services/identity.php` (`canAccessFamily`, `canAccessMember`); member/family handlers.
- **Status:** Observed implementation rules; intended business policy is unresolved.

### FAM-005 — Manage member login accounts

- **Actor:** Admin roles accepted by member-login handlers.
- **Requirement:** The API shall create, update and deactivate login accounts linked to member profiles.
- **Acceptance criteria (source-derived):**
  - Only the accepted admin roles can invoke these operations.
  - Account type/role and login state are validated against the identity model.
  - Deactivation changes the login's active state.
- **Evidence:** `api/members/login-store.php`, `login-update.php`, `login-destroy.php`; `services/identity.php`; `schema.sql` (`user_logins`).
- **Status:** Backend source observed; no app-side minimum password length was found in the inspected handler.

### FAM-006 — Cross-reference member/entity relations

- **Disposition:** This ID is retained for traceability but is not a separate capability from ENT-002. The canonical member/entity relation behavior, source evidence and acceptance criteria are defined once under ENT-002 in `requirements/features/invitations-entities/requirements.md`.
- **Status:** Cross-reference only; do not count FAM-006 as an additional functional capability or create duplicate implementation/test scope.

## Unresolved policy

Family-member add access differs from primary-member update/remove access. Password requirements vary by entry flow. The source does not establish intended business rules for either difference.
