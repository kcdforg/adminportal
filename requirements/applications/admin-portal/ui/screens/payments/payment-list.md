# Payment List

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-024-01 — Payments: Screen structure


**Observed in source:**

- Declared screen heading(s): `Payments`.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-024`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-024-02 — Payments: Input controls


**Observed in source:**

- `select (select) — Payment Type All Class Fee Donation Event Fee Refund`

- `select (select) — Status All Completed Pending Failed Refunded`

- `input (date) — From Date`

- `input (date) — To Date`.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-024`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-024-03 — Payments: User actions


**Observed in source:**

- Declared visible actions: `Refund`; `Record Payment`.

- Declared event bindings: `openRefund()`; `openForm()`.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-024`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-024-04 — Payments: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, empty, conditional sections, repeated items, pagination.

**Template:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.html`

**Component:** `kcdf-admin-app/src/app/features/payments/payment-list/payment-list.component.ts`

**Previous screen-level ID:** `UI-ADM-024`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.