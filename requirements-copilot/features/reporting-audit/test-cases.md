# Dashboard, reports and audit — test cases

Proposed cases are not executed. Existing test-source references, if any, are named below.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| DASH-001 | Verify dashboard requests minimal pages, reads list metadata totals, handles each list failure, and characterize payment-access behavior for each admin role. Treat notification card and report pages as placeholders/UI gaps unless policy is approved. | No dashboard or report test identified. | **Not run** |
| AUD-001 | Verify logged mutation produces source-defined activity row; list is super-admin-only, newest-first, paginated and honors server filters; compare UI date/actor parameters. Confirm no write route is registered. | No focused activity-log test identified. `tests/phase5-payments.php`/`phase6-community-notifications.php` include activity log fixtures/rollback assertions, but do not establish complete audit-list behavior. | **Not run** |

No report calculation case is asserted because backend route/contract evidence is absent. No tests/builds/migrations/runtime checks were run.
