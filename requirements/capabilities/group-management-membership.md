# Module: Group Management & Membership

> **Module ID:** MOD-008  
> **Previous broad capability:** CAP-008 (retired as a capability ID; see [ID migration](./id-migration.md))

## Purpose

Let the organization run community groups and let parents take part.

## Scope

Groups and categories, discovery, direct join/leave for parents, administrator or manager membership changes, roles, banned-member handling and eligibility rules.

## Atomic business capabilities

The CAP IDs below identify coherent business abilities; detailed requirements remain authoritative in the linked application documents. Classification is documentation-level and is not a claim of complete source-code verification.

### CAP-038 — Group configuration and categories

**Business outcome:** group configuration and categories within the group management & membership module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-EVT-001` | Create group event | Supporting | [group-events.md](../applications/api-backend/functional/group-events.md) | <!-- Mapping requires review -->
| `API-POL-001` | Create group poll | Supporting | [group-polls.md](../applications/api-backend/functional/group-polls.md) | <!-- Mapping requires review -->
| `API-POST-003` | Create group announcement | Supporting | [group-posts.md](../applications/api-backend/functional/group-posts.md) | <!-- Mapping requires review -->
| `API-PST-006` | Reject content creation in inactive groups | Supporting | [group-posts.md](../applications/api-backend/functional/group-posts.md) | <!-- Mapping requires review -->
| `API-PST-013` | Authorize content editing and archiving | Supporting | [group-posts.md](../applications/api-backend/functional/group-posts.md) | <!-- Mapping requires review -->
| `API-GRP-003` | Create group | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-004` | Update group | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-005` | Browse group categories | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-006` | Manage group categories | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-033` | Restrict group creation to administrators | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-034` | Require active group category | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-026` | Validate initial group owner | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-027` | Default new group visibility to public | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-028` | Create group as active | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-029` | Assign initial group owner | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `ADM-GRP-002` | Create and update groups | Primary | [groups.md](../applications/admin-portal/functional/groups.md) | <!-- Mapping requires review -->
| Group Detail (admin) | `UI-ADM-015-01` … `UI-ADM-015-05` (5) | Primary | [group-detail.md](../applications/admin-portal/ui/screens/groups/group-detail.md) | <!-- Mapping requires review -->
| Group Form Dialog (admin) | `UI-ADM-016-01` … `UI-ADM-016-04` (4) | Primary | [group-form-dialog.md](../applications/admin-portal/ui/screens/groups/group-form-dialog.md) |
| Group List (admin) | `UI-ADM-017-01` … `UI-ADM-017-04` (4) | Primary | [group-list.md](../applications/admin-portal/ui/screens/groups/group-list.md) | <!-- Mapping requires review -->

### CAP-039 — Group discovery and joining

**Business outcome:** group discovery and joining within the group management & membership module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-GRP-001` | Browse groups | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-002` | View group details | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-007` | Request to join group | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-008` | Leave group | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-012` | Restrict direct group joining to parents | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-013` | Restrict direct joining to active groups | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-014` | Restrict direct joining to public groups | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-030` | Restrict direct group joining to parents | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-031` | Allow direct joining of active public groups | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `ADM-GRP-001` | Browse and view groups | Primary | [groups.md](../applications/admin-portal/functional/groups.md) |
| `MEM-GRP-001` | Browse groups | Primary | [groups.md](../applications/member-portal/functional/groups.md) |
| `MEM-GRP-002` | View group details | Primary | [groups.md](../applications/member-portal/functional/groups.md) |

### CAP-040 — Membership administration and roles

**Business outcome:** membership administration and roles within the group management & membership module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-EVT-008` | Require active membership to register | Supporting | [group-events.md](../applications/api-backend/functional/group-events.md) |
| `API-POL-007` | Require active membership to vote | Supporting | [group-polls.md](../applications/api-backend/functional/group-polls.md) |
| `API-PST-005` | Restrict content creation to group managers | Supporting | [group-posts.md](../applications/api-backend/functional/group-posts.md) |
| `API-GRP-009` | Manage group membership | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-010` | Find eligible group member candidates | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-011` | Change group member role | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-016` | Reject duplicate active group membership | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-018` | Validate member ID when adding group member | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-019` | Validate role when adding group member | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-020` | Authorize assigned group role | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-021` | Require active profile when adding group member | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `ADM-GRP-003` | Manage group membership and roles | Primary | [groups.md](../applications/admin-portal/functional/groups.md) |
| `MEM-GRP-003` | Manage own group membership | Primary | [groups.md](../applications/member-portal/functional/groups.md) |
| Group Detail (member) | `UI-MEM-004-01` … `UI-MEM-004-05` (5) | Primary | [group-detail.md](../applications/member-portal/ui/screens/groups/group-detail.md) |
| Groups (member) | `UI-MEM-005-01` … `UI-MEM-005-03` (3) | Primary | [groups.md](../applications/member-portal/ui/screens/groups/groups.md) |
| Home (member) | `UI-MEM-006-01` … `UI-MEM-006-04` (4) | Uncertain | [home.md](../applications/member-portal/ui/screens/home/home.md) |

### CAP-041 — Group bans and reactivation

**Business outcome:** group bans and reactivation within the group management & membership module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-GRP-015` | Reject banned group member rejoining | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-017` | Reactivate eligible former group membership | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-022` | Reject banned target on group add | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-023` | Reactivate eligible group membership on add | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-032` | Reject joining when banned | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-024` | Reject duplicate active membership | Primary | [groups.md](../applications/api-backend/functional/groups.md) |
| `API-GRP-025` | Restore prior group membership | Primary | [groups.md](../applications/api-backend/functional/groups.md) |

## Related workflows and UI

### Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-GROUP-CONTENT` | Supporting | [group-content.md](../workflows/group-content.md) |
| `WF-GROUP-MEMBERSHIP` | Primary | [group-membership.md](../workflows/group-membership.md) |



### UI screen coverage

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Group Detail (admin) | `UI-ADM-015-01` … `UI-ADM-015-05` (5) | Primary | [group-detail.md](../applications/admin-portal/ui/screens/groups/group-detail.md) |
| Group Form Dialog (admin) | `UI-ADM-016-01` … `UI-ADM-016-04` (4) | Primary | [group-form-dialog.md](../applications/admin-portal/ui/screens/groups/group-form-dialog.md) |
| Group List (admin) | `UI-ADM-017-01` … `UI-ADM-017-04` (4) | Primary | [group-list.md](../applications/admin-portal/ui/screens/groups/group-list.md) |
| Group Detail (member) | `UI-MEM-004-01` … `UI-MEM-004-05` (5) | Primary | [group-detail.md](../applications/member-portal/ui/screens/groups/group-detail.md) |
| Groups (member) | `UI-MEM-005-01` … `UI-MEM-005-03` (3) | Primary | [groups.md](../applications/member-portal/ui/screens/groups/groups.md) |
| Home (member) | `UI-MEM-006-01` … `UI-MEM-006-04` (4) | Uncertain | [home.md](../applications/member-portal/ui/screens/home/home.md) |



## Technical references



## Non-functional references

The following are historical module-level NFR relationships; see the [NFR index](../non-functional/index.md) for unchanged NFR definitions and approval statuses.



IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-REL-001`, `NFR-REL-003`, `NFR-PRF-001`, `NFR-OBS-002`
- **Proposed (not approved):** `NFR-SEC-011`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).



## Implementation evidence and gaps

Source inspection and implementation-status statements below were carried forward from the previous module-level catalog. They must **not** be interpreted as independently verified for every atomic CAP.

### Prior module-level assessment

**Implemented.** Parent-only joining and banned status handling were source-checked (`API-GRP-012/030`, `-015/-022`); the remaining group requirements were content-reviewed. The member app implements join and leave.



### Prior source evidence

- `api-backend-php/api/groups/` (`join.php:9`, `remove-member.php:13`, `add-member.php`, `member-role.php`), `services/community-notifications.php:70,215`.
- `kcdf-admin-app/src/app/features/groups/`; `kcdf-parents-app/src/app/groups/group-detail.page.ts:274,283`. Database: `group_categories`, `parent_groups`, `group_members`; migrations `20261020_group_categories_roles.sql`, `20261024_group_display_name_optional_owner.sql`. Tests: `phase9-groups-roles.php`.

Individually source-checked requirements for this capability: `API-GRP-012`, `API-GRP-015`, `API-GRP-022`, `API-GRP-030`, `MEM-GRP-003`. All other requirements were content-reviewed for mapping but not individually checked against code.



### Known gaps

- `API-GRP-012/030`, `013/014/031`, `015/032`, `016/024`, `017/025` overlap (existing but duplicated); not merged.
- `API-GRP-033`, `034` appear before `026` in the file; `API-GRP-018..023` define the add-member operation separately from direct joining.
- The member home screen (`UI-MEM-006`) links to groups and posts; its capability is recorded as uncertain.


## Mapping review notes

- Automated classification assigned 8 requirement/screen rows to a provisional CAP because the title did not clearly identify a sub-capability. Review rows marked `Mapping requires review`.
- Shared workflows, UI screens, and NFRs are shown at module level; do not assume every reference applies to every atomic CAP.
