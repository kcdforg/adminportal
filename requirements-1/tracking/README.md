# Requirement Change Tracking

Use `pending/changes.md` to summarize proposed requirement additions, edits, and removals that have not yet been accepted by the product owner. Copilot must review existing requirements and update this record before implementing a feature or changing user-visible behavior.

Each change entry should include:

- The requirement ID or affected document.
- Whether it is new, modified, or removed.
- A short reason for the change.
- Any open product or policy decision.

Requirement review and implementation are separate activities. Implementation must not start until the user explicitly confirms the requirements are final and asks to proceed. Do not mark a requirement approved, implemented, or verified merely because its document was edited.

At implementation start, move the pending record to `in-progress/changes-YYYY-MM-DD.md`, using the implementation start date. If the filename already exists, append `-2`, `-3`, and so on before `.md`. Keep that filename and update the record with implementation and validation progress. When implementation is complete and its relevant checks pass, move the same record to `completed/`, preserving the date-based filename unless that destination already exists; if it does, add or increment a numeric suffix (`-2`, `-3`, and so on) before `.md`. Never overwrite an existing record. If work is incomplete or blocked, leave it in `in-progress` and document what remains. Create a fresh `pending/changes.md` for the next unapproved change set.
