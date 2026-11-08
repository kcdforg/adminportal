# KCDF Parents — working requirements

This folder contains human-readable working requirements for planning, stakeholder review, and backlog refinement. These documents describe intended product behavior; they are not proof that the behavior is implemented or approved for production.

## Folder structure

- `applications/<app>/functional/`: user and business behavior grouped by domain
- `applications/<app>/technical/`: technical constraints and integration context
- `applications/<portal>/ui/`: screen, layout, and shared-component behavior
- `workflows/`: user journeys that cross application boundaries
- `tracking/`: requirement changes and implementation status

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
