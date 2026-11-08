# Enrollment List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-009-01 — Enrollments: Screen structure


**Observed in source:**

- Declared screen heading(s): `Enrollments`.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-009`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-009-02 — Enrollments: Input controls


**Observed in source:**

- `select (select) — Status All Active Cancelled Completed`

- `select (select) — Payment Status All Pending Paid Overdue Waived`.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-009`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-009-03 — Enrollments: User actions


**Observed in source:**

- Declared visible actions: `Enroll Member`; `Cancel enrollment`.

- Declared event bindings: `openForm()`; `cancel(e)`.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-009`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-009-04 — Enrollments: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, empty, disabled controls, conditional sections, repeated items, pagination.

**Template:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/enrollments/enrollment-list/enrollment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-009`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.