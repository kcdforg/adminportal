# Member List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-020-01 — Members: Screen structure


**Observed in source:**

- Declared screen heading(s): `Members`.

**Template:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.ts`

**Previous screen-level ID:** `UI-ADM-020`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-020-02 — Members: Input controls


**Observed in source:**

- `input (text) — Type to search..`

- `select (select) — Status All statuses Active Inactive Suspended`.

**Template:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.ts`

**Previous screen-level ID:** `UI-ADM-020`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-020-03 — Members: User actions


**Observed in source:**

- Declared visible actions: `Add Member`; `View details`; `Edit member`.

- Declared event bindings: `openForm()`; `openForm(row)`.

**Template:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.ts`

**Previous screen-level ID:** `UI-ADM-020`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-020-04 — Members: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, empty, conditional sections, repeated items, pagination, navigation.

**Template:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-list/member-list.component.ts`

**Previous screen-level ID:** `UI-ADM-020`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.