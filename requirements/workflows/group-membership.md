# Group Membership — Cross-application workflow

**Workflow ID:** `WF-GROUP-MEMBERSHIP`
**Status:** Draft; confirm membership approval and removal policies.

## Purpose

Describe how a member discovers a group, joins or leaves it, and how an authorized manager maintains membership.

## Participating requirements

- `API-GRP-001` through `API-GRP-011` — Browse groups, request or change membership, and manage group roles
- Member portal group list and group-detail screens

## User journey

1. A member views groups available to them.
2. The member requests to join a group or joins directly if the group permits it.
3. If approval is required, the member can see that the request is pending.
4. An authorized manager reviews membership and updates member access or role as permitted.
5. A member or authorized manager can remove membership under the agreed policy.
6. The portal reflects the current membership state and explains unavailable actions.

## Acceptance criteria

- A user can distinguish groups they belong to from groups they may join.
- Join requests show whether they are pending, approved, or declined.
- Users cannot change another person’s membership unless their role allows it.
- Leaving or removal updates access to group-only content consistently.
- Duplicate requests and invalid membership changes are handled clearly.

## Open questions

- Confirm whether joining requires approval, is automatic, or varies by group.
- Define who can remove a member or change a group role.
- Define whether a member may rejoin after leaving or being removed.
