# Groups and community requirements

**Status:** Provisional as-is reconstruction. Backend routes and selected client paths are source-observed; runtime behavior is unverified.

## Requirements

### GRP-001 — Discover and read groups

- **Actor:** Community admin or eligible parent/member account.
- **Requirement:** The API shall list and read groups according to source-defined visibility, membership, status and role rules.
- **Acceptance criteria (source-derived):**
  - Community admins may list groups; member listing includes public groups and active memberships, with supported filters.
  - Group detail requires platform-admin access or eligible public/active-membership access.
  - Listing and detail use the source-defined pagination/response shape.
- **Evidence:** `api/groups/index.php`, `show.php`; `services/community-notifications.php`; `schema.sql` (`parent_groups`).
- **Status:** Backend and member group list/detail source paths observed. Inactive/archived public-group listing policy is unresolved.

### GRP-002 — Maintain group categories and settings

- **Actor:** Community admin for category creation/status and group creation; eligible group owner/admin for supported group updates.
- **Requirement:** The API shall create/update categories and create/update groups with validated names, visibility, status and category/owner associations.
- **Acceptance criteria (source-derived):**
  - Category names and statuses are validated; duplicate category names return a conflict.
  - Group creation validates fields and active category/owner references.
  - Group update applies the source-defined community-admin or group-owner/admin permission.
- **Evidence:** `api/groups/{store,update,categories,category-store,category-update,category-status}.php`; `services/community-notifications.php`; schema (`group_categories`, `parent_groups`).
- **Status:** Backend and admin source observed.

### GRP-003 — Manage group membership

- **Actor:** Platform/community admin, group owner/admin, or eligible member.
- **Requirement:** The API shall list/manage members, permit eligible public-group join and expose leave/remove/role operations under source-defined permissions.
- **Acceptance criteria (source-derived):**
  - Add/join validates member eligibility and group state; duplicate active members and banned members are rejected.
  - Role changes preserve owner constraints and supported role values.
  - Leave/remove updates membership state according to the handler rather than duplicating active membership.
  - Candidate discovery returns profiles with active member logins and active primary/normal family membership only to platform admins or callers authorized to add members to the specified group; search filters apply to name, email and mobile.
- **Evidence:** `api/groups/{members,add-member,join,leave,remove-member,member-role}.php`; community helpers; `schema.sql` (`group_members`).
- **Additional operation evidence:** `GET /api/v1/groups/member-candidates` (`api/groups/member-candidates.php`).
- **Status:** Backend source observed; the member UI does not expose all registered membership actions.

### GRP-004 — Publish group announcements

- **Actor:** Group owner/admin/moderator or community admin.
- **Requirement:** An authorized content manager shall create/read/update/archive group announcement content.
- **Acceptance criteria (source-derived):**
  - The API maps the `posts` resource to `content_type='announcement'` in `group_contents`.
  - Creation validates title/content and requires an active group.
  - Non-managers see published content; authorized updates/archive apply creator/manager rules.
- **Evidence:** `api/groups/resources.php`; `api/routes.php`; schema (`group_contents`).
- **Status:** Backend source and group-detail client flow observed. Standalone member posts/home use samples and are not this requirement.

### GRP-005 — Poll voting and results

- **Actor:** Active group member; group settings manager for privileged results access.
- **Requirement:** The API shall create/read polls, validate voting rules, record votes and return results only when source-defined visibility conditions permit.
- **Acceptance criteria (source-derived):**
  - Poll options and close time are validated.
  - Vote requires active membership, an active group and an open/published poll.
  - Multiple/change-vote settings are enforced; results follow configured visibility/prior-vote/closed conditions.
- **Evidence:** `api/groups/resources.php`; schema (`group_contents`, `group_poll_votes`).
- **Status:** Backend and member group-detail vote/results methods observed; no corresponding OpenAPI paths found in the checked document.

### GRP-006 — Event registration

- **Actor:** Active group member.
- **Requirement:** The API shall allow eligible members to register for and cancel their own registration for a published future group event.
- **Acceptance criteria (source-derived):**
  - Event dates and optional capacity are validated.
  - Registration rejects duplicates/full events and rechecks capacity transactionally.
  - A member can read/cancel only their own registration.
- **Evidence:** `api/groups/resources.php`; schema (`group_contents`, `group_event_registrations`).
- **Status:** Backend and member group-detail flow observed; no matching OpenAPI paths found in the checked document.

## Not converted into requirements

Standalone member posts and home feed use `SAMPLE_POSTS`; no source evidence connects them to group announcements. No product requirement is inferred to replace this sample. The checked OpenAPI and test inventory do not establish group-content endpoint coverage.
