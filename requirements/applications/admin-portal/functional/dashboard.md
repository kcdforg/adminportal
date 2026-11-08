# Dashboard

## Requirement summary

- This document captures the current working flow in plain language.
- It supports planning, review, and backlog alignment.
- Runtime validation is still required before sign-off.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### ADM-DSH-001 — View administration dashboard

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `DSH-001`
**Actor:** Administrators.

**Requirement:** An administrator opens the dashboard to view summary counts for families, active batches and pending payments.

**Previously proposed acceptance checks (not independently verified):**
- The Angular dashboard requests three totals concurrently.
- If one request fails, the combined request can prevent all three totals from updating.
- The notification count is currently a placeholder.
- Payment-total authorization may differ for non-accounts admin roles.

**Source references:**
- `kcdf-admin-app/src/app/features/dashboard/`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).