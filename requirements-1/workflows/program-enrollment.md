# Program Enrollment — Cross-application workflow

**Workflow ID:** `WF-PROGRAM-ENROLLMENT`
**Status:** Draft; confirm the intended member self-service and administrator-assisted enrollment policies.

## Purpose

Describe how program and batch information leads to a family member being enrolled in a batch.

## Participating requirements

- `API-PRG-001` — Browse programs through API
- `MEM-PRG-002` — Display courses in Ionic app
- `API-PRG-003` — Create program
- `API-PRG-004` — Update program
- `API-PRG-005` — Change program status
- `API-BAT-001` through `API-BAT-005` — Browse, view, manage, and review batch membership
- `API-ENR-001` through `API-ENR-003` — Browse, create, and cancel enrollments

## User journey

1. An administrator maintains programs and the batches offered under them.
2. An authorized user reviews the available program and batch information.
3. The user selects an eligible family member and a suitable batch.
4. The system validates eligibility and prevents invalid or duplicate enrollment.
5. The user receives confirmation and can review the resulting enrollment.
6. If cancellation is permitted, an authorized user cancels the enrollment and receives confirmation.

## Acceptance criteria

- Users see only program and batch options they are permitted to view.
- Enrollment requires a valid family member and an eligible batch.
- The system clearly reports successful enrollment and validation failures.
- Enrollment status is consistent wherever it is displayed.
- Cancellation follows the agreed policy and does not leave the user uncertain about the result.

## Open questions

- Confirm whether members can enroll themselves or whether enrollment is administrator-assisted only.
- Define eligibility, capacity, duplicate-enrollment, cancellation, and wait-list rules.
- Confirm whether any fees or payment checks are prerequisites to enrollment.
- Confirm the expected member-facing course/program experience; sample content is not a substitute for an approved enrollment journey.
