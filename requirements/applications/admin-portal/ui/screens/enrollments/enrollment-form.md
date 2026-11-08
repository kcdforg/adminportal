# Enrollment Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-008-01 — Enroll Member: Screen structure


**Observed in source:**

- Declared screen heading(s): `Enroll Member`.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-008`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-008-02 — Enroll Member: Input controls


**Observed in source:**

- `select (select) — family_id`

- `select (select) — member_id`

- `select (select) — batch_id`

- `textarea (textarea) — notes`.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-008`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-008-03 — Enroll Member: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `onFamilyChange(form.controls.family_id.value)`; `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-008`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-008-04 — Enroll Member: Conditional UI states


**Observed in source:**

- Observed template patterns: error, disabled controls, conditional sections, repeated items, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-form/enrollment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-008`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.