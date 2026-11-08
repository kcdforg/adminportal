# Implementation prompt — Payments and communications

Implement only the user-approved payment, refund or notification behavior.

## Read first

- [Requirements](./requirements.md) — PAY-001–PAY-004 and NOTIF-001–NOTIF-003
- [Technical specification](./technical-spec.md)
- [Implementation map](./implementation-map.md)
- [Test cases](./test-cases.md)
- [Database architecture](../../../docs/architecture/database-architecture.md)
- [API contract audit](../../../docs/architecture/api-contract-audit.md)
- [Baseline gap report](../../../docs/development/baseline-gap-report.md)

## Task constraints

Obtain the approved client/server contract and target recipients before changing forms or handlers. Do not add a payment gateway, email/push/SMS provider, refund policy, notification count or zero-recipient behavior unless explicitly specified. Preserve payment audit/transaction behavior and data integrity. Do not claim external delivery from persisted rows alone.
