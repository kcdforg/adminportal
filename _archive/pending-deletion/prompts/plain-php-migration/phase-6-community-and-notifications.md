# Phase 6 — Community and Notifications

## Context

Phases 0–5 are complete. Port the Slim Community and Notifications modules
using the endpoint inventory.

## Scope

- Parent groups and group membership
- Invitations and invitation acceptance
- Notifications
- Activity-log listing

## Preserve

- Public/private/invite-only visibility and group membership ownership rules.
- Admin-only actions and role-specific restrictions.
- Invitation code uniqueness, expiry, duplicate detection, acceptance,
  account creation, and associated token issuance.
- Notification ownership for read/archive operations and admin restrictions
  for sending/broadcasting.
- Activity-log authorization and audit records for mutation flows.
- Existing response, validation, filtering, and pagination behavior.

Use transactions for invitation acceptance and any operation combining
membership/account changes with audit records. Validate and store invitation
codes securely; never log passwords, token values, or unnecessary personal
data. Do not add email/SMS delivery behavior unless the Slim implementation
actually provides it.

## Exit checks

Add tests for ownership, visibility, role enforcement, duplicate and expired
invitations, concurrent/repeated acceptance, notification access, and
transaction rollback. Verify every inventory endpoint is covered.
