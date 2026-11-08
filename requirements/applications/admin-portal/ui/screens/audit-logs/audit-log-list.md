# Audit Log List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-001-01 — Audit Logs: Screen structure


**Observed in source:**

- Declared screen heading(s): `Audit Logs`.

**Template:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.ts`

**Previous screen-level ID:** `UI-ADM-001`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-001-02 — Audit Logs: Input controls


**Observed in source:**

- `input (input) — e.g. Family, Member`

- `input (input) — e.g. create, update`

- `input (date) — From Date`.

**Template:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.ts`

**Previous screen-level ID:** `UI-ADM-001`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-001-03 — Audit Logs: User actions


**Observed in source:**

- Declared visible actions: `View audit log details`.

- Declared event bindings: `viewDiff(l)`; `viewDiff(l); $event.stopPropagation()`.

**Template:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.ts`

**Previous screen-level ID:** `UI-ADM-001`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-001-04 — Audit Logs: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, empty, conditional sections, repeated items, pagination.

**Template:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/audit-logs/audit-log-list/audit-log-list.component.ts`

**Previous screen-level ID:** `UI-ADM-001`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.