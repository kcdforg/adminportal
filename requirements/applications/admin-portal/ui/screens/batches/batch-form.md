# Batch Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-005-01 — Create Batch: Screen structure


**Observed in source:**

- Declared screen heading(s): `Create Batch`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.ts`

**Previous screen-level ID:** `UI-ADM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-005-02 — Create Batch: Input controls


**Observed in source:**

- `input (input) — batch_name`

- `select (select) — program_id`

- `select (select) — trainer_id`

- `input (number) — capacity`

- `input (date) — start_date`

- `input (date) — end_date`

- `input (time) — start_time`

- `input (time) — end_time`

- `input (input) — schedule_days`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.ts`

**Previous screen-level ID:** `UI-ADM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-005-03 — Create Batch: User actions


**Observed in source:**

- Declared visible actions: `Back`; `Cancel`.

- Declared event bindings: `save()`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.ts`

**Previous screen-level ID:** `UI-ADM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-005-04 — Create Batch: Navigation


**Observed in source:**

- Declared route bindings: `/batches`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.ts`

**Previous screen-level ID:** `UI-ADM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-005-05 — Create Batch: Conditional UI states


**Observed in source:**

- Observed template patterns: error, disabled controls, conditional sections, repeated items, navigation.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-form/batch-form.component.ts`

**Previous screen-level ID:** `UI-ADM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.