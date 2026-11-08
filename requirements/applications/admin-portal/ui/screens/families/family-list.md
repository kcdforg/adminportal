# Family List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-014-01 — Families: Screen structure


**Observed in source:**

- Declared screen heading(s): `Families`.

**Template:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.ts`

**Previous screen-level ID:** `UI-ADM-014`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-014-02 — Families: Input controls


**Observed in source:**

- `input (text) — Search families..`

- `select (select) — Status All statuses Active Inactive Suspended`.

**Template:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.ts`

**Previous screen-level ID:** `UI-ADM-014`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-014-03 — Families: User actions


**Observed in source:**

- Declared visible actions: `Add Family`; `View family`; `Edit family`.

- Declared event bindings: `openForm()`; `openForm(f); $event.stopPropagation()`.

**Template:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.ts`

**Previous screen-level ID:** `UI-ADM-014`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-014-04 — Families: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, empty, conditional sections, repeated items, pagination, navigation.

**Template:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-list/family-list.component.ts`

**Previous screen-level ID:** `UI-ADM-014`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.