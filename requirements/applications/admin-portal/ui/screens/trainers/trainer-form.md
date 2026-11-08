# Trainer Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-033-01 — Profile Details: Screen structure


**Observed in source:**

- Declared screen heading(s): `Profile Details`; `Address Details`; `Trainer Details`.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.ts`

**Previous screen-level ID:** `UI-ADM-033`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-033-02 — Profile Details: Input controls


**Observed in source:**

- `input (text) — first_name`

- `input (text) — last_name`

- `input (date) — date_of_birth`

- `select (select) — gender`

- `input (tel) — mobile`

- `input (email) — email`

- `input (text) — line1`

- `input (text) — line2`

- `input (text) — taluk`

- `input (text) — village`

- `input (text) — city`

- `input (text) — state`

- `input (text) — postal_code`

- `input (text) — country`

- `input (text) — specialization`

- `input (number) — experience_years`

- `input (date) — joined_at`

- `select (select) — status`

- `textarea (textarea) — bio`.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.ts`

**Previous screen-level ID:** `UI-ADM-033`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-033-03 — Profile Details: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.ts`

**Previous screen-level ID:** `UI-ADM-033`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-033-04 — Profile Details: Conditional UI states


**Observed in source:**

- Observed template patterns: disabled controls, conditional sections, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/trainers/trainer-form/trainer-form.component.ts`

**Previous screen-level ID:** `UI-ADM-033`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.