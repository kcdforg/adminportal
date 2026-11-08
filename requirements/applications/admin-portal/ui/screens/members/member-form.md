# Member Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-019-01 — Member Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-019`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-019-02 — Member Form: Input controls


**Observed in source:**

- `input (text) — first_name`

- `input (text) — last_name`

- `input (email) — email`

- `input (text) — mobile`

- `select (select) — gender`

- `input (date) — date_of_birth`

- `select (select) — status`.

**Template:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-019`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-019-03 — Member Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-019`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-019-04 — Member Form: Conditional UI states


**Observed in source:**

- Observed template patterns: error, disabled controls, conditional sections, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/members/member-form/member-form.component.ts`

**Previous screen-level ID:** `UI-ADM-019`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.