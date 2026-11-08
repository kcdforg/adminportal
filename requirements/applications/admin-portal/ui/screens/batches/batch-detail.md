# Batch Detail

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-004-01 — Back: Screen structure


**Observed in source:**

- Declared screen heading(s): `Back`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-004-02 — Back: User actions


**Observed in source:**

- Declared visible actions: `Back`; `Info`; `Members ()`; `Sessions ()`; `Add Session`; `View attendance`.

- Declared event bindings: `activeTab.set('info')`; `activeTab.set('members')`; `activeTab.set('sessions')`; `addSession()`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-004-03 — Back: Navigation


**Observed in source:**

- Declared route bindings: `/batches`.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-004-04 — Back: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, conditional sections, repeated items, navigation.

**Template:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/batches/batch-detail/batch-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-004`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.