# Invitations and entities requirements

**Status:** Provisional as-is reconstruction. Backend routes are source-observed; client use is not established.

## Invitation requirements

### INV-001 — Create and list invitations

- **Actor:** Eligible parent role or community admin.
- **Requirement:** The API shall create invitations addressed to at least one mobile number or email and list invitations only to the inviter or community admin.
- **Acceptance criteria (source-derived):**
  - At least one target contact is required; supplied contacts are format-validated.
  - The handler generates a unique code and creates a pending invitation with its source-defined expiry.
  - Duplicate active pending invitations for the same inviter/target are rejected.
- **Evidence:** `api/invitations/store.php`, `index.php`; `services/community-notifications.php`; `schema.sql` (`invitations`).
- **Status:** Backend source observed; no app invitation UI/service was found.

### INV-002 — Read and accept invitation code

- **Actor:** Invitation recipient.
- **Requirement:** The code-based API shall return invitation status/expiry and accept an eligible pending invitation by creating a member profile and login and issuing member tokens.
- **Acceptance criteria (source-derived):**
  - Invalid, expired, already accepted or otherwise ineligible codes are rejected.
  - Acceptance validates required identity/contact fields and password, checks username uniqueness, and records acceptance transactionally.
  - Successful acceptance returns member authentication tokens.
- **Evidence:** `api/invitations/show-by-code.php`, `accept.php`; `services/identity.php`; `schema.sql` (`invitations`, `member_profiles`, `user_logins`).
- **Status:** Backend source observed. Submitted contacts are not visibly matched against stored target contacts; acceptance does not visibly create a family membership.

### INV-003 — Cancel pending invitation

- **Actor:** Inviter or community admin.
- **Requirement:** The API shall cancel an invitation only while it is pending and caller authorization permits cancellation.
- **Acceptance criteria (source-derived):**
  - A caller who is neither the inviter nor community admin is denied.
  - A non-pending invitation cannot be cancelled.
  - State is rechecked in the handler's transaction.
- **Evidence:** `api/invitations/cancel.php`; `api/routes.php`; invitation permission helpers.
- **Status:** Backend source observed; intended invitation lifecycle policy is unverified.

## Entity requirements

### ENT-001 — Maintain entity records

- **Actor:** Authenticated caller for active reads; elevated admin for writes.
- **Requirement:** The API shall list/read active entities and allow an elevated admin to create or update entity records.
- **Acceptance criteria (source-derived):**
  - Non-admin reads omit inactive entities.
  - Create/update validate entity fields and require elevated-admin authorization.
- **Evidence:** `api/entities/{index,show,store,update}.php`; `services/identity.php`; `schema.sql` (`entities`).
- **Status:** Backend source observed; no app consumer was found in the bounded review.

### ENT-002 — Maintain member/entity relations

- **Actor:** Member permitted by `canManageMemberRelations`.
- **Requirement:** The API shall associate a member with an entity using a supported relation type and allow the relationship to be ended.
- **Acceptance criteria (source-derived):**
  - Relations can be listed for an accessible member profile.
  - The operation validates entity, relation type, dates and optional context.
  - Ending a relation records its end date and non-current state.
- **Evidence:** `api/members/entity-relations.php`, `entity-relation-store.php`, `entity-relation-delete.php`; `services/identity.php`; `schema.sql` (`entity_member_relations`).
- **Status:** Backend source observed; no Angular/Ionic consumer was found in the bounded review.
- **Cross-reference:** FAM-006 is retained as a traceability pointer to this same capability; it does not define a second route, behavior or test scope.

## Unresolved policy

The invitation target contact is not visibly required to match the contact submitted on acceptance, and no family-link operation is visible. No target matching rule, family-join behavior, or intended client is asserted here.
