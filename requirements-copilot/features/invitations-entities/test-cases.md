# Invitations and entities — test cases

The proposed cases are not executed. Existing test references mean static source assertions only.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| INV-001 | Create mobile-only/email-only invitation; reject invalid/empty targets and pending duplicate; list only inviter/admin scope; characterize standalone-admin inviter FK mismatch without declaring desired policy. | `api-backend-php/tests/phase6-community-notifications.php` covers target validation, duplicate detection, code uniqueness and expiry helpers. | **Not run** |
| INV-002 | Lookup invalid/expired/accepted code; accept eligible invite transactionally; verify created profile/login/tokens; explicitly characterize absence of stored-contact match and family membership. | `phase6-community-notifications.php` covers expiry/pending and transaction helper scenarios, not a complete handler/client flow. | **Not run** |
| INV-003 | Cancel pending invitation as inviter/admin; deny unrelated caller; reject accepted/non-pending state; ensure transactional recheck. | No focused cancel test identified. | **Not run** |
| ENT-001 | List/read active entity as member and all source-visible entities as admin; create/update with valid and invalid fields and role boundaries. | No focused entity test identified. | **Not run** |
| ENT-002 | Canonical entity-relation coverage: list/create/end relation; validate entity, type/date/context and access; confirm relation is ended without physical deletion. | No focused entity-relation test identified. | **Not run** |

No builds, tests, migrations, package commands or runtime requests were run. Test cases do not decide unresolved invitation policy.
