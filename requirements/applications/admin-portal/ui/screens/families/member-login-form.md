# Member Login Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-012-01 — Member Login Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.ts`

**Previous screen-level ID:** `UI-ADM-012`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-012-02 — Member Login Form: Input controls


**Observed in source:**

- `input (text) — username`

- `input (text) — display_name`

- `input (password) — password`

- `input (checkbox) — is_active`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.ts`

**Previous screen-level ID:** `UI-ADM-012`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-012-03 — Member Login Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.ts`

**Previous screen-level ID:** `UI-ADM-012`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-012-04 — Member Login Form: Conditional UI states


**Observed in source:**

- Observed template patterns: disabled controls, conditional sections, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/member-login-form.component.ts`

**Previous screen-level ID:** `UI-ADM-012`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.