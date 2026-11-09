# NFR Audit

Audit of existing requirements and source for non-functional coverage. Baseline and proposals: [index.md](./index.md). Findings distinguish **missing documentation** (behavior exists, no requirement) from **missing implementation** (no behavior found). Evidence date: 2026-11-09 (code inspection only).

Classification keys: **ES** existing and sufficient · **EI** existing but incomplete · **ED** existing but duplicated · **MD** missing documentation, behavior verified · **PN** proposed new requirement · **BD** requires business decision · **NA** not applicable.

## Existing NFR-like content in current requirements

- Application technical documents (`applications/*/technical/*.md`) describe authentication, architecture and integration in a current-state style; they contain no measurable NFRs.
- Functional requirements embed quality criteria: pagination bounds (`API-ENR-001`), duplicate rejection (`API-ENR-002`, `API-GRP-016`), token verification/refresh/revocation (`API-AUTH-003..005`), audit logs (`API-LOG-*`, `ADM-LOG-*`).
- Shared UI specs cover loading, toast, confirm and paginator behavior (`UI-ADM-SHR-*`); there is no equivalent for the member app.
- Authentication behavior is described three times (API, admin, member technical docs) — **ED** for the token lifecycle; consolidated as [NFR-SEC-002](./index.md).

## Audit table

| Category | Existing coverage | Gap | Evidence | Recommended action |
|---|---|---|---|---|
| Security — password & session | `API-AUTH-003..005` describe tokens; hashing not stated in requirements (**MD**) | Hashing, TTLs not documented as NFR; member password policy not found (**PN/BD**: missing implementation of a consistent policy) | `api/admins/store.php:35,67`, `config/config.php:51-54`, `services/auth.php:20` | Adopt NFR-SEC-001/002; decide NFR-SEC-008 |
| Security — authorization | Functional rules per endpoint; admin route guards | No role/permission matrix (missing documentation); per-endpoint coverage unverified (**EI**) | `services/identity.php:33`, `app.routes.ts` roleGuard data | Draft matrix (NFR-SEC-011) before further features |
| Security — input & injection | Validation criteria inside functional requirements (**ES** per feature) | No system-wide statement (**MD**) | `config/init.php` prepared statements, `http/helpers.php` | Adopt NFR-SEC-004 |
| Security — abuse prevention | None | No rate limiting or lockout in code or requirements (missing documentation **and** implementation) | grep of `api/auth/`, schema: no attempt counters | Propose NFR-SEC-007 (**BD**: thresholds) |
| Security — secrets/config/errors | None | Behavior exists (**MD**); `APP_DEBUG` default true (**EI**) | `http/helpers.php`, `config/config.php:38`, `config/init.php:12-17` | Adopt NFR-SEC-005; review NFR-SEC-012 |
| Security — client token storage | Technical auth docs describe flow | Storage choice undocumented (`localStorage`) (**BD**) | `auth.store.ts:85`, `member-auth.service.ts:204` | Decide NFR-SEC-009 |
| Security — CORS/transport/headers | None | CORS allow-list exists (**MD**); headers/HTTPS not in repo (**Unknown**) | `api/index.php:22-30` | Adopt NFR-SEC-006; inspect hosting for NFR-SEC-010 |
| Privacy | None | No policy for children's/family data (**BD**) | schema tables `member_profiles`, `addresses`, `family_members` | Business/legal decision (NFR-PRV-001) |
| Performance | Pagination bounds in some functional requirements (**EI**) | No targets, workload, concurrency; indexes reviewed only for `activity_logs` | `api/enrollments/index.php:26`, `schema.sql:575` | Adopt NFR-PRF-001; targets **TBD** (PRF-002/003) |
| Reliability — transactions | None | Behavior verified in 5 code paths (**MD**); not every write checked | `services/*.php`, `api/auth/refresh.php` | Adopt NFR-REL-001; audit remaining writes later |
| Reliability — concurrency | None | `FOR UPDATE` used; no test; ignored on SQLite (**MD/EI**) | `services/academics.php:326`, `groups/resources.php:283` | Adopt NFR-REL-002; consider a concurrency test (needs approval) |
| Reliability — duplicates | `API-ENR-002`, `API-GRP-016/024` (some duplicated) | System-wide conflict mapping undocumented (**MD**) | `http/helpers.php` | Adopt NFR-REL-003 |
| Reliability — notification delivery | Workflow mentions notifications | Channels/retry undefined; push/e-mail delivery not found (**BD**) | `services/community-notifications.php:425` | Decide NFR-REL-004 |
| Availability & recovery | None | Backup/restore/RPO/RTO undefined; manual backup advice only in `DEPLOYMENT.md` (missing documentation of targets) | `DEPLOYMENT.md` lines on backup/rollback, `deployment/scripts/deploy.sh` | Decide NFR-AVL-001 |
| Maintainability | Process documents exist | Migration runner absent; front-end tests absent/minimal (missing implementation); backend scripts exist (**MD**) | `database/migrations/` (6), `tests/` (7), admin 0 specs, member 2 | Adopt NFR-MNT-001/002; decide NFR-MNT-003 |
| Compatibility | None | Browsers, OS versions, screen sizes undefined (**BD**) | `capacitor.config.ts` | Decide NFR-CMP-001 |
| Accessibility & usability | `UI-ADM-SHR-*` loading/toast/dialog specs (**EI**) | No target or member-app equivalent; ARIA in 83 files, conformance unknown | `grep aria` across both apps | Decide NFR-ACC-001 |
| Observability | `API-LOG-*`, `ADM-LOG-*` audit (**ES** for data changes) | Auth events not logged; no health route (`api/health/` empty); log retention undefined | `services/identity.php:51`, `api/auth/`, `api/health/` | Adopt NFR-OBS-002/003; propose NFR-OBS-001/004 |
| Localization/time/currency | None | Not assessed; relevance (currency, time zone) needs product input | — | **NA** until the product owner confirms; recorded as an open question |

## Notes

- Nothing in Part B of the baseline is approved or implemented; do not treat the audit as a defect list.
- Documentation-only findings (hashing, CORS, transactions, audit trail) describe behavior that already exists and only need acceptance.
