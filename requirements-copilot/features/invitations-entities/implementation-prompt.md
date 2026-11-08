# Implementation prompt — Invitations and entities

Implement only explicitly approved invitation/entity behavior. Existing backend routes do not establish an approved app workflow.

## Read first

- [Requirements](./requirements.md) — INV-001–INV-003 and ENT-001–ENT-002
- [Technical specification](./technical-spec.md)
- [Implementation map](./implementation-map.md)
- [Test cases](./test-cases.md)
- [Security architecture](../../../docs/architecture/security-architecture.md)
- [Database architecture](../../../docs/architecture/database-architecture.md)
- [Baseline gap report](../../../docs/development/baseline-gap-report.md)

## Task constraints

Obtain explicit decisions on standalone-admin inviter identity, invitation target-contact matching, family linkage and intended client scope before implementing behavior that depends on them. Do not duplicate or renumber IDs without approval. Preserve existing schema/data unless a reviewed migration is explicitly authorized. Keep secrets/private data out of output.
