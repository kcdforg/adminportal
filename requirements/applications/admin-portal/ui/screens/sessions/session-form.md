# Session Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-031-01 — Session Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.ts`

**Previous screen-level ID:** `UI-ADM-031`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-031-02 — Session Form: Input controls


**Observed in source:**

- `input (date) — session_date`

- `select (select) — session_type`

- `input (time) — start_time`

- `input (time) — end_time`

- `input (input) — title`

- `textarea (textarea) — topics_covered`

- `textarea (textarea) — homework`

- `textarea (textarea) — notes`

- `select (select) — status`.

**Template:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.ts`

**Previous screen-level ID:** `UI-ADM-031`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-031-03 — Session Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.ts`

**Previous screen-level ID:** `UI-ADM-031`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-031-04 — Session Form: Conditional UI states


**Observed in source:**

- Observed template patterns: error, disabled controls, conditional sections, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/sessions/session-form/session-form.component.ts`

**Previous screen-level ID:** `UI-ADM-031`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.