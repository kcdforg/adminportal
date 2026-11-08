# Payments and communications — test cases

Each proposed case is unexecuted. Existing-source references are not pass results.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| PAY-001 | List filters/pagination as accounts admin; deny unauthorized list; authorize family detail/list according to helper; compare date filter names to admin request. | `api-backend-php/tests/phase5-payments.php` checks family filtering, page size and family access helpers. | **Not run** |
| PAY-002 | Accept valid cash/non-cash payment and class-fee enrollment from same family; reject invalid amount, method/reference, inactive family and mismatched enrollment; verify transaction/log and admin payload mismatch. | `phase5-payments.php` checks validation, roles, family/enrollment checks and transaction rollback. | **Not run** |
| PAY-003 | Reject completed edit, allow supported pending update, represent refund as separate payment, recalculate enrollment; compare admin update/refund calls. | `phase5-payments.php` covers editability, update values and refund totals. | **Not run** |
| PAY-004 | Recalculate after completed fee/refund and pending payment changes at below/equal/above fee; characterize unused `refunded` enum without assuming intended workflow. | `phase5-payments.php` checks unpaid/partial/paid transitions and pending exclusion. | **Not run** |
| NOTIF-001 | Scope list/read/archive/read-all to caller profile; reject another profile; characterize admin/profile caller behavior without inferring portal policy; inspect UI absence. | `phase6-community-notifications.php` checks own vs other notification lookup. | **Not run** |
| NOTIF-002 | Validate recipient IDs/title/message/type, reject unsupported type/empty recipients, persist one row per valid recipient and activity log; compare UI endpoint/body. | `phase6-community-notifications.php` checks targeted validation and admin helper role. | **Not run** |
| NOTIF-003 | Broadcast to each supported audience, deduplicate active membership recipients, require target ID where relevant, and characterize zero-recipient response without deciding policy. | `phase6-community-notifications.php` checks invalid broadcast target and transaction helpers. | **Not run** |

No tests, builds, migrations, runtime calls, package installs or formatters were run. No external delivery test can be asserted from the inspected source.
