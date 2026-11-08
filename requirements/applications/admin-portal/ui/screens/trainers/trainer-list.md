# Trainer List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-034-01 — Trainers: Screen structure


**Observed in source:**

- Declared screen heading(s): `Trainers`.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.ts`

**Previous screen-level ID:** `UI-ADM-034`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-034-02 — Trainers: Input controls


**Observed in source:**

- `input (text)`

- `select (select) — Status All Active Inactive On Leave`.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.ts`

**Previous screen-level ID:** `UI-ADM-034`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-034-03 — Trainers: User actions


**Observed in source:**

- Declared visible actions: `Add Trainer`; `View trainer`; `Edit trainer`.

- Declared event bindings: `openForm()`; `openForm(t); $event.stopPropagation()`.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.ts`

**Previous screen-level ID:** `UI-ADM-034`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-034-04 — Trainers: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, empty, conditional sections, repeated items, pagination, navigation.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-list/trainer-list.component.ts`

**Previous screen-level ID:** `UI-ADM-034`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.