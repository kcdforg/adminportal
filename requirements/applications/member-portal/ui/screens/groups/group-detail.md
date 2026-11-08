# Group Detail

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-parents-app/src/app/groups/group-detail.page.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-MEM-004-01 — Group not found: Screen structure


**Observed in source:**

- Declared screen heading(s): `Group not found`.

**Template:** `kcdf-parents-app/src/app/groups/group-detail.page.html`

**Component:** `kcdf-parents-app/src/app/groups/group-detail.page.ts`

**Previous screen-level ID:** `UI-MEM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-MEM-004-02 — Group not found: Input controls


**Observed in source:**

- `select (select) — Role for Member Moderator Admin @if (selectedGroup.my_role === 'owner') { O`

- `input (input)`

- `input (input) — Name, email, or phone`

- `select (select) — candidateId`

- `input (input) — contentTitle`

- `textarea (textarea) — contentBody`

- `textarea (textarea) — pollOptions`

- `input (checkbox) — allowMultiple`

- `input (checkbox) — allowVoteChange`

- `input (checkbox) — showResults`

- `input (datetime-local) — closesAt`

- `input (datetime-local) — eventStart`

- `input (datetime-local) — eventEnd`

- `input (input) — eventLocation`

- `input (number) — eventCapacity`.

**Template:** `kcdf-parents-app/src/app/groups/group-detail.page.html`

**Component:** `kcdf-parents-app/src/app/groups/group-detail.page.ts`

**Previous screen-level ID:** `UI-MEM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-MEM-004-03 — Group not found: User actions


**Observed in source:**

- Declared visible actions: `All groups`; `Join group`; `Add member`; `Add post`; `Add event`; `Add poll`; `Remove`; `Register`; `Cancel`; `View results`; `Close`; `Search`; `Return to groups`.

- Declared event bindings: `joinGroup()`; `setSection($event.detail.value)`; `openMemberModal()`; `openContentModal('posts')`; `openContentModal('events')`; `openContentModal('polls')`; `changeMemberRole(member, $event)`; `removeMember(member)`; `register(event)`; `cancelRegistration(event)`; `togglePollOption(poll, $index, $event)`; `vote(poll)`; `showResults(poll)`; `closeModal()`; `addMember()`; `loadMemberCandidates()`; `saveContent()`.

**Template:** `kcdf-parents-app/src/app/groups/group-detail.page.html`

**Component:** `kcdf-parents-app/src/app/groups/group-detail.page.ts`

**Previous screen-level ID:** `UI-MEM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-MEM-004-04 — Group not found: Navigation


**Observed in source:**

- Declared route bindings: `/groups`.

**Template:** `kcdf-parents-app/src/app/groups/group-detail.page.html`

**Component:** `kcdf-parents-app/src/app/groups/group-detail.page.ts`

**Previous screen-level ID:** `UI-MEM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-MEM-004-05 — Group not found: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, error, empty, disabled controls, conditional sections, repeated items, modal/dialog, navigation.

**Template:** `kcdf-parents-app/src/app/groups/group-detail.page.html`

**Component:** `kcdf-parents-app/src/app/groups/group-detail.page.ts`

**Previous screen-level ID:** `UI-MEM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.