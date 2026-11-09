# Requirements Review & SDLC Process Recommendations

Benchmarks: ISO/IEC/IEEE 29148 (requirements quality), INVEST / Gherkin (BDD) for stories, IEEE 830 attributes (unambiguous, verifiable, traceable), Arc42 / C4 for architecture, ADRs (Nygard), DORA metrics, Definition of Ready/Done.

Observed in [requirements/](../requirements): ~264 requirement entries across `applications/{api-backend,admin-portal,member-portal}` (functional, technical, ui), 8 cross-app `workflows/`, and `tracking/{pending,in-progress,completed}`. ID scheme `API-GRP-012`, `ADM-FAM-003`, `MEM-AUTH-002`, `UI-…`, `WF-…`; most carry **Type / Owner / Legacy ID / Actor / Requirement / Acceptance criteria**. Distribution: API ≈ 199 entries, ADM ≈ 51, UI ≈ 14, MEM ≈ 14.

## 1. What is working well

- Stable, human-readable IDs and a consistent per-requirement template (Type, Owner, Actor, Requirement, Acceptance criteria).
- Separation of requirement approval from implementation status; a pending → in-progress → completed change-record process.
- Cross-app workflows that reference requirement IDs (traceability seed).
- Plain-language style that avoids code snippets.

## 2. Findings and improvements

### 2.1 Atomicity

| # | Finding | Improvement |
|---|---|---|
| R1 | Some requirements bundle multiple behaviours, e.g. payment create: *"enters a payment… For class fees, the payment is associated with an enrollment and its totals are recalculated"* = create + association + recalculation. | One requirement = one observable behaviour with a single *shall*. Split into `API-PAY-00x create payment`, `…link to enrollment`, `…recalculate enrollment totals`. Test: "can it pass/fail independently?" |
| R2 | Mix of behaviours and implementation ("update checks payment is editable **before and within its update transaction**"). | Keep the rule ("a payment in a non-editable state shall not be updated, even under concurrent updates") and move transactional detail to technical/architecture. |
| R3 | Mixed modals: "must", "shall", plain present tense. | Standardise: **shall** (mandatory), **should** (recommended), **may** (optional). One sentence form: *"The <system/actor> shall <verb> <object> when <condition>."* |
| R4 | Requirement counts are lopsided (API-GRP 34, API-FAM 19 vs ADM-PAY 3) because API entries are per-endpoint while ADM entries are per-screen. | Express **business capabilities / user stories first** (capability level), then derive per-app requirements from them, so a feature is visible across the three apps. |
| R5 | Acceptance criteria on 273 items, but wording is often prose; one doc states criteria "must still be extracted… before approval". | Use Given/When/Then, one scenario per criterion, include negative/permission/boundary cases. Each must be directly convertible into an automated test. |
| R6 | `Legacy ID` retained on ~120 items. | Keep mapping in a single `legacy-id-map.md`; remove from each entry once migration complete. |

### 2.2 Missing attributes (add to the template)

`Status` (Draft/Needs decision/Approved — currently at document level; move to requirement level), `Priority` (MoSCoW), `Rationale`, `Source/Stakeholder`, `Dependencies`, `Roles/permissions` (explicit actor-permission matrix), `Applies to` (API / Admin / Member), `Test reference` (path or test ID), `Implementation status` (None/In progress/Done + PR link), `Version/last-changed`, `NFR tags` (security, performance, privacy, a11y).

Suggested front-matter (machine readable, still human friendly):

```yaml
id: API-PAY-004
title: Record a family payment
capability: CAP-PAY-001
type: functional        # functional | nfr | constraint | ui
apps: [api, admin]
status: approved        # draft | needs-decision | approved | deprecated
priority: must
actors: [accounts-admin, super-admin]
depends_on: [API-FAM-002]
verified_by: [tests/payments/create.feature]
```

### 2.3 Gaps in coverage (non-functional & cross-cutting)

The set is dominated by functional behaviour. Add NFR requirements (each measurable):

- **Security & privacy**: password policy, lockout/rate limit, session lifetime, role matrix, audit-log retention, handling of children's personal data and consent, data deletion/export.
- **Performance/availability**: p95 latency for list endpoints, max page size, concurrency for payments, backup/RPO/RTO.
- **Accessibility**: WCAG 2.2 AA for admin and mobile.
- **Usability/UX**: loading/empty/error states, responsive breakpoints, offline behaviour (mobile).
- **Compatibility**: supported browsers, iOS/Android versions, screen sizes.
- **Localization/time/currency**: timezone, locale, currency rounding.
- **Notifications**: delivery channels, retry, opt-out.
- **Operational**: logging, monitoring, error reporting, deployment, migrations.
- **Data**: validation rules/field dictionary, status state machines, retention.
- **Admin portal ↔ member portal parity**: only 14 `MEM-*` entries vs 51 `ADM-*`; member app (home, posts, courses, family, invitations, notifications) is under-specified.
- **Glossary** (Family, Member, Profile, Batch, Enrollment, Program, Session, Group…) to remove ambiguity.
- **Persona/role catalogue** and a **permission matrix** (role × capability × CRUD) — the strongest single improvement for authorization testing (BOLA/IDOR).

### 2.4 Better organisation

Current structure is organised by **application → layer → domain**, which scatters one feature over ≥ 4 files. Recommended hybrid:

```
requirements/
  00-glossary.md
  01-personas-and-roles.md          # + permission matrix
  02-nfr/                           # security, performance, a11y, privacy …
  capabilities/                     # PRIMARY organising unit (feature-first)
    payments/
      README.md                     # capability overview, goals, scope, open questions
      stories.md                    # user stories (INVEST), with CAP-… and US-… IDs
      requirements.md               # atomic reqs tagged apps: [api, admin, member]
      scenarios/*.feature           # Gherkin acceptance criteria
      ui/                           # screen specs, wireframe links
  workflows/                        # end-to-end journeys referencing capability IDs
  decisions/                        # ADR / open decisions register
  traceability/matrix.md            # generated: req → story → test → code/PR
  tracking/{pending,in-progress,completed}   # keep as-is
```

Rationale: your stated goal is *"specify an end-to-end workflow and have requirements generated for all three apps"*. A capability-first layout lets a single change set touch one folder and spawn API + Admin + Member requirements together, rather than editing three trees. Keep `applications/<app>/technical` for app-specific constraints (architecture, integration), which genuinely differ per app.

ID scheme: keep `API-/ADM-/MEM-` prefixes (they're useful) but add capability ID `CAP-PAY-001` and story ID `US-PAY-012`; never reuse or renumber IDs; retire with `status: deprecated`.

### 2.5 UI requirements

Currently UI specs are per screen (≈ 48 files in admin). Add: shared **UI standards** (tables, forms, validation, states, a11y) as reusable rules so screen specs only state deviations. Link wireframes/Figma. Specify states: loading, empty, error, permission-denied, long-content.

## 3. Process: making the "workflow → requirements → implementation" pipeline reliable

The current Copilot instructions (requirements-first with approval gate and tracked change records) are sound. Strengthen them:

1. **Intake template** for a new workflow: goal, actors, trigger, happy path, alternates/exceptions, data, permissions, notifications, NFRs, out of scope, open questions. Copilot fills requirement drafts from this.
2. **Impact analysis step**: before writing, list affected capabilities, IDs, API endpoints, DB migrations, screens (admin + mobile), tests. Record in the change file.
3. **Definition of Ready** (requirement can be implemented): atomic, has actor, acceptance scenarios incl. negative case, permission defined, no open decisions, API contract agreed.
4. **Contract-first for cross-app work**: update `openapi.json` (the source of truth) *before* coding; generate TS types for admin & mobile; backend implements; front-ends consume. Prevents the three apps drifting.
5. **Implementation order**: DB migration → API (with tests) → admin portal → mobile; each with feature flags if released separately.
6. **Definition of Done**: all scenarios automated and passing; lint/static analysis clean; OpenAPI updated; requirements status/traceability updated; migration + rollback notes; a11y check for UI; security checklist (authz, validation, audit log); change record moved to `completed`.
7. **Traceability**: each test names the requirement ID (`@API-PAY-004`); a script (CI) checks every *approved* requirement has ≥ 1 test and every test maps to an existing ID; generate `traceability/matrix.md`.
8. **Change control**: after approval, edits to an approved requirement need a change entry with reason (versioned in git; tag baselines, e.g. `req-baseline-v1`). Mark requirements as `Implemented` only via the completed record.
9. **Requirement lint in CI**: validate front-matter, unique IDs, valid links, forbidden words ("fast", "user-friendly", "etc.", "and/or"), one *shall* per requirement.
10. **Open-decision register** (`decisions/`) with owner and due date; requirements in `needs-decision` cannot enter implementation.

## 4. Wider SDLC practices

| Area | Practice |
|---|---|
| Planning | Roadmap by capability; milestone per workflow slice (vertical slices across API+admin+mobile), small increments. |
| Branching/PRs | Trunk-based with short-lived branches `feat/<REQ-ID>-slug`; PR template with requirement IDs, test evidence, screenshots, migration notes; Conventional Commits. |
| Quality gates (CI) | Backend: lint, PHPStan, PHPUnit, composer audit. Admin/mobile: ESLint, unit tests, build budgets, Playwright e2e, axe. Requirements lint. OpenAPI lint + contract tests. |
| Environments | local (docker-compose: PHP + MySQL + mailhog) → staging → prod; seeded demo data; automated DB migrations with backup. |
| Test pyramid | Unit (services) → API integration (DB) → contract → e2e for the 8 workflows → manual exploratory/UAT per release. Test data builders/fixtures. |
| Security | Threat model per capability (STRIDE, lightweight), dependency scanning (Dependabot), secret scanning, SAST (CodeQL), pre-release OWASP ASVS checklist. |
| Architecture docs | Arc42/C4 diagrams in `docs/architecture`; ADR for each major decision (e.g. "procedural vs layered backend", "token storage"). `docs/adr` exists — keep using it. |
| Release | Semantic versions per app, changelog generated from completed change records, rollout/rollback plan, mobile store release checklist, feature flags. |
| Observability | Structured logs with request IDs, error tracking (Sentry), uptime/health checks, dashboards, DORA metrics. |
| Retrospective | Short review after each completed change record: what slipped (requirement gap vs implementation), update templates accordingly. |
| AI-assist hygiene | Keep `.github/copilot-instructions.md` short and point to templates; add per-folder instruction files (e.g. `api-backend-php`, admin, mobile conventions) and prompt files for "new workflow intake", "impact analysis", "implement requirement ID". Require Copilot to cite requirement IDs in commits/PRs. |

## 5. Suggested adoption plan

1. **Week 1**: add glossary, roles & permission matrix, NFR skeleton, requirement template with front-matter; decide folder layout (capability-first or keep current).
2. **Week 2–3**: migrate high-churn capabilities (payments, enrollments, groups) — split non-atomic requirements, convert acceptance criteria to Gherkin; add `Status/Priority`.
3. **Week 3–4**: requirements lint + traceability script in CI; PR template; intake/impact-analysis templates and Copilot prompt files.
4. **Ongoing**: contract-first OpenAPI, test tagging with requirement IDs, quarterly requirement hygiene review (deprecate stale IDs).

## 6. Open questions for you

1. Keep application-first layout or move to capability-first?
2. Should the approved status live per requirement (recommended) rather than per document?
3. Preferred acceptance format: Gherkin `.feature` files vs inline bullet scenarios?
4. Is the product owner a single approver? (affects the approval gate in the Copilot instructions)
