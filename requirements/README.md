> **Capability taxonomy update:** See [module-based capability index](./capabilities/index.md), [ID migration](./capabilities/id-migration.md), and [category-based NFR index](./non-functional/index.md). Existing CAP-001…CAP-011 references in historical documents are retired broad-module identifiers.

# KCDF Parents — working requirements

This folder contains human-readable working requirements for planning, stakeholder review, and backlog refinement. These documents describe intended product behavior; they are not proof that the behavior is implemented or approved for production.

## Folder structure

- `applications/<app>/functional/`: user and business behavior grouped by domain
- `applications/<app>/technical/`: technical constraints and integration context
- `applications/<portal>/ui/`: screen, layout, and shared-component behavior
- `workflows/`: user journeys that cross application boundaries
- `tracking/`: requirement changes and implementation status
- `capabilities/`: business capability catalog ([index](capabilities/index.md)) with one file per capability (`CAP-NNN-*.md`), requirement [traceability and gaps](capabilities/traceability.md), and the [review summary](capabilities/review-summary.md)
- `non-functional/`: shared non-functional requirements ([baseline and proposals](non-functional/index.md)) and the [audit](non-functional/audit.md)

## Capabilities and non-functional requirements

- Capabilities (`CAP-nnn`) group business functionality across applications. They only link to requirements; application requirements remain the authoritative functional specifications, workflows describe cross-application processes, and UI documents describe screens.
- Shared NFRs (`NFR-<category>-nnn`) are defined once in `non-functional/index.md`; application documents should reference them rather than restate them.
- Part A of the NFR index is verified existing behavior (confirmed by code inspection, not by test execution). Part B is **proposed** and not approved; targets not yet agreed are marked "TBD — requires approval". Proposed changes follow `tracking/README.md`.

## Status

The requirements are working drafts. Each requirement should have a clear actor, expected behavior, applicable conditions, and testable acceptance criteria. Product decisions that have not been confirmed should be marked as open questions rather than silently assumed.

## Documentation style

Use plain language and keep product behavior separate from implementation details. Each requirement should answer:

- Who is the actor?
- What behavior is expected?
- What conditions or restrictions apply?
- What observable outcome demonstrates that it works?

Use acceptance criteria that can be checked without knowing the source-code implementation. Capture unresolved decisions in an **Open questions** section. Do not use source-code snippets or source-audit evidence as acceptance criteria. Technical implementation notes belong in the technical documents.

Suggested status values:

- **Draft:** written but not yet reviewed by stakeholders.
- **Needs decision:** one or more product or policy questions remain open.
- **Approved:** explicitly accepted by the product owner.

Implementation and test status should be tracked separately from requirement approval.

## Current focus

The requirement set covers authentication, programs, batches, enrollments, payments, groups, members, families, attendance, and admin/member portal screens. Review domain coverage and open decisions with stakeholders before treating this collection as a complete product baseline.
