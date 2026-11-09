# Admin Portal (kcdf-admin-app) — UI/UX & Technical Review

Benchmarks: WCAG 2.2 AA, Nielsen's 10 Usability Heuristics, Material Design 3 / Tailwind UI patterns for admin dashboards, Angular Style Guide & performance best practices, Core Web Vitals.

Observed: Angular 21 (standalone), Tailwind 4, feature-folder structure (14 features), shared components (data-table, paginator, modal, toast, confirm-dialog, status-badge, page-header, icon), auth store with localStorage tokens, 134 spec files, 33 files using `subscribe(`, no `OnPush`, 0 `aria-*` attributes in templates. `styles.scss` still carries Material-era utility styles (`Roboto`, `.mt-16`, `#f5f5f5`) alongside Tailwind — mixed styling approaches.

> Based on code/structure review; no live visual walkthrough was performed. A screenshot-based audit (Lighthouse + axe) is recommended as the next step.

## 1. Scorecard

| Area | Rating | Note |
|---|---|---|
| Visual consistency | 3 | Shared components exist; legacy Material utilities and Tailwind coexist. |
| Navigation / IA | 3 | Sidebar + main layout; 14 top-level modules - needs grouping. |
| Accessibility | 1.5 | No `aria-*` in templates; verify focus management in modals, keyboard operation, contrast. |
| Responsiveness | 2.5 | Breakpoint only seen in sidebar (≥1280px); tables likely overflow on tablets. |
| Feedback & error handling | 3.5 | Toast region, confirm dialog, loading overlay present. |
| Performance | 3 | Verify lazy routes (26 `loadComponent` hits - good), but no OnPush. |
| Code structure | 3.5 | core/features/shared/layout is a sound structure. |

## 2. Layout & Navigation

1. **Group the sidebar** into sections with headings: *Overview* (Dashboard) · *People* (Families, Members, Trainers) · *Programs* (Programs, Batches, Sessions, Enrollments) · *Community* (Groups, Notifications) · *Finance* (Payments) · *Insights* (Reports, Audit logs). Collapsible sections, active-state highlighting, icons with tooltips in collapsed rail mode.
2. **Breadcrumbs** on detail/form screens (Families › Sharma family › Edit) and a consistent `page-header` with primary action on the right.
3. **Global search / command palette** (Ctrl/⌘+K) for families, members, payments by reference — the most frequent admin task.
4. **Responsive shell**: off-canvas drawer < 1024px, sticky top bar with user menu, environment badge (DEV/UAT/PROD).
5. **Role-aware navigation**: hide modules the role cannot use (accounts admin vs super admin) instead of showing 403s.
6. Keep content max-width (~1440px) and consistent 4/8px spacing scale.

## 3. Visual design

- Define **design tokens** (Tailwind `@theme`): brand primary/secondary, semantic colours (success/warning/danger/info), neutral scale, radius, shadow, type scale. Remove hard-coded hex (`#f5f5f5`, `#999`) and the old Material utility classes in `styles.scss`.
- Typography: replace Roboto fallback stack with a single variable font (Inter) self-hosted; 14px base for dense tables, 16px forms.
- Status badges: colour **and** icon/text (not colour alone); use one mapping table (paid/pending/failed/active/inactive) shared across modules.
- Add **dark mode** token set (optional, phase 2).
- Dashboard: KPI cards with trend deltas, sparkline, date-range filter, "needs attention" list (overdue payments, sessions without attendance, pending invitations), charts (enrollment by program, monthly collections). Skeleton loaders rather than spinners.
- Empty states with illustration + primary CTA ("No batches yet – Create batch").

## 4. Data tables & forms (most of the app)

| Topic | Recommendation |
|---|---|
| Tables | Sticky header, column visibility, sortable columns with `aria-sort`, row hover, zebra optional, density toggle, horizontal scroll container on small screens, responsive "card" rendering < 640px. |
| Filters | Filter bar with chips for applied filters, "Clear all", persisted in URL query params (shareable/back-button safe). |
| Pagination | Server-driven, page-size selector (10/25/50), total count, keyboard accessible. |
| Bulk actions | Row selection + bulk status change/notify/export. |
| Export | CSV/XLSX export on reports and lists (with current filters). |
| Forms | Labels always visible, inline validation on blur, error summary at top for long forms, required marker, helper text, `autocomplete` attributes, unsaved-changes guard (`canDeactivate`), disable submit while saving, preserve focus on first invalid field. |
| Long forms (family/member/enrollment) | Break into steps or sections with sticky section nav; autosave drafts optional. |
| Destructive actions | Confirm dialog stating the object name & consequences; prefer "Undo" toast where reversible. |
| Money/dates | Locale formatting (₹, DD MMM YYYY), right-aligned numbers, timezone display on session times. |
| Audit log diff | Side-by-side diff with highlighted changes; filter by actor/entity/date. |

## 5. Accessibility (WCAG 2.2 AA) — largest gap

- 0 `aria-*` attributes in templates: add `aria-label` to icon-only buttons, `aria-live` on toast region, `role="dialog"`/`aria-modal`/labelled-by on modals with **focus trap and focus return**, `aria-sort` on tables, `aria-current="page"` in nav.
- Visible focus ring on all interactive elements (do not remove outlines); min target size 24×24px.
- Colour contrast ≥ 4.5:1 (check `#999` text on `#f5f5f5` → fails).
- Skip-to-content link; heading hierarchy; `<main>`, `<nav>` landmarks.
- Add automated axe checks (Playwright + axe-core, or `@axe-core/playwright`) in CI.

## 6. Technical UX/performance

1. Adopt `ChangeDetectionStrategy.OnPush` + signals/`toSignal` and the `async` pipe; 33 files call `subscribe(` — verify unsubscription (`takeUntilDestroyed`).
2. Token storage: access/refresh tokens in `localStorage` (XSS-exposed). Prefer in-memory access token + refresh token in an `HttpOnly; Secure; SameSite` cookie (needs backend support), or at minimum a strict CSP and no third-party scripts.
3. HTTP interceptor: single-flight token refresh (queue concurrent 401s), global error mapper to toast, retry for idempotent GET.
4. Use `@defer` for heavy dashboard/report charts; image/icon sprite for the icon component.
5. Typed API client generated from OpenAPI; `core/models/index.ts` (546 lines) is a manual mirror and will drift.
6. Test strategy: component tests exist (134 spec files); add Playwright e2e for the 8 workflows in `requirements/workflows`.
7. Add ESLint (angular-eslint) + Prettier + budgets in `angular.json`; the mobile app already has lint, admin does not.
8. i18n readiness (Angular i18n or Transloco) if Hindi/Kannada UI is possible.

## 7. UX heuristics check (Nielsen)

| Heuristic | Status | Action |
|---|---|---|
| Visibility of system status | Partial | Skeletons, progress on saves, last-updated stamps |
| Match with real world | Likely good | Use domain words consistently (Family vs Household, Batch vs Class) — glossary in requirements |
| User control & freedom | Gap | Undo, unsaved-changes guard, cancel everywhere |
| Consistency & standards | Partial | One styling system, one status mapping |
| Error prevention | Gap | Disable invalid actions (e.g. payment for inactive family), inline constraints |
| Recognition over recall | Gap | Typeahead selects (family, program, batch) instead of numeric IDs/long dropdowns |
| Flexibility & efficiency | Gap | Shortcuts, bulk actions, saved filters |
| Aesthetic & minimalist | Partial | Reduce columns by default; progressive disclosure |
| Error recovery | Partial | Human messages from API `error.code` |
| Help & documentation | Gap | Contextual help tooltips, empty-state guidance |

## 8. Priority roadmap

1. **P0 (1–2 wk):** accessibility basics (ARIA, focus trap, contrast), design tokens + remove Material leftovers, sidebar grouping, URL-persisted filters.
2. **P1 (2–4 wk):** table component upgrade (sort/sticky/responsive), form patterns (unsaved guard, error summary), dashboard redesign, typeahead selects.
3. **P2 (4–8 wk):** token storage hardening, OnPush/signals migration, generated API client, Playwright e2e + axe in CI, export/bulk actions, command palette.
4. **P3:** dark mode, i18n, saved views.
