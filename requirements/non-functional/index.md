# Non-Functional Requirements — Category Index

NFR identifiers and wording are preserved from the uploaded baseline. The original complete index is retained as [original-index.md](./original-index.md) for auditability. No proposed NFR is approved by this restructuring.

| Category | Existing baseline | Proposed | Document |
|---|---:|---:|---|
| Security | 6 | 6 | [security.md](./security.md) |
| Privacy | 0 | 1 | [privacy.md](./privacy.md) |
| Performance | 1 | 2 | [performance.md](./performance.md) |
| Reliability | 3 | 1 | [reliability.md](./reliability.md) |
| Availability | 0 | 1 | [availability.md](./availability.md) |
| Maintainability | 2 | 1 | [maintainability.md](./maintainability.md) |
| Compatibility | 0 | 1 | [compatibility.md](./compatibility.md) |
| Accessibility | 0 | 1 | [accessibility.md](./accessibility.md) |
| Observability | 2 | 2 | [observability.md](./observability.md) |

## Existing baseline — observed, not automatically approved

| ID | Requirement | Category |
|---|---|---|
| [NFR-SEC-001](./security.md#nfr-sec-001-password-storage) | Password storage | Security |
| [NFR-SEC-002](./security.md#nfr-sec-002-token-lifecycle) | Token lifecycle | Security |
| [NFR-SEC-003](./security.md#nfr-sec-003-server-side-authorization) | Server-side authorization | Security |
| [NFR-SEC-004](./security.md#nfr-sec-004-input-validation-and-injection-protection) | Input validation and injection protection | Security |
| [NFR-SEC-005](./security.md#nfr-sec-005-safe-error-handling-and-production-configuration) | Safe error handling and production configuration | Security |
| [NFR-SEC-006](./security.md#nfr-sec-006-cross-origin-access-allow-list) | Cross-origin access allow-list | Security |
| [NFR-REL-001](./reliability.md#nfr-rel-001-transactional-consistency-of-multi-step-writes) | Transactional consistency of multi-step writes | Reliability |
| [NFR-REL-002](./reliability.md#nfr-rel-002-concurrency-control-on-contended-records) | Concurrency control on contended records | Reliability |
| [NFR-REL-003](./reliability.md#nfr-rel-003-duplicate-protection) | Duplicate protection | Reliability |
| [NFR-PRF-001](./performance.md#nfr-prf-001-bounded-list-responses) | Bounded list responses | Performance |
| [NFR-OBS-002](./observability.md#nfr-obs-002-data-change-audit-trail) | Data-change audit trail | Observability |
| [NFR-OBS-003](./observability.md#nfr-obs-003-server-side-error-logging) | Server-side error logging | Observability |
| [NFR-MNT-001](./maintainability.md#nfr-mnt-001-versioned-database-migrations) | Versioned database migrations | Maintainability |
| [NFR-MNT-002](./maintainability.md#nfr-mnt-002-backend-test-scripts-and-deployment-procedure) | Backend test scripts and deployment procedure | Maintainability |

## Proposed — awaiting approval

| ID | Proposal | Category |
|---|---|---|
| [NFR-SEC-007](./security.md#nfr-sec-007-login-abuse-protection) | Login abuse protection | Security |
| [NFR-SEC-008](./security.md#nfr-sec-008-consistent-password-policy) | Consistent password policy | Security |
| [NFR-SEC-009](./security.md#nfr-sec-009-client-token-storage) | Client token storage | Security |
| [NFR-SEC-010](./security.md#nfr-sec-010-http-security-headers-and-transport) | HTTP security headers and transport | Security |
| [NFR-SEC-011](./security.md#nfr-sec-011-role-and-permission-matrix) | Role and permission matrix | Security |
| [NFR-SEC-012](./security.md#nfr-sec-012-debug-off-by-default-in-production) | Debug off by default in production | Security |
| [NFR-REL-004](./reliability.md#nfr-rel-004-notification-delivery-semantics) | Notification delivery semantics | Reliability |
| [NFR-PRF-002](./performance.md#nfr-prf-002-performance-targets) | Performance targets | Performance |
| [NFR-PRF-003](./performance.md#nfr-prf-003-query-support-for-common-filters) | Query support for common filters | Performance |
| [NFR-OBS-001](./observability.md#nfr-obs-001-security-event-logging) | Security-event logging | Observability |
| [NFR-OBS-004](./observability.md#nfr-obs-004-operational-monitoring) | Operational monitoring | Observability |
| [NFR-MNT-003](./maintainability.md#nfr-mnt-003-automated-regression-tests-for-the-front-ends) | Automated regression tests for the front ends | Maintainability |
| [NFR-PRV-001](./privacy.md#nfr-prv-001-personal-data-of-minors-and-consent) | Personal data of minors and consent | Privacy |
| [NFR-AVL-001](./availability.md#nfr-avl-001-availability-backup-and-recovery-objectives) | Availability, backup and recovery objectives | Availability |
| [NFR-CMP-001](./compatibility.md#nfr-cmp-001-supported-browsers-and-devices) | Supported browsers and devices | Compatibility |
| [NFR-ACC-001](./accessibility.md#nfr-acc-001-accessibility-baseline) | Accessibility baseline | Accessibility |

## Review and traceability

- [NFR audit](./audit.md) — original analysis and proposals.
- [Capability ID migration](../capabilities/id-migration.md) — explains old CAP references in the preserved NFR text.
- Existing baseline findings that are partial remain partial; source-code inspection does not prove full compliance.
