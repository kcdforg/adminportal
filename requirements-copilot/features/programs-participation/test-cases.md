# Programs and participation — test cases

Cases are proposed only; none were run. Existing references describe source content, not test results.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| PROG-001 | Validate program list filters/pagination and create/update/status permissions/fields; compare admin update verb and payload to registration/handler. | `api-backend-php/tests/phase1-smoke.php` checks one program route dispatch; `phase7-parity.php` checks fixed handler routing. No program validation test identified. | **Not run** |
| PROG-002 | Create/read/update trainer with admin vs assigned trainer, allowed field limits, invalid profile/address, and client verb comparison. | No focused trainer test identified. | **Not run** |
| PROG-003 | Verify batch create/update validation and admin/trainer/enrollment visibility; verify only registered member route works; test list metadata and actual UI request. | `phase1-smoke.php`/`phase7-parity.php` cover generic route registration, not batch authorization or add-member UI flow. | **Not run** |
| PROG-004 | Enroll eligible family member in upcoming/active, available batch; reject capacity, duplicate, inaccessible family and invalid status; check transactional fee snapshot/unpaid creation and UI payload. | No focused enrollment test identified. | **Not run** |
| PROG-005 | Cancel eligible enrollment; reject unauthorized/already-cancelled; verify membership state and transaction; characterize re-enrollment under unique keys without deciding intent. | No focused cancellation test identified. | **Not run** |
| PROG-006 | Create/update session, enforce trainer/time checks, lock using dedicated endpoint, reject writes after lock; compare client calls. | No focused session/lock test identified. | **Not run** |
| PROG-007 | Read/write attendance records; reject invalid member/status and locked session; verify individual patch and server/client payload shape. | No attendance-specific test identified; `phase7-parity.php` is route plumbing only. | **Not run** |

Source-only inventory. No tests, builds, migrations, runtime requests, installs or formatters were run. No status or re-enrollment policy is chosen.
