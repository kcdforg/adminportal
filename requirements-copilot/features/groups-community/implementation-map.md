# Groups and community — implementation map

| ID | Backend implementation | Client / schema | Existing test source | Disposition |
|---|---|---|---|---|
| GRP-001 | `api/groups/{index,show}.php`; `canViewGroup`, group decoration/list helpers | Admin group list/detail; member `groups/groups.page.ts`, `group-detail.page.ts`; `parent_groups`, `group_members` | `tests/phase6-community-notifications.php` covers selected group-access helpers | **Partial**; public inactive/archived status filtering uncertain |
| GRP-002 | `api/groups/{store,update,categories,category-store,category-update,category-status}.php`; community validation/access helpers | Admin `core/services/group.service.ts`, `features/groups/`; `group_categories`, `parent_groups` | `tests/phase9-groups-roles.php` checks group/category validation and role helpers | **Observed** |
| GRP-003 | `api/groups/{members,member-candidates,add-member,join,leave,remove-member,member-role}.php`; membership/candidate/role helpers | Admin group detail/list and member group detail; `group_members` | `phase6-community-notifications.php`, `phase9-groups-roles.php` cover selected access/role helpers, not candidate endpoint end-to-end | **Partial**; client action coverage subset; candidate discovery is API-only in bounded UI review |
| GRP-004 | `api/groups/resources.php`; resource loop routes for posts; `group_contents` | Admin group service and member group detail; sample posts/home are separate | No post-content handler test identified; phase7 route source check excludes dynamic loop expansion | **Observed backend; OpenAPI missing** |
| GRP-005 | `api/groups/resources.php`; poll, vote, results routes; `group_contents`, `group_poll_votes` | Member `groups/group-detail.page.ts` calls poll operations | No poll-content end-to-end test identified | **Observed backend; OpenAPI missing** |
| GRP-006 | `api/groups/resources.php`; event and registration routes; `group_contents`, `group_event_registrations` | Member `groups/group-detail.page.ts` calls event registration operations | No event registration test identified | **Observed backend; OpenAPI missing** |

`tests/phase7-parity.php` scans literal direct `addRoute()` source statements; it does not expand the `foreach` resource loop. Its existence is not evidence the generated operations were checked. No application source was changed.
