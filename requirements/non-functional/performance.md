# Performance — Non-Functional Requirements

Original requirement text and evidence are preserved below. Existing-baseline entries are not automatically approved; proposed entries remain unapproved.

## Existing observed baseline — pending owner acceptance

### NFR-PRF-001 — Bounded list responses
- **Category / Scope:** Performance / API
- **Requirement:** List endpoints shall paginate and clamp page size (default 20, maximum 100).
- **Acceptance criteria:** `per_page=1000` returns at most 100 rows.
- **Capabilities:** CAP-005, CAP-008, CAP-009, CAP-011
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** `API-ENR-001` (page size bounded)
- **Status / Origin:** Partial — verified in enrollments, groups and group content; other list endpoints not individually checked / existing documented requirement
- **Evidence:** `api-backend-php/api/enrollments/index.php:26`, `api/groups/index.php:18`, `api/groups/resources.php:116`.

## Proposed — not approved

### NFR-PRF-002 — Performance targets
- **Scope:** API, Admin, Member
- **Requirement:** Under an approved reference workload, the listed operations shall meet approved response-time thresholds.
- **Rationale:** No targets, workload or concurrency expectations exist. Recommendation only: start by measuring list endpoints (families, enrollments, reports) before choosing numbers.
- **Acceptance criteria:** **TBD — requires approval** (operations, workload, thresholds).
- **Implementation status:** Not measured.

### NFR-PRF-003 — Query support for common filters
- **Scope:** Database
- **Requirement:** Columns used for filtering and joins on large lists shall be reviewed for indexes.
- **Rationale:** Activity logs have indexes (`schema.sql:575-577`); other tables were not reviewed.
- **Acceptance criteria:** Query-plan review of agreed list/report queries with agreed data volume. Volume: **TBD**.
- **Implementation status:** Unknown.
