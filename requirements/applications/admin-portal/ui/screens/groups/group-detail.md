# Group Detail

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-015-01 — Back: Screen structure


**Observed in source:**

- Declared screen heading(s): `Back`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-015`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-015-02 — Back: Input controls


**Observed in source:**

- `select (select) — Member Moderator Admin Owner`

- `input (input) — groupName`

- `input (input) — displayName`

- `select (select) — categoryId`

- `select (select) — visibility`

- `textarea (textarea) — description`

- `select (select) — status`

- `input (input) — memberCandidateSearch`

- `select (select) — newMemberId`

- `select (select) — newMemberRole`

- `input (input) — title`

- `textarea (textarea) — content`

- `textarea (textarea) — options`

- `input (checkbox) — allowMultiple`

- `input (checkbox) — allowVoteChange`

- `input (checkbox) — showResults`

- `input (datetime-local) — closesAt`

- `input (datetime-local) — startAt`

- `input (datetime-local) — endAt`

- `input (input) — location`

- `input (number) — capacity`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-015`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-015-03 — Back: User actions


**Observed in source:**

- Declared visible actions: `Back`; `Edit group details`; `Members ()`; `Posts`; `Events`; `Polls`; `Add member`; `Add post`; `Add event`; `Add poll`; `Ban member`; `Remove member`; `Edit`; `Archive`; `Close`; `Cancel`; `Save details`; `Search`.

- Declared event bindings: `openSettingsDialog()`; `selectTab('members')`; `selectTab('posts')`; `selectTab('events')`; `selectTab('polls')`; `openMemberDialog()`; `openContentDialog('posts')`; `openContentDialog('events')`; `openContentDialog('polls')`; `changeMemberRole(member, $event)`; `banMember(member)`; `removeMember(member)`; `editContent(item)`; `archiveContent(item)`; `closeDialog()`; `$event.stopPropagation()`; `saveSettings()`; `addMember()`; `searchMemberCandidates()`; `newMemberId = +$any($event.target).value`; `newMemberRole = $any($event.target).value`; `createContent()`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-015`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-015-04 — Back: Navigation


**Observed in source:**

- Declared route bindings: `/groups`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-015`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-015-05 — Back: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, error, empty, disabled controls, conditional sections, repeated items, modal/dialog, navigation.

**Template:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-detail/group-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-015`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.