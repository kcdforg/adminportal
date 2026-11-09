# Copilot Development Workflow

For feature work and changes to user-visible behavior, follow the requirements-first workflow in `requirements/tracking/README.md`.

1. Before editing application code, inspect the existing requirements for the affected behavior and relevant workflows.
2. Refine or add human-readable requirements and testable acceptance criteria. Record additions, changes, removals, and unresolved decisions in `requirements/tracking/pending/changes.md`.
3. Do not start implementation until the user explicitly confirms the requirements are final and asks to proceed with implementation. If approval is ambiguous, ask before coding.
4. At implementation start, move the pending change record to `requirements/tracking/in-progress/changes-YYYY-MM-DD.md`, using the implementation start date. If that path exists, use `changes-YYYY-MM-DD-2.md`, then `-3.md`, and so on. Keep that same filename throughout the work.
5. Implement against the approved requirements, update the in-progress record with implementation and validation results, and do not claim completion until the relevant checks pass. If work is incomplete or blocked, leave the record in `in-progress` and state what remains.
6. After implementation and validation are complete, move the same record to `requirements/tracking/completed/`. Preserve its date-based filename unless that destination already exists; in that case, add or increment a numeric suffix (`-2`, `-3`, and so on) before `.md`. Never overwrite an existing record.
7. Once a pending record has been moved, create a fresh `pending/changes.md` for the next unapproved change set. Never overwrite another change record or mark unapproved requirements complete.

For requests that are documentation-only or do not change product behavior, use the relevant repository guidance and do not create an implementation tracking record unless the work changes requirements.

## Capability and NFR awareness for new business requirements

When a new business requirement is received, apply these steps within (not instead of) the workflow above:

1. Read `requirements/capabilities/index.md` and identify the affected capabilities; then read the relevant `requirements/capabilities/CAP-NNN-*.md` files.
2. Read the linked application, workflow, UI, technical, and non-functional requirements, and identify the affected applications. Do not create requirements for unaffected applications.
3. Analyze the request and ask the user about missing business rules instead of assuming them.
4. Draft or update atomic application-specific requirements, then workflow and UI changes where needed.
5. Evaluate NFR impact. Add or change a shared NFR only when justified, not merely because a functional requirement was added. Never invent numeric targets; mark them "TBD — requires approval".
6. Update the affected capability files and `requirements/capabilities/traceability.md`, then record all proposed changes in `requirements/tracking/pending/changes.md`.
7. Obtain approval before implementation; update requirements before modifying application code.
8. Implement and verify the approved change, then update implementation status, evidence, and requirement status. Do not mark a requirement implemented because documentation was written, and keep proposed NFRs separate from verified AS-IS ones.

Capability rules:

- Do not create a new capability for every feature request; prefer updating an existing one. A new capability needs a genuinely distinct business function and **user approval**. Do not split, merge, rename, or renumber capabilities without approval.
- Capability files are navigation and traceability documents: link to requirements, do not copy their text or become alternative specifications.
- When a requirement is added, modified, or retired, update its capability references, the traceability mapping, and affected workflow references in the same change set. Preserve stable IDs and avoid duplicate requirement definitions. Do not rewrite unrelated documentation.
