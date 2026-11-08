# Requirements-to-release workflow

This workflow governs future, explicitly scoped product changes. It does not assert that the documented requirements are approved business policy or that the current application interfaces are runtime-verified. Keep the Phase 05 accuracy audit proposals unresolved unless a product owner decides them.

## Authoritative evidence

Use durable project documents and the current source for the change:

1. [Requirements index](../../requirements/index.md) and its linked requirement sets define the current provisional requirement baseline and stable IDs.
2. The relevant feature `technical-spec.md`, `implementation-map.md`, and `test-cases.md` describe source-observed interfaces and test ideas.
3. [System architecture](../architecture/system-architecture.md), feature architecture documents, and the [API contract audit](../architecture/api-contract-audit.md) describe observed boundaries and discrepancies.
4. [Baseline gap report](./baseline-gap-report.md) records known partial, missing and unresolved behavior.
5. Current application code, schema, tests and API description must be rechecked before a change; documentation can lag.

The archived [`project-conventions.mdc`](../../_archive/pending-deletion/rules/project-conventions.mdc) contains older project-layout and design assertions that do not consistently match the current repository inventory. Treat those statements as legacy guidance until reconciled with current source and a user-approved decision; do not copy them into requirements or assume they describe the current architecture.

## Change lifecycle

1. **Request:** Record the requested user-visible outcome, affected actor/workflow, motivation and explicit exclusions.
2. **Trace:** Identify stable requirement IDs. If no requirement exists, classify the gap and ask for approval before adding or changing requirements. Do not silently turn a code observation, UI label or historical statement into product policy.
3. **Impact analysis:** Trace relevant routes, handlers, permissions, validations, data/schema, admin/member clients, API contract, tests and documentation. Identify dependencies, compatibility concerns, known gaps and rollback/data risks.
4. **Decision and approval:** Present scope, requirement IDs, proposed approach and unresolved business choices. Obtain user approval before changing business rules, API compatibility, schema/data, protected paths, deploy behavior or archival state.
5. **Ready check:** Confirm the Definition of Ready below. Create a narrow implementation task/branch and keep unrelated user changes untouched.
6. **Implementation:** Change only approved scope. Follow the actual codebase patterns, preserve type/data integrity, surface errors, and avoid broad or destructive operations. Update APIs and clients coherently; do not add fictional endpoints.
7. **Verification:** Run the smallest relevant checks that are safe and available. Record exact commands/results. Distinguish existing test source, proposed tests, executed tests and runtime/deployment validation.
8. **Documentation sync:** Update requirements/spec/map/test cases, architecture/API descriptions and gap report where the change affects them. Update the API contract before or with any approved API change.
9. **Review and release:** Review the diff for scope, security/permissions, data migration and compatibility. Use the repository's approved review/release process; do not claim production readiness without deployment evidence.

## Definition of Ready

A change is ready for implementation when:

- The user-visible outcome, non-goals and affected requirement IDs are identified.
- Actors, authorization boundaries and data ownership are understood or explicitly marked unresolved.
- Existing source/API behavior and relevant client integrations have been inspected.
- API/schema compatibility, data migration and rollback considerations are recorded.
- Acceptance criteria and safe verification steps are measurable.
- Required business decisions and scope approvals are recorded; no unresolved policy is disguised as a technical assumption.

## Definition of Done

A change is done when:

- Only approved files and behavior changed; unrelated user changes remain intact.
- Acceptance criteria are mapped to implementation and evidence.
- Relevant tests/checks are executed where available, with exact results recorded; unexecuted checks remain clearly identified.
- Authorization, validation, negative/error paths and data integrity were reviewed.
- API contract, client expectations and database/migration documentation are synchronized where applicable.
- Implementation maps and test cases reflect current state; known gaps and residual risk are recorded.
- Review is complete and no unsupported runtime, security or production claims remain.

## Evidence and risk controls

- Keep facts, inferences, proposals and unresolved policy distinct.
- Do not read, reproduce or commit secrets or personal data.
- Do not run destructive migrations, archival moves, cleanup or broad refactors without explicit scoped approval.
- Preserve source/configuration changes outside scope. Never reset, clean or revert unrelated work.
- If a task cannot meet its criteria safely, stop at a coherent checkpoint and report the evidence and decision needed.
