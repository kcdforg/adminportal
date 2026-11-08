# Group Form Dialog

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/groups/group-list/group-form-dialog.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-016-01 — Group Form Dialog: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-form-dialog.component.html`

**Previous screen-level ID:** `UI-ADM-016`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-016-02 — Group Form Dialog: Input controls


**Observed in source:**

- `input (input) — group_name`

- `input (input) — display_name`

- `textarea (textarea) — description`

- `select (select) — category_id`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-form-dialog.component.html`

**Previous screen-level ID:** `UI-ADM-016`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-016-03 — Group Form Dialog: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-form-dialog.component.html`

**Previous screen-level ID:** `UI-ADM-016`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-016-04 — Group Form Dialog: Conditional UI states


**Observed in source:**

- Observed template patterns: error, disabled controls, conditional sections, repeated items, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-form-dialog.component.html`

**Previous screen-level ID:** `UI-ADM-016`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.