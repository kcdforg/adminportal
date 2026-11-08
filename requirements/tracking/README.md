# Tracking protocol

1. Exactly one active `pending/changes.md` is allowed. It accumulates changes to requirements that have not entered implementation.
2. Copilot edits requirement documents first, then lists changed requirement IDs in the pending batch (new / modified / removed), with a short reason. Existing entries are updated, not duplicated.
3. The owner reviews and commits the requirements plus pending batch. **A commit is approval only when explicitly made under the owner's approval convention.**
4. On an explicit implementation instruction, Copilot checks the approval commit and moves the pending batch to `in-progress/changes-YYYY-MM-DD.md` (implementation start date), creating a fresh empty `pending/changes.md`. Use `-02`, `-03` suffixes if that filename exists.
5. Each requirement in the batch has an implementation checklist, evidence and verification result. The batch stays in progress if any item is incomplete or unverified.
6. Once all requirements in the batch are implemented and verified, move the **same dated filename** to `completed/`; record verification evidence and completion date.
7. If a requirement is changed after its batch starts, add the new change to pending and flag the overlap. The earlier batch cannot certify the newer version.
8. Historical completed files remain immutable except for explicit corrections; a new modification creates a new pending entry.
9. Do not treat `source-reviewed`, `implemented`, and `verified` as interchangeable states. The 103 migrated requirements need initial owner acceptance and implementation assessment.
10. Workflow documents can be linked to the batch as affected references; a workflow is end-to-end verified only after a cross-application test.
