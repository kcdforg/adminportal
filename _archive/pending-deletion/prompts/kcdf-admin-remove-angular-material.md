# Phased Prompt: Replace Angular Material with Tailwind UI in KCDF Admin

Use this prompt to guide an AI coding agent through removing Angular Material from the existing KCDF admin application and replacing its remaining UI controls and services with Tailwind-styled, accessible Angular components.

## Goal

Fully remove Angular Material from `kcdf-admin-app` without losing UI or behavior. The application already uses Tailwind CSS 4 utilities for much of its visual styling, but Material remains in screen controls and behavior such as dialogs, snackbars, tooltips, icons, tables, paginators, date pickers, and form controls.

The desired end state is:

- KCDF screens use Tailwind styling and native Angular/template controls or lightweight shared Tailwind components.
- There are no Angular Material imports, modules, directives, selectors, providers, or services in the app source.
- Material-only dependencies are removed from `package.json` and the lockfile only after a verified reference search.
- Authentication, authorization, API behavior, business workflows, accessibility, and static deployment remain intact.

## Mandatory operating rules

1. **Inspect before editing.** Review project instructions, the current Git working tree, project configuration, all Angular Material references, and the existing Tailwind setup.
2. **Do not overwrite concurrent work.** Preserve unrelated and uncommitted changes. Never discard or revert user changes.
3. **Phase approval gates are mandatory.** Complete Phase 0 and present a detailed inventory and migration plan. Do not edit source files until the user approves. At the end of each subsequent phase, report results and wait for approval before continuing.
4. **Do not use generated `dist/` output as source.** Make changes only to maintained source/configuration/docs. Do not edit, commit, or rely on hashed generated chunks as a fix.
5. **Do not change the PHP backend.** No PHP, schema, endpoint, API contract, or backend deployment changes.
6. **Preserve behavior, not Material implementation details.** Keep routes, role guards, login/logout/token refresh, API calls, reactive forms, field validation, filtering, pagination semantics, sorting, report calculations, export behavior, confirmation outcomes, error messages, and user feedback.
7. **Use accessible replacements.** Preserve keyboard operation, focus behavior, labels, validation association, ARIA state, modal focus trapping/escape/backdrop behavior, tooltip access, and live feedback. Do not use bare clickable elements or native `alert()`/`confirm()`/`prompt()` as shortcuts.
8. **Keep the application lightweight.** Prefer a small set of reusable, explicitly typed Tailwind components/services. Do not add a component library, NgRx, or other state framework. Avoid new runtime packages unless a concrete requirement cannot be met with native browser/Angular features.
9. **Keep Angular 21 and TypeScript 5.9.** Do not upgrade framework/compiler major versions as part of this UI replacement.
10. **Do not hide build-budget problems.** Keep the existing production budgets unless the user explicitly approves an evidence-based change. Prefer code splitting/tree-shaking and small implementations over raising limits.
11. **Validate after each phase.** Use the production build and relevant tests after each approved implementation phase, unless the user explicitly asks not to build or test. If validation is prohibited, do not run it; clearly identify it as outstanding.
12. **No fake data.** Production pages must continue to use the real KCDF API and must not gain demo records, placeholder responses, or invented endpoints.

## Known starting point (verify before acting)

The following reflects earlier inspection and is not a substitute for checking current source:

- The app is a standalone Angular 21 application using TypeScript 5.9, RxJS, signals, Angular Material/CDK, and Tailwind CSS 4.
- Tailwind utilities are wired using `@tailwindcss/postcss` and `src/tailwind.css`. Tailwind Preflight is intentionally omitted to avoid resetting Angular Material styles during the hybrid phase.
- The app contains KCDF business screens for dashboard, login, members, families, trainers, programs, batches/sessions, enrollments, payments, groups, notifications, reports, and audit logs. Their presentation has already been migrated substantially, but Material behavior and controls remain in many files.
- Material-backed behavior may include `MatDialog` and dialog data/ref, `MatSnackBar`, `MatTooltip`, `MatIcon`, Material button/form-field/input/select/checkbox/radio modules, datepicker, table/sort/paginator, progress bar/spinner, and Angular animations providers.
- The Angular Material dependency must remain until the final source-reference audit proves no Material APIs remain. Angular CDK, `@angular/animations`, and `provideAnimationsAsync` must be checked independently: remove only when no direct or required use remains.
- Production output is documented as `dist/kcdf-admin-app/browser/`, with the hosting server rewriting SPA routes to `index.html`.
- A recent production build reported the initial JS bundle at 532.53 kB, exceeding the 500 kB warning budget by 32.53 kB. Do not increase the budget to silence this warning.

## Phase 0 — Inventory and migration design (no edits)

1. Inspect current `git status`, applicable `AGENTS.md` files, and `prompts/kcdf-admin-tailadmin-upgrade.md` where present.
2. Search the full app source and configuration for all Angular Material/CDK usage:
   - Imports and providers.
   - Material elements/directives/attributes and CSS selectors/classes.
   - `MatDialog`, `MatSnackBar`, tooltips, icons, date pickers, tables/sort/paginators, form controls, progress indicators, and animations.
   - Direct CDK use independent of Material.
3. Inventory all affected files and usages, grouped by control/behavior and feature. Do not rely on a sample search or prior notes; account for every occurrence.
4. For each behavior, document the replacement approach and contract:
   - Buttons, icons, text/password/number/date inputs, select, checkbox, radio, form validation.
   - Modal/confirmation and existing member/family/payment forms presented in dialogs.
   - Snackbars/toasts from login, API error handling, role guards, and feature flows.
   - Tooltips and icon buttons.
   - Tables, sorting, paging, empty/loading states, row clicks, and responsive overflow.
   - Loading progress indicators.
   - Animation provider or CDK dependencies.
5. Identify risks such as focus management, server-side pagination semantics, date locale/timezone behavior, native select keyboard behavior, toast timing, theme/contrast, overlay stacking, and Angular zoneless change detection.
6. Propose the order and exact file groups for implementation; identify which primitives should be built once and reused.
7. Present the audit, proposed components/services, files to change, package removals planned, and verification matrix. **Stop and wait for explicit approval before edits.**

## Phase 1 — Build the shared Tailwind control foundation

Start only after Phase 0 approval.

1. Implement only the minimal reusable controls/services justified by the inventory (for example, accessible button/icon patterns, labelled form-field styles, validation message pattern, toast service/component, modal/confirmation service/component, or tooltip directive).
2. Reuse Tailwind CSS 4 and Angular primitives; do not add a component library.
3. Maintain strict typing, standalone component patterns, signal/zoneless compatibility, and the current app's naming/formatting patterns.
4. Test keyboard and focus behavior for controls that affect focus or overlays, including Escape/backdrop close, focus return, and tab navigation.
5. Keep Angular Material installed and existing screens operational while migration is in progress.
6. Run the production build and relevant tests; report results and stop for approval.

## Phase 2 — Replace feedback, icons, and tooltip behavior

Start only after Phase 1 approval.

1. Replace Material snackbars in the error interceptor, role guard, login, and feature flows with the shared accessible Tailwind notification mechanism.
2. Replace `MatIcon`/`mat-icon` usage with a consistent lightweight icon approach. Prefer inline SVG/shared icon definitions or existing repository assets; do not load a new icon package without approval.
3. Replace `MatTooltip` and tooltip directives with an accessible Tailwind tooltip pattern, preserving text labels for keyboard and assistive technology.
4. Preserve exact user-facing error/success messages, durations where meaningful, and action semantics.
5. Remove only unused imports/providers from files covered in this phase.
6. Run the production build and relevant tests; report results and stop for approval.

## Phase 3 — Replace dialogs, loading indicators, and overlay controls

Start only after Phase 2 approval.

1. Replace Material confirmation dialogs and feature dialogs with reusable Tailwind modal components/services.
2. Preserve dialog input data contracts, confirm/cancel return values, destructive-action distinction, form validation, submit/cancel behavior, and any dialog sizing/scroll behavior.
3. Implement modal accessibility: labelled dialog, `role="dialog"`/`aria-modal`, initial focus, trapped tab sequence, Escape handling where existing behavior permits, backdrop behavior, focus restoration, and scroll locking. Do not create a non-modal visual panel that silently removes those behaviors.
4. Replace progress bars/spinners with Tailwind indicators and meaningful accessible loading labels/states.
5. Replace CDK overlay/dialog use only after verifying a native/shared replacement meets behavior requirements.
6. Run the production build and relevant tests; report results and stop for approval.

## Phase 4 — Replace form controls and date selection

Start only after Phase 3 approval.

1. Replace Material buttons, form fields, inputs, selects, checkboxes, radios, datepicker, and validation components throughout all feature screens and dialogs.
2. Keep existing Angular reactive forms, `FormControl`/`FormGroup` instances, validators, value types, disabled states, touched/dirty behavior, error messages, autocomplete attributes, and submit logic.
3. For date fields, preserve the API value format, timezone/date-only semantics, validation, keyboard accessibility, min/max constraints, and locale expectations. Use native date inputs only if they satisfy these requirements; otherwise implement a small justified Tailwind date control without a broad dependency.
4. Verify selects and option values do not change types or nullable values (for example numeric IDs and explicit empty/null choices).
5. Replace all Material buttons and icon buttons without losing disabled states, submit semantics, focus rings, labels, or tooltip text.
6. Run the production build and relevant tests; report results and stop for approval.

## Phase 5 — Replace tables, sorting, pagination, and responsive data views

Start only after Phase 4 approval.

1. Replace Material tables, sorting headers, paginator, and table data-source behavior with accessible Tailwind HTML tables and small Angular helpers where required.
2. Preserve each endpoint's existing server-side pagination contract and exact page-index/page-number conversions, page sizes, filters, sorting keys, sorting direction, row actions/click behavior, and totals.
3. Preserve empty, loading, and error states; ensure horizontal scrolling and useful narrow-screen behavior.
4. Ensure table header buttons have accurate `aria-sort` state and sorting/pagination controls are keyboard accessible.
5. Run the production build and relevant tests; report results and stop for approval.

## Phase 6 — Remove Material and unused animation/CDK dependencies

Start only after Phases 1–5 have been approved and completed.

1. Search all maintained source, templates, styles, tests, configuration, and documentation for any remaining Angular Material references. Search for Material package paths, selectors/directives, providers, `Mat*` APIs, and classes, not only imports.
2. Resolve every remaining Material usage, or stop and report blockers; do not leave mixed Material controls and claim completion.
3. Determine independently whether `@angular/material`, `@angular/cdk`, `@angular/animations`, and `provideAnimationsAsync` are still required. Remove packages/providers only when verified unused. Keep Angular dependencies aligned to Angular 21.
4. Update `package.json` and regenerate the npm lockfile with the project's npm version. Do not manually prune lockfile content or replace project manifests.
5. Remove Angular Material theme/styles only after checking `src/styles.scss` and all style references. Retain Tailwind theme/utilities and existing KCDF global styles.
6. Run a full source/configuration search and show that no Angular Material references remain.
7. Run the production build and relevant tests. Report bundle size against the existing 500 kB warning/1 MB error budgets; do not change budgets unless the user approves.
8. Stop for approval before final browser/production verification.

## Phase 7 — Final runtime and deployment verification

Start only after Phase 6 approval.

1. Verify login, logout, auth guard, role guard, and error/notification behaviors.
2. Exercise critical live-API workflows: create/edit/list/detail for applicable domains, payments, attendance, filters, paging, sorting, and report/export workflows. Never substitute fake data.
3. Verify keyboard-only usage, screen-reader labels/roles, modal focus, validation, notification announcements, and contrast.
4. Inspect mobile, tablet, and desktop layouts and confirm there are no horizontal-overflow regressions beyond intentionally scrollable tables.
5. Verify production assets remain static deployable output at the documented path and server-side SPA fallback still works.
6. Update directly related documentation to reflect the final Tailwind-only control architecture, dependencies, and any operational requirements.
7. Provide a final summary with changed files, removed packages, validation results, bundle sizes, remaining limitations, and confirmation that the PHP backend was untouched.

## Required phase report

At each phase end, report:

- **Completed:** exact components/behaviors migrated.
- **Preserved behavior:** affected data contracts, forms, routes, auth, and user interactions.
- **Material remaining:** relevant use intentionally retained in this phase and why.
- **Validation:** exact commands and passed/failed/not-run status.
- **Risks/blockers:** concrete unresolved issues only.
- **Next phase:** planned scope; wait for approval before continuing.

If compilation, a test, or a behavior check fails, fix issues caused by the current phase before requesting approval to continue. Never claim validation that was not performed. Do not silently swallow API/UI errors or use success-shaped fallbacks.
