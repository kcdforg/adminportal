# Module: Family & Member Management

> **Module ID:** MOD-002  
> **Previous broad capability:** CAP-002 (retired as a capability ID; see [ID migration](./id-migration.md))

## Purpose

Maintain the household and person records that every other capability depends on.

## Scope

Family records and family-member relationships (relationship type, role, primary member, soft removal), member profiles and lifecycle state, member entity relations, trainer-to-profile linkage as a supporting need, and member self-service views of profile and family.

## Atomic business capabilities

The CAP IDs below identify coherent business abilities; detailed requirements remain authoritative in the linked application documents. Classification is documentation-level and is not a claim of complete source-code verification.

### CAP-018 — Family records and relationships

**Business outcome:** family records and relationships within the family & member management module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-ENR-005` | Authorize enrollment creation | Supporting | [enrollments.md](../applications/api-backend/functional/enrollments.md) | <!-- Mapping requires review -->
| `API-ENR-006` | Verify family membership | Supporting | [enrollments.md](../applications/api-backend/functional/enrollments.md) |
| `API-ENT-001` | Browse entities | Primary | [entities.md](../applications/api-backend/functional/entities.md) | <!-- Mapping requires review -->
| `API-FAM-001` | Browse families as administrator | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-002` | View family details | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-003` | Create family | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-004` | Update family | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-005` | Add family member association | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-006` | Update family member association | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-019` | Restrict family creation to elevated administrators | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-007` | Reject duplicate family codes | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-008` | Create family with active status | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-009` | Create optional family address | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-010` | Restrict family membership addition | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-011` | Validate family relationship type | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-012` | Validate family member role | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-013` | Reject duplicate active family membership | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-014` | Limit active primary family member | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-015` | Reactivate removed family membership | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-016` | Restrict family member updates | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-017` | Prevent duplicate member email on family update | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-FAM-018` | Soft-remove family membership | Primary | [families.md](../applications/api-backend/functional/families.md) |
| `API-GRP-021` | Require active profile when adding group member | Supporting | [groups.md](../applications/api-backend/functional/groups.md) | <!-- Mapping requires review -->
| `API-GRP-026` | Validate initial group owner | Supporting | [groups.md](../applications/api-backend/functional/groups.md) | <!-- Mapping requires review -->
| `API-INV-005` | Accept invitation and register account | Supporting | [invitations.md](../applications/api-backend/functional/invitations.md) | <!-- Mapping requires review -->
| `API-MEM-005` | Create member login | Supporting | [members.md](../applications/api-backend/functional/members.md) | <!-- Mapping requires review -->
| `API-MEM-006` | Maintain member login | Supporting | [members.md](../applications/api-backend/functional/members.md) | <!-- Mapping requires review -->
| `API-MEM-011` | Reject duplicate member email on creation | Primary | [members.md](../applications/api-backend/functional/members.md) | <!-- Mapping requires review -->
| `API-MEM-010` | Reject duplicate member email on update | Primary | [members.md](../applications/api-backend/functional/members.md) | <!-- Mapping requires review -->
| `API-TRN-003` | Create trainer record | Supporting | [trainers.md](../applications/api-backend/functional/trainers.md) | <!-- Mapping requires review -->
| `ADM-DSH-001` | View administration dashboard | Supporting | [dashboard.md](../applications/admin-portal/functional/dashboard.md) | <!-- Mapping requires review -->
| `ADM-FAM-001` | View family list | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-002` | Search and filter families | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-003` | Create a family record | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-004` | Edit a family record | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-005` | View family detail | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-006` | Add a family member | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-007` | Edit a family member relationship | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-008` | Remove a family member | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-009` | Manage member login access for family members | Supporting | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-FAM-010` | Validate family and membership data | Primary | [families.md](../applications/admin-portal/functional/families.md) |
| `ADM-MBR-002` | Search and filter members | Primary | [members.md](../applications/admin-portal/functional/members.md) | <!-- Mapping requires review -->
| `ADM-MBR-006` | Validate member input | Primary | [members.md](../applications/admin-portal/functional/members.md) | <!-- Mapping requires review -->
| `ADM-MBR-007` | Manage member lifecycle state | Primary | [members.md](../applications/admin-portal/functional/members.md) | <!-- Mapping requires review -->
| `MEM-FAM-001` | View family information | Primary | [family.md](../applications/member-portal/functional/family.md) |
| `MEM-FAM-002` | Request a family information change | Primary | [family.md](../applications/member-portal/functional/family.md) |
| `MEM-PRO-002` | Update profile information | Primary | [profile.md](../applications/member-portal/functional/profile.md) | <!-- Mapping requires review -->
| Family Detail (admin) | `UI-ADM-010-01` … `UI-ADM-010-04` (4) | Primary | [family-detail.md](../applications/admin-portal/ui/screens/families/family-detail.md) |
| Family Form (admin) | `UI-ADM-013-01` … `UI-ADM-013-04` (4) | Primary | [family-form.md](../applications/admin-portal/ui/screens/families/family-form.md) |
| Family List (admin) | `UI-ADM-014-01` … `UI-ADM-014-04` (4) | Primary | [family-list.md](../applications/admin-portal/ui/screens/families/family-list.md) |
| Family Member Form (admin) | `UI-ADM-011-01` … `UI-ADM-011-04` (4) | Primary | [family-member-form.md](../applications/admin-portal/ui/screens/families/family-member-form.md) |
| Member Login Form (admin) | `UI-ADM-012-01` … `UI-ADM-012-04` (4) | Supporting | [member-login-form.md](../applications/admin-portal/ui/screens/families/member-login-form.md) | <!-- Mapping requires review -->
| Member Form (admin) | `UI-ADM-019-01` … `UI-ADM-019-04` (4) | Primary | [member-form.md](../applications/admin-portal/ui/screens/members/member-form.md) | <!-- Mapping requires review -->
| Family (member) | `UI-MEM-003-01` … `UI-MEM-003-04` (4) | Primary | [family.md](../applications/member-portal/ui/screens/family/family.md) |

### CAP-019 — Member profiles and details

**Business outcome:** member profiles and details within the family & member management module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-MEM-001` | Browse member profiles | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-002` | View member profile | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-003` | Create member profile | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-004` | Update member profile | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-012` | Restrict member profile creation | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-008` | Create member profiles as active | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-009` | Restrict member profile updates | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `ADM-MBR-001` | View member list | Primary | [members.md](../applications/admin-portal/functional/members.md) |
| `ADM-MBR-003` | Open member detail | Primary | [members.md](../applications/admin-portal/functional/members.md) |
| `ADM-MBR-004` | Create a member record | Primary | [members.md](../applications/admin-portal/functional/members.md) |
| `ADM-MBR-005` | Edit a member record | Primary | [members.md](../applications/admin-portal/functional/members.md) |
| Member Detail (admin) | `UI-ADM-018-01` … `UI-ADM-018-04` (4) | Primary | [member-detail.md](../applications/admin-portal/ui/screens/members/member-detail.md) |
| Member List (admin) | `UI-ADM-020-01` … `UI-ADM-020-04` (4) | Primary | [member-list.md](../applications/admin-portal/ui/screens/members/member-list.md) |

### CAP-020 — Member self-service

**Business outcome:** member self-service within the family & member management module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `MEM-PRO-001` | View own profile | Primary | [profile.md](../applications/member-portal/functional/profile.md) |
| Profile (member) | `UI-MEM-008-01` … `UI-MEM-008-04` (4) | Primary | [profile.md](../applications/member-portal/ui/screens/me/profile.md) |

### CAP-021 — Organization and entity records

**Business outcome:** organization and entity records within the family & member management module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-ENT-002` | View entity details | Primary | [entities.md](../applications/api-backend/functional/entities.md) |
| `API-ENT-003` | Create entity | Primary | [entities.md](../applications/api-backend/functional/entities.md) |
| `API-ENT-004` | Update entity | Primary | [entities.md](../applications/api-backend/functional/entities.md) |
| `API-MEM-007` | Manage member entity relations | Primary | [members.md](../applications/api-backend/functional/members.md) |

## Related workflows and UI

### Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-FAMILY-REGISTRATION` | Primary | [family-registration.md](../workflows/family-registration.md) |
| `WF-INVITATIONS` | Supporting | [invitations.md](../workflows/invitations.md) |



### UI screen coverage

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Family Detail (admin) | `UI-ADM-010-01` … `UI-ADM-010-04` (4) | Primary | [family-detail.md](../applications/admin-portal/ui/screens/families/family-detail.md) |
| Family Form (admin) | `UI-ADM-013-01` … `UI-ADM-013-04` (4) | Primary | [family-form.md](../applications/admin-portal/ui/screens/families/family-form.md) |
| Family List (admin) | `UI-ADM-014-01` … `UI-ADM-014-04` (4) | Primary | [family-list.md](../applications/admin-portal/ui/screens/families/family-list.md) |
| Family Member Form (admin) | `UI-ADM-011-01` … `UI-ADM-011-04` (4) | Primary | [family-member-form.md](../applications/admin-portal/ui/screens/families/family-member-form.md) |
| Member Login Form (admin) | `UI-ADM-012-01` … `UI-ADM-012-04` (4) | Supporting | [member-login-form.md](../applications/admin-portal/ui/screens/families/member-login-form.md) |
| Member Detail (admin) | `UI-ADM-018-01` … `UI-ADM-018-04` (4) | Primary | [member-detail.md](../applications/admin-portal/ui/screens/members/member-detail.md) |
| Member Form (admin) | `UI-ADM-019-01` … `UI-ADM-019-04` (4) | Primary | [member-form.md](../applications/admin-portal/ui/screens/members/member-form.md) |
| Member List (admin) | `UI-ADM-020-01` … `UI-ADM-020-04` (4) | Primary | [member-list.md](../applications/admin-portal/ui/screens/members/member-list.md) |
| Family (member) | `UI-MEM-003-01` … `UI-MEM-003-04` (4) | Primary | [family.md](../applications/member-portal/ui/screens/family/family.md) |
| Profile (member) | `UI-MEM-008-01` … `UI-MEM-008-04` (4) | Primary | [profile.md](../applications/member-portal/ui/screens/me/profile.md) |



## Technical references



## Non-functional references

The following are historical module-level NFR relationships; see the [NFR index](../non-functional/index.md) for unchanged NFR definitions and approval statuses.



IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-003`, `NFR-SEC-004`, `NFR-REL-001`, `NFR-REL-003`
- **Proposed (not approved):** `NFR-PRV-001`, `NFR-SEC-011`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).



## Implementation evidence and gaps

Source inspection and implementation-status statements below were carried forward from the previous module-level catalog. They must **not** be interpreted as independently verified for every atomic CAP.

### Prior module-level assessment

**Partially implemented.** Family, member and trainer-profile operations exist in the API and admin portal; member app shows profile and family. Entities and entity relations exist only in the API. `MEM-FAM-002` (how to request a correction) was not individually verified. Only `API-FAM-012` and `API-FAM-014` were source-checked among the family rules.



### Prior source evidence

- `api-backend-php/api/families/` (`store.php`, `members-store.php:30,67`, `members-destroy.php`), `api/members/`, `api/entities/`, `services/identity.php:66` (transaction).
- Routes: `/families`, `/families/{id}/members`, `/members`, `/members/{id}/entity-relations`, `/entities` in `api/routes.php`.
- `kcdf-admin-app/src/app/features/families/`, `features/members/`; `kcdf-parents-app/src/app/me/`, `family/` and `core/member-auth.service.ts:282` (family members call).
- Database: `member_profiles`, `addresses`, `families`, `family_members`, `entities`, `entity_member_relations`; migration `20261018_add_taluk_village_to_addresses.sql`. Tests: `phase9-family-read.php`.

Individually source-checked requirements for this capability: `ADM-FAM-009`, `API-ENR-006`, `API-FAM-012`, `API-FAM-014`, `API-MEM-005`. All other requirements were content-reviewed for mapping but not individually checked against code.



### Known gaps

- Entity and entity-relation endpoints (`API-ENT-*`, `API-MEM-007`) have no admin or member screens or UI requirements.
- `API-FAM-019` and `API-MEM-012`/`011`/`008`/`009`/`010` appear out of numeric order in their files.
- `ADM-FAM-009` and `API-MEM-005/006` (member logins) are mapped to CAP-001 as primary; the boundary is recorded for review.
- Personal data of minors has no policy requirement (proposed `NFR-PRV-001`).


## Mapping review notes

- Automated classification assigned 17 requirement/screen rows to a provisional CAP because the title did not clearly identify a sub-capability. Review rows marked `Mapping requires review`.
- Shared workflows, UI screens, and NFRs are shown at module level; do not assume every reference applies to every atomic CAP.
