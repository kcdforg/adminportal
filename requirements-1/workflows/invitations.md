# Invitations — Cross-application workflow

**Workflow ID:** `WF-INVITATIONS`
**Status:** Draft; confirm invitation eligibility, expiry, and account-creation rules.

## Purpose

Describe how an eligible person is invited to join, accepts the invitation, and receives the appropriate account access.

## Participating requirements

- `API-INV-001` through `API-INV-005` — Create, list, view, cancel, and accept invitations
- `API-FAM-001` through `API-FAM-006` — Family records and relationships
- `API-MEM-001` through `API-MEM-007` — Member profiles, login access, and entity relationships
- Admin and member authentication requirements

## User journey

1. An authorized user creates an invitation for an eligible person and specifies its intended purpose.
2. The invited person receives and opens the invitation.
3. The system checks that the invitation is valid and has not been used, cancelled, or expired.
4. The person accepts the invitation and completes any required registration steps.
5. The system confirms acceptance and provides the appropriate next step for signing in.
6. An authorized user can review or cancel invitations that have not been accepted.

## Acceptance criteria

- An invitation communicates its purpose and expiry, if an expiry applies.
- Invalid, expired, cancelled, or already-used invitations cannot be accepted.
- Acceptance does not create duplicate accounts or relationships.
- Users receive a clear confirmation or explanation when an invitation cannot be used.
- Cancellation prevents subsequent acceptance.

## Open questions

- Define invitation expiry, resend, and cancellation behavior.
- Confirm whether accepting an invitation creates a profile, a login, a family relationship, or some combination.
- Define duplicate-email/phone handling and recovery when the invitee already has an account.
