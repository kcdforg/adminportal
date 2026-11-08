# Pending Requirement Changes

## 2026-11-08 — Readability and coverage refinement

**Status:** Draft; pending product-owner review. No implementation work is included in this change set.

### New functional requirements

- **Admin portal:** `ADM-PRG-001`–`ADM-PRG-003`, `ADM-BAT-001`–`ADM-BAT-003`, `ADM-ENR-001`–`ADM-ENR-003`, `ADM-PAY-001`–`ADM-PAY-003`, `ADM-GRP-001`–`ADM-GRP-003`, `ADM-NOT-001`–`ADM-NOT-003`, `ADM-SES-001`–`ADM-SES-003`, `ADM-TRN-001`–`ADM-TRN-003`, and `ADM-LOG-001`–`ADM-LOG-002`.
- **Member portal:** `MEM-FAM-001`–`MEM-FAM-002`, `MEM-GRP-001`–`MEM-GRP-003`, `MEM-POST-001`–`MEM-POST-002`, `MEM-PRO-001`–`MEM-PRO-002`, and `MEM-PRG-003`.

### Modified requirements and documents

- Rewrote or refined the admin and member authentication, dashboard, program, enrollment, and notification requirements.
- Reframed backend functional requirements as user-facing behavior and removed source-audit evidence and stale audit references.
- Rewrote generated portal screen, layout, and shared-component documents as behavior and acceptance criteria while retaining their requirement IDs.
- Reworked all cross-application workflows to describe user journeys, acceptance criteria, and open decisions.
- Updated the requirements overview and the enrollment API integration note.

### Open decisions for review

- Confirm role and permission matrices for admin functions, payment data, and group management.
- Define program visibility, enrollment eligibility, capacity, cancellation, and fee rules.
- Define family self-service boundaries and family relationship policies.
- Define notification channels, recipients, delivery expectations, and approval rules.
- Define session/attendance statuses, locking, and correction rules.
- Confirm member portal access to programs, groups, posts, family data, and profile editing.
- Resolve the Admin Portal enrollment-cancellation integration before treating it as complete.

These documents remain drafts until the product owner resolves applicable decisions and explicitly approves them.
