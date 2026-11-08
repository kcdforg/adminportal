# Enrollment Report

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-028-01 — Enrollment Report: Screen structure


**Observed in source:**

- Declared screen heading(s): `Enrollment Report`; `Total Enrollments`.

**Template:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.ts`

**Previous screen-level ID:** `UI-ADM-028`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-028-02 — Enrollment Report: Input controls


**Observed in source:**

- `select (select) — batch_id`

- `select (select) — status`

- `input (date) — date_from`

- `input (date) — date_to`.

**Template:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.ts`

**Previous screen-level ID:** `UI-ADM-028`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-028-03 — Enrollment Report: User actions


**Observed in source:**

- Declared visible actions: `Export CSV`; `Generate`.

- Declared event bindings: `exportCsv()`; `load()`.

**Template:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.ts`

**Previous screen-level ID:** `UI-ADM-028`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-028-04 — Enrollment Report: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, disabled controls, conditional sections, repeated items.

**Template:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/enrollment-report/enrollment-report.component.ts`

**Previous screen-level ID:** `UI-ADM-028`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.