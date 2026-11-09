# Authentication

This document describes administrator sign-in and access to protected areas of the administration portal.

## Status

Working draft. Confirm administrator role and session policies before approval.

### ADM-AUTH-001 — Admin portal sign-in

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `AUTH-001`
**Actor:** Administrators with an active account.

**Requirement:** An active administrator can sign in to the administration portal using valid administrator credentials. Member-only accounts cannot sign in through this flow.

**Acceptance criteria:**
- Valid administrator credentials grant access to the administration portal.
- Invalid credentials do not grant access and produce a clear message.
- A member-only account is denied access to the administrator portal.

### ADM-AUTH-007 — Require sign-in for protected screens

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `AUTH-007`
**Actor:** Administration portal users.

**Requirement:** Users must be signed in before they can view protected administration screens.

**Acceptance criteria:**
- An unauthenticated user who opens a protected screen is directed to sign in.
- After sign-in, the user can continue only to destinations allowed for their account.
- Protected information is not displayed while signed out.

### ADM-AUTH-008 — Restrict screens by administrator role

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `AUTH-008`
**Actor:** Signed-in administrators.

**Requirement:** The portal limits role-restricted screens and actions to administrators with the required permission.

**Acceptance criteria:**
- A user without the required role cannot open a restricted screen or perform its protected action.
- The portal explains that access is unavailable and provides a safe navigation path.
- Server-side authorization remains authoritative even if a screen is hidden in the portal.

## Open questions

- Confirm the role-to-screen and role-to-action permission matrix.
- Define session duration, renewal behavior, and the user experience after session expiry.
