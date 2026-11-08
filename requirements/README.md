# KCDF Parents — working requirements

This folder stores the current-state requirements in a human-readable format for planning, review, and backlog work. It is a working draft and should be validated against live runtime behavior before any requirement is considered approved for production.

## Folder structure

- `applications/<app>/functional/`: business requirements grouped by domain
- `applications/<app>/technical/`: implementation and integration notes
- `applications/<portal>/ui/`: screen and component requirements
- `workflows/`: cross-application flow drafts
- `source-audit/`: historical review material and audit notes
- `traceability/`: legacy ID mapping and crosswalk documents
- `tracking/`: backlog status tracking and review notes

## Status

These requirements are written as plain-English behavior statements based on the current implementation and project documentation. They are intended for planning and coordination, not as final product sign-off documents.

## Documentation style

Each requirement should answer four questions clearly:

- Who is the actor?
- What behavior is expected?
- What conditions or restrictions apply?
- How should it be validated?

When requirements are not yet fully confirmed, they should be marked as draft or pending runtime validation.

## Current focus

The requirement set is organized around the main product areas: authentication, programs, batches, enrollments, payments, groups, members, families, attendance, and admin/member portal flows.
