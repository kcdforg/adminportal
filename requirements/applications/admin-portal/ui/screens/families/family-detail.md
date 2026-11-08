# Family Detail

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-ADM-010-01 — Edit: Screen structure


**Observed in source:**

- Declared screen heading(s): `Edit`; `Family Details`; `Husband / Father`; `Wife / Mother`; `Other Members (Guardians)`; `Children`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-010`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-010-02 — Edit: User actions


**Observed in source:**

- Declared visible actions: `Back to Families`; `Edit`; `Retry family details`; `Retry`; `Add Husband`; `Add Wife`; `Add Son`; `Add Daughter`; `Deactivate member login`; `Remove member`.

- Declared event bindings: `openEdit()`; `load()`; `retryMembers()`; `addMember('father', 'Add husband', 'male')`; `addMember('mother', 'Add Wife', 'female')`; `addMember('child', 'Add Son', 'male')`; `addMember('child', 'Add Daughter', 'female')`; `editMember(member)`; `manageLogin(member)`; `removeLogin(member)`; `removeMember(member)`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-010`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-010-03 — Edit: Navigation


**Observed in source:**

- Declared route bindings: `/families`.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-010`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-ADM-010-04 — Edit: Conditional UI states


**Observed in source:**

- Observed template patterns: loading, error, conditional sections, repeated items, navigation.

**Template:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.html`

**Component:** `kcdf-admin-app/src/app/features/families/family-detail/family-detail.component.ts`

**Previous screen-level ID:** `UI-ADM-010`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.