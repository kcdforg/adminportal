# Program Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-025-01 — Program Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.ts`

**Previous screen-level ID:** `UI-ADM-025`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-025-02 — Program Form: Input controls


**Observed in source:**

- `input (text) — name`

- `textarea (textarea) — description`

- `select (select) — program_type`

- `input (number) — fee_amount`

- `select (select) — fee_frequency`

- `select (select) — status`.

**Template:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.ts`

**Previous screen-level ID:** `UI-ADM-025`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-025-03 — Program Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.ts`

**Previous screen-level ID:** `UI-ADM-025`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-025-04 — Program Form: Conditional UI states


**Observed in source:**

- Observed template patterns: error, disabled controls, conditional sections, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/programs/program-form/program-form.component.ts`

**Previous screen-level ID:** `UI-ADM-025`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.