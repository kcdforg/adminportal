# Groups

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source template:** `kcdf-parents-app/src/app/groups/groups.page.html`

**Evidence level:** Template declarations only; runtime behavior not verified.

## UI-MEM-005-01 — Groups: Screen structure


**Observed in source:**

- The source contains the screen template.

**Template:** `kcdf-parents-app/src/app/groups/groups.page.html`

**Component:** `kcdf-parents-app/src/app/groups/groups.page.ts`

**Previous screen-level ID:** `UI-MEM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-MEM-005-02 — Groups: User actions


**Observed in source:**

- Declared visible actions: `@if (group.display_name && group.display_name !== group.group_name) { } members`.

**Template:** `kcdf-parents-app/src/app/groups/groups.page.html`

**Component:** `kcdf-parents-app/src/app/groups/groups.page.ts`

**Previous screen-level ID:** `UI-MEM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.

## UI-MEM-005-03 — Groups: Conditional UI states


**Observed in source:**

- Observed template patterns: error, empty, conditional sections, repeated items, navigation.

**Template:** `kcdf-parents-app/src/app/groups/groups.page.html`

**Component:** `kcdf-parents-app/src/app/groups/groups.page.ts`

**Previous screen-level ID:** `UI-MEM-005`

**Verification:** Source declaration observed; actual behavior and integration not runtime-tested.