# Capability ID Migration — Historical to Module / Atomic CAP

**Policy:** The uploaded CAP-001…CAP-011 identifiers previously denoted broad domains. They are now historical/retired, not reassigned. Atomic CAP identifiers begin at CAP-012. This avoids silently changing the meaning of an existing identifier.

| Historical CAP ID | Current module | New atomic CAP IDs |
|---|---|---|
| CAP-001 (retired) | [MOD-001: Identity, Authentication & Access Control](./authentication.md) | CAP-012, CAP-013, CAP-014, CAP-015, CAP-016, CAP-017 |
| CAP-002 (retired) | [MOD-002: Family & Member Management](./family-member-management.md) | CAP-018, CAP-019, CAP-020, CAP-021 |
| CAP-003 (retired) | [MOD-003: Invitations & Member Onboarding](./invitations-member-onboarding.md) | CAP-022, CAP-023, CAP-024 |
| CAP-004 (retired) | [MOD-004: Program, Batch & Trainer Management](./program-batch-trainer-management.md) | CAP-025, CAP-026, CAP-027, CAP-028 |
| CAP-005 (retired) | [MOD-005: Enrollment Management](./enrollment-management.md) | CAP-029, CAP-030, CAP-031 |
| CAP-006 (retired) | [MOD-006: Sessions & Attendance](./sessions-attendance.md) | CAP-032, CAP-033, CAP-034 |
| CAP-007 (retired) | [MOD-007: Payments](./payments.md) | CAP-035, CAP-036, CAP-037 |
| CAP-008 (retired) | [MOD-008: Group Management & Membership](./group-management-membership.md) | CAP-038, CAP-039, CAP-040, CAP-041 |
| CAP-009 (retired) | [MOD-009: Group Content (Posts, Events, Polls)](./group-content.md) | CAP-042, CAP-043, CAP-044 |
| CAP-010 (retired) | [MOD-010: Notifications](./notifications.md) | CAP-045, CAP-046, CAP-047 |
| CAP-011 (retired) | [MOD-011: Reporting, Dashboard & Audit](./reporting-dashboard-audit.md) | CAP-048, CAP-049, CAP-050, CAP-051 |

## References requiring interpretation

Existing NFR text, original audit, legacy capability snapshots and historical tracker entries may mention CAP-001…CAP-011. These are **historical module-level associations**; they do not mean that each new atomic CAP inherits every NFR. Assess applicability before establishing an individual CAP-to-NFR link.

## Preservation

Original capability documents are archived in [legacy](./legacy/). Existing application requirements and their IDs were not changed.

## Approval required

Confirm the proposed CAP boundaries and the rows flagged for mapping review before adopting this as the authoritative capability taxonomy.
