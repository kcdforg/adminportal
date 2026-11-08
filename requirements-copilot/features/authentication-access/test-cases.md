# Authentication and access — test cases

Test cases below are trace proposals, not executed results. Existing-source references identify static test code only.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| AUTH-001 | Valid active admin login returns admin-scoped access/refresh claims; reject missing credentials, inactive login, wrong account type and unsupported role. | `api-backend-php/tests/phase8-auth-account-types.php` checks admin claim scope, standalone admin token context and route source; `phase1-smoke.php` dispatches auth login routes. | **Not run** |
| AUTH-002 | Valid member credentials issue member-scoped tokens; reject wrong portal, invalid profile link, and characterize inactive/suspended linked-profile behavior without assuming policy. | `tests/phase8-auth-account-types.php` has role-separation assertions; no end-to-end member login case identified. | **Not run** |
| AUTH-003 | Accept valid access bearer; reject absent/malformed/expired/wrong-type or portal-mismatched token; verify a sample protected handler actually invokes `checkAuth()`. | `tests/phase8-auth-account-types.php`; `tests/phase7-parity.php` checks bearer-header extraction and routing source. | **Not run** |
| AUTH-004 | Rotate an eligible stored refresh token; reject expired/revoked/wrong-scope token; characterize two concurrent presentations and affected-row behavior under the currently unresolved single-use policy. | No focused refresh test identified. | **Not run** |
| AUTH-005 | Submit matching stored refresh token, mismatched token, revoked/nonexistent row and verify actual persisted-state result; retain current mismatch as an observed finding pending policy. | No focused logout-row test identified. | **Not run** |
| AUTH-006 | As `admin_super`, list/filter accounts; create valid standalone and profile-linked admin accounts; reject invalid role, short password, bad/duplicate profile link and duplicate username; verify GET detail, supported update fields, authorization failures and activity records. | `tests/phase8-auth-account-types.php` has adjacent account-type/role assertions, not full admin CRUD coverage. | **Not run** |
| AUTH-007 | For admin and member tokens, retrieve current account context; reject missing/invalid token, inactive/mismatched login, changed profile link and missing member profile; verify standalone-admin and member response fields. | No focused `/auth/me` response-contract test identified. | **Not run** |

**No tests, builds, migrations, runtime requests, package installs or formatters were run.** Any test proposal requiring an intended behavior decision is explicitly exploratory until policy is resolved.
