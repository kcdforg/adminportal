# Availability — Non-Functional Requirements

Original requirement text and evidence are preserved below. Existing-baseline entries are not automatically approved; proposed entries remain unapproved.

## Proposed — not approved

### NFR-AVL-001 — Availability, backup and recovery objectives
- **Scope:** Hosting, database
- **Requirement:** Availability expectation, database and file backup schedule, retention, restore test, and recovery objectives (RPO/RTO) shall be defined.
- **Rationale:** `DEPLOYMENT.md` says to take a backup before migrations and describes cutover/rollback, but no schedule, retention or restore procedure exists.
- **Acceptance criteria:** **TBD — requires approval**; a restore drill passes within the approved objectives.
- **Implementation status:** Documented only (manual backups before changes).
