# Reliability — Non-Functional Requirements

Original requirement text and evidence are preserved below. Existing-baseline entries are not automatically approved; proposed entries remain unapproved.

## Existing observed baseline — pending owner acceptance

### NFR-REL-001 — Transactional consistency of multi-step writes
- **Category / Scope:** Reliability and data integrity / API
- **Requirement:** Operations that change more than one record (identity creation, enrollment, payment, group/notification/invitation flows, refresh-token rotation) shall commit atomically or roll back entirely.
- **Acceptance criteria:** Forcing a failure after the first write of such an operation leaves no partial records.
- **Capabilities:** CAP-002, CAP-003, CAP-005, CAP-007, CAP-008, CAP-010
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** `API-ENR-002`, `API-PAY-*`, `API-FAM-*`
- **Status / Origin:** Verified for the listed code paths; not every multi-write endpoint was checked / verified implementation constraint
- **Evidence:** `api-backend-php/services/identity.php:66`, `services/academics.php:302`, `services/payments.php:180`, `services/community-notifications.php:36`, `api/auth/refresh.php:49`.

### NFR-REL-002 — Concurrency control on contended records
- **Category / Scope:** Reliability / API (MySQL)
- **Requirement:** Capacity-, vote-, registration- and lock-sensitive operations shall serialize on the affected row so concurrent requests cannot violate the rule.
- **Acceptance criteria:** Two simultaneous enrollments for the last seat result in one success; two simultaneous accepts of one invitation result in one success.
- **Capabilities:** CAP-005, CAP-006, CAP-003, CAP-009
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Status / Origin:** Partial — `SELECT … FOR UPDATE` is used, but it is skipped on SQLite and no concurrency test exists / verified implementation constraint
- **Evidence:** `api-backend-php/services/academics.php:326,337`, `api/groups/resources.php:283,364,527`, `services/community-notifications.php:225,499`.

### NFR-REL-003 — Duplicate protection
- **Category / Scope:** Reliability / API, database
- **Requirement:** Duplicate records shall be prevented by application checks and database constraints, and reported as a conflict.
- **Acceptance criteria:** A violating insert returns 409 `DUPLICATE_ENTRY`.
- **Capabilities:** CAP-002, CAP-005, CAP-008
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Related requirements:** `API-ENR-002`, `API-GRP-016`
- **Status / Origin:** Verified (mapping of 23000 errors) / verified implementation constraint
- **Evidence:** `api-backend-php/http/helpers.php` (`handleException`), `database/schema.sql` (35 `FOREIGN KEY` clauses and unique indexes).

## Proposed — not approved

### NFR-REL-004 — Notification delivery semantics
- **Scope:** API
- **Requirement:** The delivery channels, failure handling and retry rules for `in_app`, `push` and `email` notifications shall be defined.
- **Rationale:** Types exist in code and schema but no push/e-mail delivery integration was found.
- **Acceptance criteria:** **Requires business decision** on channels and retries.
- **Capabilities / related:** CAP-010; `API-NOT-*`, [payments-and-notifications workflow](../workflows/payments-and-notifications.md)
- **Implementation status:** Partial (in-app only verified).
