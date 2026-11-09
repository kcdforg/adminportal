# Authentication

This document describes how a member signs in to and uses an authenticated member-portal session.

## Requirement summary

- Member accounts use the member sign-in experience.
- A successful sign-in grants access only to the member’s permitted information.
- The session remains available as intended and can be ended by signing out.
- Invalid credentials, expired sessions, and access failures are explained clearly.

## Status

Working draft. Confirm session persistence and expiry policies before approval.

### MEM-AUTH-002 — Member portal sign-in

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `AUTH-002`
**Actor:** Members with an active member account.

**Requirement:** A member can sign in to the member portal using valid member credentials.

**Acceptance criteria:**
- Member credentials are handled by the member sign-in flow.
- Invalid credentials do not grant access and produce a clear message.
- A successful sign-in takes the member to an appropriate authenticated screen.
- Member accounts cannot use administrator-only access through this flow.

### MEM-AUTH-009 — Maintain member session

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `AUTH-009`
**Actor:** Signed-in members.

**Requirement:** The portal maintains the member’s authenticated session according to the agreed session policy.

**Acceptance criteria:**
- The member can continue using protected screens while the session is valid.
- A session that cannot be renewed does not leave protected information accessible.
- The member receives clear feedback when the session expires or requires sign-in again.

### MEM-AUTH-010 — Sign out

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `AUTH-010`
**Actor:** Signed-in members.

**Requirement:** A member can sign out and end access to the authenticated portal session.

**Acceptance criteria:**
- Signing out clears access to protected portal screens on the current client.
- The member is taken to the sign-in screen or another agreed public screen.
- Reopening a protected screen after sign-out requires authentication.
- A failed server-side revocation, if applicable, is handled without retaining local access.

## Open questions

- Define session duration, persistence after browser close, and renewal behavior.
- Confirm whether active sessions should be revoked across other devices when a member signs out.
- Confirm how session-expiry messaging and return-to-destination behavior should work.
