# Audit Logs

This document covers administrator access to recorded system activity.

## Status

Working draft. Confirm retention, privacy, and export policies.

### ADM-LOG-001 — Browse activity records

**Actor:** Super administrators.

**Requirement:** A super administrator can browse recorded activity for operational review.

**Acceptance criteria:**
- The list identifies when an event occurred, who performed it, and the affected record where available.
- Supported filters narrow the displayed records consistently.
- Empty results are shown as a normal state.
- Users without the required access cannot retrieve activity records.

### ADM-LOG-002 — Inspect activity details

**Actor:** Super administrators.

**Requirement:** A super administrator can inspect available details and recorded changes for an activity entry.

**Acceptance criteria:**
- Change details are presented in a readable way when available.
- Missing optional details do not prevent the rest of the entry from being viewed.
- Sensitive data is not exposed beyond the approved audit purpose.

## Open questions

- Define which events are recorded, retention periods, and whether export is allowed.
- Define sensitive fields that must be omitted or masked.
