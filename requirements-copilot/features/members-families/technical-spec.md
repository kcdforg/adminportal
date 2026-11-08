# Members and families — technical specification

**Evidence status:** Static PHP/client/schema/test-source trace; not business approval or runtime verification.

| ID | Current behavior and interfaces | Status / boundary |
|---|---|---|
| FAM-001 | Member list/create/read/update via `api/members/{index,store,show,update}.php`; identity validation/access helpers in `services/identity.php`; profile data in `member_profiles`. Client surfaces include admin `member.service.ts` and member profile/family pages. | **Partial**: list/create permissions are narrower and operation-specific; client integration is partial. |
| FAM-002 | Family list/create/read/update via `api/families/{index,store,show,update}.php`; family/address persistence in `families`, `addresses`; access through `canViewFamily`/`canEditFamily`. Admin family service/pages and member family page consume selected reads. | **Partial**: admin-only list/elevated create differ from primary/member detail and edit access. |
| FAM-003 | Family membership add/update/remove via `api/families/members-{store,update,destroy}.php`; transaction/locking helpers in identity service; `family_members` records. Removal marks state; it is not a physical delete. | **Observed**; admin add form uses new-profile fields. |
| FAM-004 | Separate family and member read/edit checks (`canViewFamily`, `canEditFamily`, `canViewMember`, `canEditMember`, `canAccessFamily`, `canAccessMember`) are applied by handlers. | **Observed implementation**; intended role policy remains unresolved, including readonly-admin treatment. |
| FAM-005 | Create/update/deactivate member login at `api/members/login-{store,update,destroy}.php`; role checks and refresh-token revocation in handlers; `user_logins` and `refresh_tokens` schema. Admin family/member detail forms provide UI. | **Observed**; inspected handlers require non-empty password but do not establish a minimum length. |
| FAM-006 | Traceability cross-reference to ENT-002 in `invitations-entities/technical-spec.md`, which is the canonical member/entity relation capability. | Retained ID, not separate behavior or implementation scope; no bounded Angular/Ionic consumer found. |

## Interfaces and source references

- Routes: `api-backend-php/api/routes.php` (`/members`, `/families`, family members, member logins, entity relations).
- Backend handlers/helpers: `api-backend-php/api/members/*.php`, `api/families/*.php`, `services/identity.php`.
- Data: `api-backend-php/database/schema.sql` (`member_profiles`, `families`, `family_members`, `addresses`, `user_logins`, `entity_member_relations`, `entities`).
- Clients: admin `kcdf-admin-app/src/app/core/services/{member,family}.service.ts` and `features/{members,families}/`; member `kcdf-parents-app/src/app/{family,me}/`.
- OpenAPI path presence and security/request details: `api-backend-php/swagger/openapi.json`; comparison in `docs/architecture/api-contract-audit.md`.

## Unresolved matters

Keep operation-specific actors/access, readonly-admin scope and password entry-flow differences unresolved. FAM-006 is a stable cross-reference to ENT-002 and is not separate implementation scope.
