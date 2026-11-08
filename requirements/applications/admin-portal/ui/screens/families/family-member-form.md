# Family Member Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-011-01 — Family Member Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-011`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-011-02 — Family Member Form: Input controls


**Observed in source:**

- `input (text) — first_name`

- `input (text) — last_name`

- `input (email) — email`

- `input (tel) — mobile`

- `input (date) — date_of_birth`

- `select (select) — member_role`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-011`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-011-03 — Family Member Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-011`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-011-04 — Family Member Form: Conditional UI states


**Observed in source:**

- Observed template patterns: disabled controls, conditional sections, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-011`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.