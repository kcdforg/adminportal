# Authentication — Cross-application workflow

**Workflow ID:** `WF-AUTHENTICATION`
**Status:** Draft; confirm sign-in, session, and access policies with the product owner.

## Purpose

Describe how an administrator or member signs in, uses protected areas, and ends a session.

## Participating requirements

- `ADM-AUTH-001` — Admin portal sign-in
- `MEM-AUTH-002` — Member portal sign-in
- `API-AUTH-003` — JWT access token verification
- `API-AUTH-004` — Refresh access credentials
- `API-AUTH-005` — Logout and refresh-token revocation
- `API-AUTH-006` — Current authenticated account
- `ADM-AUTH-007` — Require admin sign-in for protected screens
- `ADM-AUTH-008` — Restrict administrator screens by role
- `MEM-AUTH-009` — Persist member session
- `MEM-AUTH-010` — Clear member session

## User journey

1. The user opens the appropriate portal and enters their credentials.
2. The portal submits the credentials and reports success or a useful sign-in error.
3. After successful sign-in, the user can access only the areas permitted for their account.
4. When a session needs renewal, the portal renews it without losing the user’s work where possible.
5. On sign-out, the portal clears the local session and the user can no longer access protected areas.

## Acceptance criteria

- Admin and member users follow the correct portal sign-in experience.
- Invalid credentials do not grant access and produce clear feedback.
- Protected areas require an active authenticated session.
- Users without the required role cannot access role-restricted areas.
- Session renewal and sign-out have consistent outcomes across both portals.

## Open questions

- Confirm session timeout expectations and whether users should be returned to their original destination after signing in.
- Confirm what user-facing message is appropriate when a session expires or cannot be renewed.
