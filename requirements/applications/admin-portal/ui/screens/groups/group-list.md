# Group List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-017-01 — Groups: Screen structure


**Observed in source:**

- Declared screen heading(s): `Groups`; `Group categories`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.ts`

**Previous screen-level ID:** `UI-ADM-017`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-017-02 — Groups: Input controls


**Observed in source:**

- `input (input) — Category name`

- `input (input) — Description`

- `input (input) — Edit category name`

- `input (input) — Edit category description`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.ts`

**Previous screen-level ID:** `UI-ADM-017`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-017-03 — Groups: User actions


**Observed in source:**

- Declared visible actions: `Manage Categories`; `Create Group`; `Save`; `Cancel`; `Edit`.

- Declared event bindings: `categoriesOpen.set(!categoriesOpen())`; `openForm()`; `saveCategory()`; `saveCategory(category)`; `editingCategoryId.set(null)`; `editCategory(category)`; `toggleCategoryStatus(category)`; `$event.stopPropagation()`; `openForm(g); $event.stopPropagation()`.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.ts`

**Previous screen-level ID:** `UI-ADM-017`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-017-04 — Groups: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, error, empty, disabled controls, conditional sections, repeated items, pagination, navigation.

**Template:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/groups/group-list/group-list.component.ts`

**Previous screen-level ID:** `UI-ADM-017`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.