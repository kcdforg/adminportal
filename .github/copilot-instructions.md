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
