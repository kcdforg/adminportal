# Implementation prompt — Authentication and access

Implement only the user-approved changes for the authentication/access scope. Do not resolve Phase 05 proposals by assumption.

## Read first

- [Authentication requirements](./requirements.md) — AUTH-001–AUTH-007
- [Technical specification](./technical-spec.md)
- [Implementation map](./implementation-map.md)
- [Test cases](./test-cases.md)
- [Security architecture](../../../docs/architecture/security-architecture.md)
- [API contract audit](../../../docs/architecture/api-contract-audit.md)
- [Baseline gap report](../../../docs/development/baseline-gap-report.md)

## Task constraints

Before editing, identify approved requirement IDs, target files, behavior, exclusions, compatibility effects and test plan. Ask for approval if the request does not decide the AUTH-005 logout mismatch, inactive profile eligibility, or refresh rotation semantics. Change only requested scope; do not change unrelated features, requirements, API contracts or schema without approval. Preserve existing user changes and do not expose secrets.

Implement and test only the authorized task. Update API/client documentation and traceability records when behavior changes. Report exact files, commands and results; do not claim unrun checks passed.
