# Groups and community — test cases

Proposed cases have not been executed. Existing test references are static source only.

| ID | Proposed case | Existing test source observed | Execution |
|---|---|---|---|
| GRP-001 | List/detail as platform admin, public member/nonmember and private/invite-only member; characterize archived/inactive public listing without assuming intended policy. | `api-backend-php/tests/phase6-community-notifications.php` tests public/private/member/banned access helpers. | **Not run** |
| GRP-002 | Create category/group; reject duplicate category, invalid status/category/owner; verify community-admin create and owner/admin update permissions. | `tests/phase9-groups-roles.php` checks group/category validation and role rules. | **Not run** |
| GRP-003 | List members and authorized candidates; verify candidate eligibility/search and deny unauthorized callers; add/join/leave/remove/change role across eligible groups; reject banned/duplicate membership and protected owner transitions; confirm client surfaces only invoke registered methods. | `phase6-community-notifications.php` and `phase9-groups-roles.php` cover selected helper rules, not complete endpoint/candidate workflows. | **Not run** |
| GRP-004 | Create/list/read/update/archive posts resource with manager vs ordinary member; verify published visibility and route operations absent from OpenAPI. | No content-handler test identified. | **Not run** |
| GRP-005 | Validate poll options/close state; reject unauthorized/closed vote; enforce multiple/change-vote policy; check conditional results and OpenAPI gap. | No poll-content test identified. | **Not run** |
| GRP-006 | Validate event dates/capacity; register, reject duplicate/full, retrieve/cancel own registration only; check transactional capacity and OpenAPI gap. | No event-registration test identified. | **Not run** |

No tests, builds, migrations, package commands, formatters or runtime calls were run. Cases do not settle group visibility policy or approve contract changes.
