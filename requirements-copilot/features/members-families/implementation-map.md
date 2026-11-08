# Members and families — implementation map

| ID | Backend implementation | Client / schema | Existing test source | Disposition |
|---|---|---|---|---|
| FAM-001 | `api/members/{index,store,show,update}.php`; `services/identity.php::{canAccessMember,validateIdentityFields}` | Admin `core/services/member.service.ts`, `features/members/`; member `me/profile.page.ts`; `member_profiles` | No focused profile CRUD test identified | **Partial**; operation access and client coverage vary |
| FAM-002 | `api/families/{index,store,show,update}.php`; `canViewFamily`, `canEditFamily`; `addresses`, `families` | Admin `core/services/family.service.ts`, `features/families/`; member `family/family.page.ts` | `tests/phase9-family-read.php` covers family/address read helper | **Partial**; no complete list/create/update flow test |
| FAM-003 | `api/families/{members-store,members-update,members-destroy}.php`; transaction/record-lock helpers | Admin `features/families/family-detail/`; `family_members`, `member_profiles`, `addresses` | No focused family-membership transaction test identified | **Observed**, test gap |
| FAM-004 | Identity access helpers in `services/identity.php`; member/family read/write handlers | Admin and member family/profile surfaces; role guard client code | `tests/phase9-family-read.php` covers family data helper, not full permission matrix | **Observed**, policy unresolved |
| FAM-005 | `api/members/{login-store,login-update,login-destroy}.php`; login/token state helpers | Admin family-detail login form/service; `user_logins`, `refresh_tokens` | No focused login-account lifecycle test identified | **Observed**, client/backend integration not executed |
| FAM-006 | Cross-reference only; canonical relation implementation is ENT-002 in `invitations-entities/implementation-map.md` | Same `entities`, `entity_member_relations` data; no Angular/Ionic consumer found in bounded review | Covered once by ENT-002 test proposal | **No independent capability/test scope; ID retained for traceability** |

Route, schema and source evidence is current-code evidence only. Relevant unconnected APIs and unresolved permissions are tracked in `baseline-workflow/analysis/requirements-accuracy-audit.md`.
