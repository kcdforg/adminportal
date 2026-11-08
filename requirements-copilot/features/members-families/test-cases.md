# Members and families — test cases

All cases are proposed unless named in the existing-source column; none were executed.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| FAM-001 | Exercise list/create/show/update with valid/invalid identity fields, duplicate non-empty email where checked, and allowed/denied callers per operation. | No focused member-profile CRUD test identified. | **Not run** |
| FAM-002 | Create/list/read/update family and address; cover duplicate code and admin/active-member/primary access boundaries. | `api-backend-php/tests/phase9-family-read.php` asserts family detail includes its address. | **Not run** |
| FAM-003 | Add existing/new profile; reject duplicate active membership/second primary; update role; remove and confirm row is retained as removed; verify transaction rollback. | No focused family-membership test identified. | **Not run** |
| FAM-004 | Verify family view/edit and profile view/edit independently for admin, readonly admin, active primary, ordinary member and self; leave unresolved policy cases as characterization tests. | `phase9-family-read.php` covers family query shape only, not permission matrix. | **Not run** |
| FAM-005 | Create/update/deactivate login; verify role boundaries, uniqueness, refresh-token revocation and password validation currently enforced. | No focused member-login lifecycle test identified. | **Not run** |
| FAM-006 | Cross-reference only; use the canonical ENT-002 entity-relation test cases. Do not duplicate test execution or coverage claims. | Canonical test proposal is in `invitations-entities/test-cases.md`. | **Not run; one shared capability** |

Static inspection only; no tests/builds/migrations/runtime checks were run. Proposals that depend on intended role or password policy do not select policy.
