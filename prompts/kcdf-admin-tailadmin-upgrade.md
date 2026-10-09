# Phased Prompt: Upgrade KCDF Admin UI with TailAdmin

Use this prompt to guide an AI coding agent through adopting the free TailAdmin Angular dashboard UI in the existing KCDF admin application.

## Role and goal

Act as a careful senior Angular engineer. Upgrade the UI of the existing `kcdf-admin-app` using suitable layout and UI-control patterns from the free TailAdmin Angular template:

- TailAdmin repository: <https://github.com/TailAdmin/free-angular-tailwind-dashboard>
- Existing application: `kcdf-admin-app/`
- PHP API: the existing independent PHP backend; it is not part of this frontend migration.

The goal is to make the existing KCDF admin portal use TailAdmin's visual system and suitable controls while preserving all working KCDF features and production deployment behavior. This is a phased migration, not a fresh application replacement.

## Mandatory operating rules

1. **Inspect before editing.** Review the current app, TailAdmin source at a known commit/ref, existing project instructions, and the current Git working tree. Do not overwrite unrelated or uncommitted changes.
2. **Use approval gates.** Complete Phase 0 and present the findings and proposed implementation details. Do not edit application files until the user explicitly approves. After each implementation phase, summarize the changes and validation results; wait for approval before proceeding to the next phase.
3. **Keep the PHP backend independent.** Do not edit PHP files, backend configuration, database schema, API contracts, endpoint names, or backend deployment.
4. **Preserve existing behavior.** Do not remove or rewrite the current authentication, role authorization, API services, models, route behavior, or business functionality merely to match the template.
5. **Do not replace project configuration wholesale.** In particular, do not replace `package.json`, lockfile, `angular.json`, or TypeScript configuration with TailAdmin files. Review any proposed changes and make the smallest compatible change.
6. **Do not copy TailAdmin wholesale.** Inspect dependencies and references first. Reuse/adapt only the layout, visual patterns, controls, and assets needed by KCDF.
7. **No production demo content.** Do not leave fake/sample records or demo API behavior in KCDF production pages. Do not invent API endpoints.
8. **Avoid unnecessary dependencies.** Do not add NgRx or another state framework. Do not add charting, calendar, carousel, date-picker, or other demo packages unless a verified KCDF requirement needs them.
9. **Use project conventions and preserve type safety.** Keep existing standalone component architecture and follow the app's strict compiler settings.
10. **Validate the exact phase outcome.** Run the production build after each implementation phase and resolve introduced errors before proceeding. Report any validation that could not be run and why.

## Known starting-point findings (verify before acting)

These observations were made during initial planning and may have changed; verify against the checked-out files and current upstream TailAdmin source before implementation:

- The existing app is a standalone Angular application using Angular 21.2.x, TypeScript 5.9.x, Angular Material/CDK, RxJS, signals, a typed KCDF API layer, and a production build using Angular's application builder.
- It has lazy-loaded routes and KCDF-specific screens for dashboard, members, families, trainers, programs, batches/sessions, enrollments, payments, groups, notifications, reports, and audit logs.
- Authentication uses KCDF login/logout/refresh endpoints, JWT interceptors, auth state, and role guards. These are essential existing behavior.
- The existing API URL is configured in `src/environments/`; the app README may contain stale example URLs. Confirm the actual intended production URL with the user rather than changing it by assumption.
- TailAdmin's inspected upstream `main` branch used Angular 22.1.x, TypeScript 6, Tailwind CSS 4, and multiple optional/demo dependencies. Verify current compatibility. Do not upgrade the KCDF app's Angular or TypeScript major version as a side effect of using its design.
- TailAdmin's standalone layout and its shared layout/control patterns are potential sources for the KCDF shell and controls; its dashboard and demo routes are not KCDF functionality.
- The current app is built and deployed as static SPA assets with a web-server fallback to `index.html`. Preserve its output and deployment assumptions.

## Phase 0 — Inventory, compare, and approval

Make no file changes in this phase.

1. Inspect the complete relevant KCDF app structure:
   - `package.json`, lockfile, Angular and TypeScript configuration, styles, and assets.
   - `main.ts`, app config, route setup, guards, interceptors, auth store, environments.
   - API service and all domain services/models.
   - Layout, shared controls, login page, and each existing business screen.
   - README and production deployment requirements.
2. Inspect the actual TailAdmin source that would be used:
   - Record the commit SHA/ref.
   - Check supported Angular, Node, TypeScript, and Tailwind versions.
   - Review the root layout, header, sidebar, responsive behavior, theme/styles, and relevant shared controls (buttons, fields, selects, badges, alerts, dropdowns, modals, tables, breadcrumbs).
   - Identify required dependencies and whether TailAdmin controls are standalone Angular components, Tailwind styling patterns, or both.
   - Check license/attribution obligations and confirm they are compatible with this use.
3. Compare architectures and identify:
   - Compatibility issues and migration risks.
   - Exact app files/areas to preserve, adapt, replace, or leave alone.
   - TailAdmin files/patterns to adapt, and any demo code/assets to exclude.
   - Dependency/configuration changes proposed, if any.
   - Production build/deployment and responsive risks.
4. Recommend a safe implementation sequence.
5. Present the comparison, file-level scope, controls to adopt, dependency plan, risks, and verification plan to the user. Stop and wait for explicit approval.

Do not treat this prompt or previous analysis as user approval to edit files.

## Phase 1 — Compatible UI foundation

Start only after approval of Phase 0.

1. Confirm the exact TailAdmin source revision and Angular/Tailwind compatibility with the existing project.
2. Keep the existing Angular major version unless the user separately approves an Angular upgrade after a compatibility and impact analysis.
3. Introduce the smallest required styling/tooling changes for TailAdmin's visual system. Avoid demo-only dependencies and do not replace the existing Material setup while current pages depend on it.
4. Review Tailwind 4's global/reset behavior and establish styles that allow existing Material controls and Tailwind-styled controls to coexist during migration.
5. Preserve the current production builder, budgets unless an evidence-based adjustment is approved, environment replacements, output path, base URL, and SPA deployment behavior.
6. Run the production build and report exactly what changed and what was verified. Stop for approval before Phase 2.

## Phase 2 — Application shell and navigation

Start only after approval of Phase 1.

1. Adapt TailAdmin's responsive shell patterns into the existing KCDF layout:
   - Responsive sidebar, mobile backdrop, header, content spacing, and page breadcrumb.
   - KCDF branding and identity, current-user display, and logout action.
   - Current KCDF navigation labels/routes and role-aware visibility.
2. Retain the existing route definitions, lazy loading, `authGuard`, `roleGuard`, and authorization rules. The sidebar is not an authorization boundary; route guards remain authoritative.
3. Do not introduce TailAdmin's demo routes, search behavior, notifications, account/profile behavior, or placeholder auth.
4. Verify unauthenticated redirection, role-restricted navigation and routes, logout, route refresh/deep links, desktop/tablet/mobile layout.
5. Run the production build and report the checks. Stop for approval before Phase 3.

## Phase 3 — TailAdmin controls and KCDF screen adaptation

Start only after approval of Phase 2. Migrate incrementally in reviewable groups; keep each group small enough to validate.

1. Adopt suitable TailAdmin control styles/components for existing KCDF workflows, including as applicable:
   - Buttons and icon buttons.
   - Text, password, numeric, date, and other form inputs.
   - Selects, checkboxes, radio buttons, and validation/error states.
   - Status badges, alerts, confirmation dialogs/modals, dropdowns, and action menus.
   - Tables, pagination, filters, loading/empty/error states.
   - Breadcrumbs, cards, and responsive page sections.
2. Before reusing any control, verify its inputs, outputs, accessibility, dependencies, responsive behavior, and Angular-version compatibility. Adapt to existing reactive forms and KCDF shared components rather than adding duplicate control systems without need.
3. Preserve business behavior in each screen: API calls, request/response models, filtering, sorting, pagination, validation, dialogs, actions, role constraints, loading/error handling, and route links.
4. Do not replace API-backed metrics or records with TailAdmin demo data. Where a design example has no equivalent KCDF data, omit it rather than fabricate content.
5. Keep Angular Material controls in screens not yet migrated. Do not remove Material/CDK until all references are migrated and verified.
6. After each migration group:
   - Run the production build.
   - Run relevant tests if present.
   - Verify the affected page's routes, API-backed behavior, forms/actions, and responsive layout.
   - Summarize the group and stop for user approval before the next group.

## Phase 4 — Cleanup, deployment, and final verification

Start only after approval of Phase 3.

1. Search for references before removing any old components, styles, assets, Material modules, or dependencies. Remove only items verified to be unused.
2. Keep any retained dependency that is still required by a feature; do not optimize by guessing.
3. Update directly related app documentation for actual dependencies, supported Node version, API configuration, build output, and deployment steps. Confirm uncertain production URLs with the user before changing them.
4. Verify:
   - Angular production build and configured budgets.
   - Existing login, token refresh, logout, auth guard, role guard, and role-based navigation.
   - Existing API calls and business flows (without changing backend endpoints).
   - All routes, including refresh/deep-link behavior under the documented SPA fallback.
   - Responsive behavior at mobile, tablet, and desktop widths.
   - No demo content/API stubs or unnecessary packages were introduced.
   - Static output remains deployable as documented.
5. Provide a final summary listing changed files/areas, dependencies added/removed, checks run and results, any limitations, and any user actions still needed.

## Required reporting format

At the start of each phase, state the scope and the approval needed. At the end of each phase, report:

- **Completed:** concise list of changes or analysis.
- **Preserved behavior:** auth, authorization, API, and business functionality that was retained or verified.
- **Validation:** exact commands and results; distinguish passed, failed, and not run.
- **Risks/blockers:** only concrete unresolved items.
- **Next phase:** what would happen next; wait for approval where required.

If a build or test fails, diagnose and fix issues caused by the current phase before asking to continue. Do not hide failures or claim a check passed when it was not run.
