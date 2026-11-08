# Invitations and entities — implementation map

| ID | Backend implementation | Client / schema | Existing test source | Disposition |
|---|---|---|---|---|
| INV-001 | `api/invitations/{index,store}.php`; community validation/duplicate/code helpers | No app consumer found; `invitations`, `member_profiles` | `tests/phase6-community-notifications.php` checks invitation validation, duplicate detection, code generation/expiry | **Partial**; standalone admin inviter identity uncertainty |
| INV-002 | `api/invitations/{show-by-code,accept}.php`; community invitation/token helpers; identity token issuance | No app consumer found; `invitations`, `member_profiles`, `user_logins`, `refresh_tokens` | `tests/phase6-community-notifications.php` checks pending/expiry/helper cases and transaction-related behavior | **Observed**, no end-to-end acceptance test identified |
| INV-003 | `api/invitations/cancel.php`; route and transaction/state checks | No app consumer found; `invitations` | No focused cancellation test identified | **Observed**, test coverage gap |
| ENT-001 | `api/entities/{index,show,store,update}.php`; `services/identity.php` validation/admin helpers | No app consumer found; `entities` | No entity CRUD test identified | **Observed backend; client missing/unmapped** |
| ENT-002 | Canonical member entity-relation handlers; `canManageMemberRelations` | No app consumer found; `entity_member_relations` | Canonical entity-relation test proposal; FAM-006 cross-references this row | **Observed backend; client missing/unmapped** |

Test file references denote existing test source, not a test result. Phase 05's invitation-actor and duplicate-ID findings remain open.
