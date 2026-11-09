# Dashboard

This document describes the administration dashboard as a starting point for routine work.

## Requirement summary

- Administrators can see a concise overview of important operational information.
- Dashboard figures should reflect the underlying records and the user’s permissions.
- Missing data or failed requests should not make the entire dashboard unusable.

## Status

Working draft. Confirm which indicators are important and which administrator roles may see each one.

### ADM-DSH-001 — View administration dashboard

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `DSH-001`
**Actor:** Signed-in administrators.

**Requirement:** An administrator can open the dashboard to review key operational summaries, including family, active-batch, and pending-payment counts.

**Acceptance criteria:**
- Each displayed count is clearly labeled and represents the agreed definition of that measure.
- A count respects the administrator’s access permissions.
- The dashboard distinguishes unavailable data from a genuine zero count.
- If one summary cannot be loaded, other available dashboard information remains usable.
- Selecting a summary, when it is actionable, takes the user to the relevant area.

## Open questions

- Confirm the final dashboard indicators, definitions, and refresh expectations.
- Decide whether notification counts belong on the dashboard.
- Confirm which roles may view financial summaries.
