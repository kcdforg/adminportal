# Family Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-013-01 — Family Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.ts`

**Previous screen-level ID:** `UI-ADM-013`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-013-02 — Family Form: Input controls


**Observed in source:**

- `input (text) — family_code`

- `input (text) — family_name`

- `select (select) — status`

- `input (text) — line1`

- `input (text) — line2`

- `input (text) — taluk`

- `input (text) — village`

- `input (text) — city`

- `input (text) — state`

- `input (text) — pincode`

- `input (text) — country`.

**Template:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.ts`

**Previous screen-level ID:** `UI-ADM-013`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-013-03 — Family Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.ts`

**Previous screen-level ID:** `UI-ADM-013`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-013-04 — Family Form: Conditional UI states


**Observed in source:**

- Observed template patterns: error, empty, disabled controls, conditional sections, repeated items, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-form/family-form.component.ts`

**Previous screen-level ID:** `UI-ADM-013`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.