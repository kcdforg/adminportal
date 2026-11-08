# Implementation prompt — Dashboard, reports and audit

Implement only explicitly approved dashboard/report/audit behavior.

## Read first

- [Requirements](./requirements.md) — DASH-001 and AUD-001; report APIs are currently an explicit exception
- [Technical specification](./technical-spec.md)
- [Implementation map](./implementation-map.md)
- [Test cases](./test-cases.md)
- [API contract audit](../../../docs/architecture/api-contract-audit.md)
- [Baseline gap report](../../../docs/development/baseline-gap-report.md)

## Task constraints

Do not infer report calculations, filters, exports, authorization or data sources from route/page labels. Obtain approved report definitions and access rules before creating API endpoints or changing the UI. Preserve append-style audit behavior and do not expose audit data beyond approved permissions.
