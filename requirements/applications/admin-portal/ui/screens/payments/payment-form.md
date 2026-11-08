# Payment Form

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-023-01 — Payment Form: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-023`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-023-02 — Payment Form: Input controls


**Observed in source:**

- `select (select) — family_id`

- `select (select) — payment_type`

- `input (number) — amount`

- `select (select) — payment_method`

- `input (date) — payment_date`

- `input (input) — reference_number`

- `textarea (textarea) — notes`.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-023`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-023-03 — Payment Form: User actions


**Observed in source:**

- Declared visible actions: `Cancel`.

- Declared event bindings: `dialogRef.close()`; `save()`.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-023`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-023-04 — Payment Form: Conditional UI states


**Observed in source:**

- Observed template patterns: disabled controls, conditional sections, repeated items, modal/dialog.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-form/payment-form.component.ts`

**Previous screen-level ID:** `UI-ADM-023`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.