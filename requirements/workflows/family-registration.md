# Family Registration — Cross-application workflow

**Workflow ID:** `WF-FAMILY-REGISTRATION`
**Status:** Draft; confirm who may create families and which details are mandatory.

## Purpose

Describe how a family record and its member relationships are created and maintained.

## Participating requirements

- `API-FAM-001` through `API-FAM-006` — Browse, view, create, and update families and family-member relationships
- `API-MEM-001` through `API-MEM-007` — Browse, view, create, and update member profiles, logins, and entity relationships
- `ADM-MBR-001` through `ADM-MBR-007` — Admin member management
- `ADM-FAM-001` through `ADM-FAM-010` — Admin family management
- `ADM-AUTH-001` — Admin portal sign-in
- `MEM-AUTH-002` — Member portal sign-in

## User journey

1. An authorized user creates or locates a family record.
2. The user enters required family details and, when available, address information.
3. The user adds or links the appropriate member profiles and assigns each relationship.
4. The user reviews the family and its linked members for accuracy.
5. If required, an authorized administrator creates or manages portal login access for a linked member.
6. The system confirms successful changes and clearly reports any validation or permission errors.

## Acceptance criteria

- Required family and member information is clear before submission.
- A family relationship identifies both the person and their relationship to the family.
- Users cannot create invalid or unauthorized relationships.
- Changes to family membership are reflected consistently in family and member views.
- Login access can be managed separately from the family profile.

## Open questions

- Define mandatory family, address, and member information.
- Define permitted relationship types and rules for primary guardians or contacts.
- Confirm whether family registration is staff-only, member self-service, or both.
- Clarify what happens to logins, enrollments, and payment history when a family relationship changes.
