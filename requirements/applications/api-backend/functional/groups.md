# Groups



## Requirement summary

- This document captures the current working behavior in plain language.
- It should be reviewed with business stakeholders before sign-off.
- Runtime validation is still required before any requirement is treated as approved.


### API-GRP-001 — Browse groups

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-001`
**Actor:** Ionic parent-portal members.

**Requirement:** A member browses groups they are allowed to discover or access.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/index.php:7`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `? ($database->select('group_members', 'group_id', ['member_id' => $profileId, 'status' => 'active']) ?? [])` at `api-backend-php/api/groups/index.php:13`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => [], 'meta' => [` at `api-backend-php/api/groups/index.php:19`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/index.php`, `api-backend-php/api/groups/show.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-002 — View group details

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-002`
**Actor:** Ionic parent-portal members.

**Requirement:** A member opens a group record they are authorized to see.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/index.php:7`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `? ($database->select('group_members', 'group_id', ['member_id' => $profileId, 'status' => 'active']) ?? [])` at `api-backend-php/api/groups/index.php:13`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => [], 'meta' => [` at `api-backend-php/api/groups/index.php:19`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/index.php`, `api-backend-php/api/groups/show.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-003 — Create group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-003`
**Actor:** Group managers and administrators.

**Requirement:** An eligible administrator or manager creates a group.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/store.php:7`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!empty($body['category_id'])) {` at `api-backend-php/api/groups/store.php:12`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The selected category does not exist or is inactive.', 422, [` at `api-backend-php/api/groups/store.php:15`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->insert('parent_groups', $values);` at `api-backend-php/api/groups/store.php:41`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/store.php`, `api-backend-php/api/groups/update.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-004 — Update group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-004`
**Actor:** Group managers and administrators.

**Requirement:** An eligible administrator or manager changes the supported details of an existing group.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/store.php:7`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `if (!empty($body['category_id'])) {` at `api-backend-php/api/groups/store.php:12`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('VALIDATION_FAILED', 'The selected category does not exist or is inactive.', 422, [` at `api-backend-php/api/groups/store.php:15`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->insert('parent_groups', $values);` at `api-backend-php/api/groups/store.php:41`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/store.php`, `api-backend-php/api/groups/update.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-005 — Browse group categories

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-005`
**Actor:** Authorized administrators.

**Requirement:** An administrator views the categories used to organize groups.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/categories.php:7`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$categories = $database->select('group_categories', '*', array_merge($where, [` at `api-backend-php/api/groups/categories.php:13`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/categories.php`, `api-backend-php/api/groups/category-store.php`, `api-backend-php/api/groups/category-update.php`, `api-backend-php/api/groups/category-status.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-006 — Manage group categories

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-006`
**Actor:** Authorized administrators.

**Requirement:** An administrator creates or maintains group categories using the supported operations.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/categories.php:7`. Runtime outcome not established.
- **Request validation (source inspection):** Verify the behavior expressed by `'description' => !empty($body['description']) ? $body['description'] : null,` at `api-backend-php/api/groups/category-store.php:16`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('UNAUTHORIZED', 'Only authenticated admins and members can view group categories.', 403);` at `api-backend-php/api/groups/categories.php:9`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$categories = $database->select('group_categories', '*', array_merge($where, [` at `api-backend-php/api/groups/categories.php:13`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/categories.php`, `api-backend-php/api/groups/category-store.php`, `api-backend-php/api/groups/category-update.php`, `api-backend-php/api/groups/category-status.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-007 — Request to join group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-007`
**Actor:** Members and group managers.

**Requirement:** A member requests group membership where the group permits joining.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/members.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/members.php:12`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_members', [` at `api-backend-php/api/groups/add-member.php:46`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/members.php`, `api-backend-php/api/groups/add-member.php`, `api-backend-php/api/groups/join.php`, `api-backend-php/api/groups/leave.php`, `api-backend-php/api/groups/remove-member.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-008 — Leave group

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-008`
**Actor:** Members and group managers.

**Requirement:** An existing member leaves a group where permitted.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/members.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/members.php:12`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_members', [` at `api-backend-php/api/groups/add-member.php:46`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/members.php`, `api-backend-php/api/groups/add-member.php`, `api-backend-php/api/groups/join.php`, `api-backend-php/api/groups/leave.php`, `api-backend-php/api/groups/remove-member.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-009 — Manage group membership

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-009`
**Actor:** Members and group managers.

**Requirement:** An eligible group manager reviews or changes group membership using the supported operations.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/members.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/members.php:12`. Runtime outcome not established.
- **Database read (source inspection):** Verify the behavior expressed by `$rows = $database->select('group_members', ['[>]member_profiles' => ['member_id' => 'id']], [` at `api-backend-php/api/groups/members.php:28`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_members', [` at `api-backend-php/api/groups/add-member.php:46`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/members.php`, `api-backend-php/api/groups/add-member.php`, `api-backend-php/api/groups/join.php`, `api-backend-php/api/groups/leave.php`, `api-backend-php/api/groups/remove-member.php`
- `api-backend-php/api/routes.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-010 — Find eligible group member candidates

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-010`
**Actor:** Group managers.

**Requirement:** An eligible group manager browses candidates for group membership.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/member-candidates.php:8`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse([` at `api-backend-php/api/groups/member-candidates.php:56`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/member-candidates.php`, `api-backend-php/api/groups/member-role.php`
- `api-backend-php/api/routes.php`. ## Unresolved verification - Source existence is not evidence of successful runtime behavior. Confirm actual frontend UX, server configuration, migrations, and automated tests. ## Pass 6 — poll, event and member-client state boundaries **Poll vote contract (observed):** Ionic submits `{option_indexes: number[]}`. PHP accepts that array, deduplicates option indexes, validates the options and single/multiple selection rule, and serializes a vote per member. An existing vote may be changed only if the poll's `allow_vote_change` setting permits it. The backend reche

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).

---

### API-GRP-011 — Change group member role

**Type:** Functional
**Owner:** api-backend
**Legacy ID:** `GRP-011`
**Actor:** Group managers.

**Requirement:** An eligible group manager changes a member’s role subject to group role permissions.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/groups/member-candidates.php:8`. Runtime outcome not established.
- **Explicit failure response (source inspection):** Verify the behavior expressed by `errorResponse('NOT_FOUND', 'Group not found.', 404);` at `api-backend-php/api/groups/member-candidates.php:13`. Runtime outcome not established.
- **Database write (source inspection):** Verify the behavior expressed by `$database->update('group_members', ['member_role' => $role], ['id' => $existing['id']]);` at `api-backend-php/api/groups/member-role.php:41`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse([` at `api-backend-php/api/groups/member-candidates.php:56`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/groups/member-candidates.php`, `api-backend-php/api/groups/member-role.php`
- `api-backend-php/api/routes.php`. ## Unresolved verification - Source existence is not evidence of successful runtime behavior. Confirm actual frontend UX, server configuration, migrations, and automated tests. ## Pass 6 — poll, event and member-client state boundaries **Poll vote contract (observed):** Ionic submits `{option_indexes: number[]}`. PHP accepts that array, deduplicates option indexes, validates the options and single/multiple selection rule, and serializes a vote per member. An existing vote may be changed only if the poll's `allow_vote_change` setting permits it. The backend reche

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).


## Additional source-reconciled atomic requirements (2026-11-08)

The statements below are verified against the cited PHP handler branches only. They do not establish end-to-end operation.

### API-GRP-012 — Restrict direct group joining to parents

**Requirement:** The direct group join endpoint rejects callers who fail isParent.

**Source:** `api-backend-php/api/groups/join.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-013 — Restrict direct joining to active groups

**Requirement:** The direct group join endpoint rejects inactive or archived groups.

**Source:** `api-backend-php/api/groups/join.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-014 — Restrict direct joining to public groups

**Requirement:** The direct group join endpoint rejects groups whose visibility is not public.

**Source:** `api-backend-php/api/groups/join.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-015 — Reject banned group member rejoining

**Requirement:** The direct group join endpoint rejects an existing banned membership.

**Source:** `api-backend-php/api/groups/join.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-016 — Reject duplicate active group membership

**Requirement:** The direct group join endpoint rejects an existing active membership.

**Source:** `api-backend-php/api/groups/join.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-017 — Reactivate eligible former group membership

**Requirement:** The direct group join endpoint reactivates an existing nonactive, nonbanned membership and assigns the member role.

**Source:** `api-backend-php/api/groups/join.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-018 — Validate member ID when adding group member

**Requirement:** The add-member endpoint requires a positive integer member_id.

**Source:** `api-backend-php/api/groups/add-member.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-019 — Validate role when adding group member

**Requirement:** The add-member endpoint accepts owner, admin, moderator or member as member_role.

**Source:** `api-backend-php/api/groups/add-member.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-020 — Authorize assigned group role

**Requirement:** The add-member endpoint checks canAddGroupMember for the requested role.

**Source:** `api-backend-php/api/groups/add-member.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-021 — Require active profile when adding group member

**Requirement:** The add-member endpoint rejects a target without an active member account.

**Source:** `api-backend-php/api/groups/add-member.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-022 — Reject banned target on group add

**Requirement:** The add-member endpoint rejects a banned existing membership.

**Source:** `api-backend-php/api/groups/add-member.php`

**Verification:** Source branch inspected; runtime untested.

### API-GRP-023 — Reactivate eligible group membership on add

**Requirement:** The add-member endpoint reactivates an existing nonactive, nonbanned membership with the requested role.

**Source:** `api-backend-php/api/groups/add-member.php`

**Verification:** Source branch inspected; runtime untested.


## Additional source-verified atomic requirements — 2026-11-08

These describe observed PHP handler behavior, not runtime-tested outcomes. Existing broad IDs remain unchanged.

### API-GRP-033 — Restrict group creation to administrators

**Requirement:** The backend requires group-administrator permission to create a group.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/store.php:9`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-034 — Require active group category

**Requirement:** If a category is supplied for a new group, the backend rejects categories that do not exist or are inactive.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/store.php:14`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-026 — Validate initial group owner

**Requirement:** If an initial owner is supplied, the backend requires an active member profile with an enabled member login.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/store.php:21`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-027 — Default new group visibility to public

**Requirement:** The backend sets a new group to public visibility when visibility is omitted.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/store.php:35`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-028 — Create group as active

**Requirement:** The backend stores newly created groups with active status.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/store.php:36`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-029 — Assign initial group owner

**Requirement:** When an initial owner is supplied, the backend creates an active owner membership for that profile.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/store.php:49`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-030 — Restrict direct group joining to parents

**Requirement:** Only authenticated parent accounts can join groups directly.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/join.php:9`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-031 — Allow direct joining of active public groups

**Requirement:** Direct joining is rejected for inactive groups or groups whose visibility is not public.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/join.php:24`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-032 — Reject joining when banned

**Requirement:** A banned group member cannot join the group directly.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/join.php:29`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-024 — Reject duplicate active membership

**Requirement:** The backend rejects joining a group when the requester already has an active membership.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/join.php:32`.

**Evidence status:** PHP handler inspected; runtime unverified.

### API-GRP-025 — Restore prior group membership

**Requirement:** When an eligible former member joins again, the backend reactivates their existing membership with member role.

**Acceptance check:** Verify that the endpoint enforces the stated rule for matching and nonmatching inputs or actors. This check is derived from static source inspection; it has not been executed.

**Source:** `api-backend-php/api/groups/join.php:37`.

**Evidence status:** PHP handler inspected; runtime unverified.