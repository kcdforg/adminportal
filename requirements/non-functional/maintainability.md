# Maintainability — Non-Functional Requirements

Original requirement text and evidence are preserved below. Existing-baseline entries are not automatically approved; proposed entries remain unapproved.

## Existing observed baseline — pending owner acceptance

### NFR-MNT-001 — Versioned database migrations
- **Category / Scope:** Maintainability / database
- **Requirement:** Schema changes after the baseline schema shall be delivered as dated SQL migration files.
- **Acceptance criteria:** Each post-baseline schema change has a file under `database/migrations/`.
- **Capabilities:** all
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Status / Origin:** Verified (6 migrations present; no automated runner or ordering table found) / verified implementation constraint
- **Evidence:** `api-backend-php/database/migrations/`, `api-backend-php/MIGRATION_INVENTORY.md`.

### NFR-MNT-002 — Backend test scripts and deployment procedure
- **Category / Scope:** Maintainability / API, deployment
- **Requirement:** The backend shall have repeatable verification scripts and a documented deployment/rollback procedure.
- **Acceptance criteria:** The scripts in `tests/` run against a disposable database as documented in `DEPLOYMENT.md`; deployment is serialized by a lock.
- **Capabilities:** all
  - **ID migration:** Previous CAP IDs refer to broad modules; see [migration map](../capabilities/id-migration.md).
- **Status / Origin:** Partial — 7 PHP phase scripts exist; admin app has 0 spec files and member app 2; no automated pipeline found / verified implementation constraint
- **Evidence:** `api-backend-php/tests/`, `api-backend-php/DEPLOYMENT.md` (sections "Cutover and rollback", test steps), `deployment/scripts/deploy.sh:8-9`; `kcdf-parents-app/src/app/home/home.page.spec.ts`, `app.component.spec.ts`.

---

# Part B — Proposed NFRs (not approved)

Each entry: **Status:** not implemented unless noted; **Origin:** proposed improvement; **Decision status:** proposed — awaiting approval. Targets marked TBD must be set by the product owner.

## Proposed — not approved

### NFR-MNT-003 — Automated regression tests for the front ends
- **Scope:** Admin, Member
- **Requirement:** Critical flows (login/refresh, role guards) shall have automated tests.
- **Rationale:** Admin app has no spec files; member app has 2 (generated-style).
- **Acceptance criteria:** Agreed flows covered; scope **TBD**. Do not add frameworks beyond the existing Angular/Karma tooling without approval.
- **Implementation status:** Partial.
