# Attendance Report

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-027-01 — Attendance Report: Screen structure


**Observed in source:**

- Declared screen heading(s): `Attendance Report`.

**Template:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.ts`

**Previous screen-level ID:** `UI-ADM-027`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-027-02 — Attendance Report: Input controls


**Observed in source:**

- `select (select) — batch_id`

- `input (date) — date_from`

- `input (date) — date_to`.

**Template:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.ts`

**Previous screen-level ID:** `UI-ADM-027`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-027-03 — Attendance Report: User actions


**Observed in source:**

- Declared visible actions: `Export CSV`; `Generate`.

- Declared event bindings: `exportCsv()`; `load()`.

**Template:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.ts`

**Previous screen-level ID:** `UI-ADM-027`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-027-04 — Attendance Report: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, disabled controls, conditional sections, repeated items.

**Template:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.html`

**Component:** `kcdf-admin-app/src/app/features/reports/attendance-report/attendance-report.component.ts`

**Previous screen-level ID:** `UI-ADM-027`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.