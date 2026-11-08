# Invitations and entities — technical specification

**Evidence status:** Backend PHP/schema and bounded client/test inventory only; no runtime verification or product-policy inference.

| ID | Current behavior and interfaces | Status / boundary |
|---|---|---|
| INV-001 | `GET/POST /api/v1/invitations` → `api/invitations/{index,store}.php`; validates target contact(s), generates code, stores pending invitation/expiry, prevents duplicate pending invitations; inviter/admin-scoped list. `invitations` stores inviter member ID and contacts. | **Partial**: standalone admin can pass community-admin authorization but has no profile ID matching required inviter FK shape. No app consumer was found. |
| INV-002 | `GET /invitations/{code}` and `POST /invitations/{code}/accept` → code lookup and transactional profile/login/token creation. Invitation validation/token helpers in `services/community-notifications.php` and `services/identity.php`. | **Observed**: submitted target contact matching and family-membership creation are not evidenced. |
| INV-003 | `DELETE /invitations/{id}` → `api/invitations/cancel.php`; inviter/admin authorization and pending state are rechecked transactionally before state update. | **Observed**; intended lifecycle policy is unverified. |
| ENT-001 | `GET/POST /entities`, `GET/PUT /entities/{id}` → `api/entities/{index,show,store,update}.php`; authenticated read, active-only filtering for non-admin readers, elevated-admin write checks and identity-field validation. | **Observed backend**, no bounded app consumer found. |
| ENT-002 | Canonical entity relation list/create/end uses member relation handlers and `canManageMemberRelations`; relations are soft-ended by date/current state. | **Observed backend**; no app consumer. FAM-006 is a traceability cross-reference, not a second behavior. |

## Interfaces and data

- Routes: `api-backend-php/api/routes.php`; handler sources `api-backend-php/api/invitations/*.php`, `api/entities/*.php`, and `api/members/entity-relations*.php`.
- Helpers: `api-backend-php/services/community-notifications.php`, `services/identity.php`.
- Schema: `api-backend-php/database/schema.sql` (`invitations`, `entities`, `entity_member_relations`, `member_profiles`, `user_logins`).
- No Angular/Ionic invitation, entity or relation consumer was identified in the bounded review. OpenAPI coverage is itemized in `docs/architecture/api-contract-audit.md`.

## Unresolved matters

Invitation target-contact matching, family linkage, admin inviter identity model, and intended client remain unresolved. FAM-006 is retained as a cross-reference to ENT-002 and is not separate implementation scope. Provisional audit: `baseline-workflow/analysis/requirements-accuracy-audit.md`; do not turn proposals into behavior.
