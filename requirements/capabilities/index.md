# KCDF Parents — Module and Atomic Capability Catalog

Modules organize related business features. **CAP IDs identify individual business capabilities**, not whole modules. Application requirements remain authoritative. The original 11 CAP IDs are retired from active use and recorded in [ID migration](./id-migration.md); new CAP IDs begin at CAP-012 to avoid reusing identifiers.

| Module | Module document | Atomic capabilities |
|---|---|---|
| MOD-001 | [Identity, Authentication & Access Control](./authentication.md) | [CAP-012](./authentication.md#cap-012-administrator-authentication), [CAP-013](./authentication.md#cap-013-member-authentication), [CAP-014](./authentication.md#cap-014-session-and-token-lifecycle), [CAP-015](./authentication.md#cap-015-authorization-and-access-control), [CAP-016](./authentication.md#cap-016-administrator-account-management), [CAP-017](./authentication.md#cap-017-member-login-account-management) |
| MOD-002 | [Family & Member Management](./family-member-management.md) | [CAP-018](./family-member-management.md#cap-018-family-records-and-relationships), [CAP-019](./family-member-management.md#cap-019-member-profiles-and-details), [CAP-020](./family-member-management.md#cap-020-member-self-service), [CAP-021](./family-member-management.md#cap-021-organization-and-entity-records) |
| MOD-003 | [Invitations & Member Onboarding](./invitations-member-onboarding.md) | [CAP-022](./invitations-member-onboarding.md#cap-022-invitation-creation-and-administration), [CAP-023](./invitations-member-onboarding.md#cap-023-invitation-acceptance-and-account-onboarding), [CAP-024](./invitations-member-onboarding.md#cap-024-invitation-status-and-eligibility) |
| MOD-004 | [Program, Batch & Trainer Management](./program-batch-trainer-management.md) | [CAP-025](./program-batch-trainer-management.md#cap-025-program-management), [CAP-026](./program-batch-trainer-management.md#cap-026-batch-management), [CAP-027](./program-batch-trainer-management.md#cap-027-trainer-management), [CAP-028](./program-batch-trainer-management.md#cap-028-program-discovery) |
| MOD-005 | [Enrollment Management](./enrollment-management.md) | [CAP-029](./enrollment-management.md#cap-029-enrollment-administration), [CAP-030](./enrollment-management.md#cap-030-enrollment-eligibility-and-capacity), [CAP-031](./enrollment-management.md#cap-031-enrollment-cancellation-and-status) |
| MOD-006 | [Sessions & Attendance](./sessions-attendance.md) | [CAP-032](./sessions-attendance.md#cap-032-session-scheduling-and-management), [CAP-033](./sessions-attendance.md#cap-033-attendance-recording), [CAP-034](./sessions-attendance.md#cap-034-attendance-corrections-and-locking) |
| MOD-007 | [Payments](./payments.md) | [CAP-035](./payments.md#cap-035-payment-recording), [CAP-036](./payments.md#cap-036-payment-correction-and-refunds), [CAP-037](./payments.md#cap-037-payment-review-and-reconciliation) |
| MOD-008 | [Group Management & Membership](./group-management-membership.md) | [CAP-038](./group-management-membership.md#cap-038-group-configuration-and-categories), [CAP-039](./group-management-membership.md#cap-039-group-discovery-and-joining), [CAP-040](./group-management-membership.md#cap-040-membership-administration-and-roles), [CAP-041](./group-management-membership.md#cap-041-group-bans-and-reactivation) |
| MOD-009 | [Group Content (Posts, Events, Polls)](./group-content.md) | [CAP-042](./group-content.md#cap-042-group-announcements-and-posts), [CAP-043](./group-content.md#cap-043-group-events-and-registration), [CAP-044](./group-content.md#cap-044-group-polls-and-voting) |
| MOD-010 | [Notifications](./notifications.md) | [CAP-045](./notifications.md#cap-045-notification-creation-and-broadcasts), [CAP-046](./notifications.md#cap-046-member-notification-inbox-and-status), [CAP-047](./notifications.md#cap-047-notification-preferences-and-delivery) |
| MOD-011 | [Reporting, Dashboard & Audit](./reporting-dashboard-audit.md) | [CAP-048](./reporting-dashboard-audit.md#cap-048-dashboard-and-summary-metrics), [CAP-049](./reporting-dashboard-audit.md#cap-049-enrollment-and-attendance-reporting), [CAP-050](./reporting-dashboard-audit.md#cap-050-payment-reporting), [CAP-051](./reporting-dashboard-audit.md#cap-051-activity-log-review) |

## How to use

1. Identify the relevant module, then the individual CAP describing the requested business outcome.
2. Follow requirement links in that module to the authoritative application specifications.
3. Consult [traceability](./traceability.md), [workflows](../workflows/) and [NFR index](../non-functional/index.md) for cross-cutting impact.
4. A new CAP ID must represent a meaningful distinct business ability; individual validation rules and CRUD steps generally remain atomic requirements.
5. Proposed capability splits and ambiguous mappings require product-owner review before implementation.

## Review status

This restructuring is a **documentation taxonomy proposal**, not an implementation audit or approval of new business requirements. Rows marked `Mapping requires review` need manual verification.

- [Legacy-to-current ID migration](./id-migration.md)
- [Traceability by atomic CAP](./traceability.md)
- [Original catalog and mapping](./legacy/)
