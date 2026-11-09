# CAP-001: Identity, Authentication & Access Control

## 1. Purpose

Let administrators and members prove who they are, keep a session, and be limited to what their role and scope allow.

## 2. Scope

Admin and member sign-in, access-token verification, refresh and logout with token revocation, current-account lookup, route/role restrictions in the admin portal, administrator login accounts, and creation/maintenance of member login credentials.

## 3. Sub-Capabilities

- Portal sign-in and sign-out (admin, member)
- Token lifecycle (verify, refresh, revoke)
- Role-based access (admin roles, route guards)
- Administrator account management
- Member login account management

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-ADM-001` | List administrator accounts | Primary | [administrators.md](../applications/api-backend/functional/administrators.md) |
| `API-ADM-002` | View administrator account | Primary | [administrators.md](../applications/api-backend/functional/administrators.md) |
| `API-ADM-003` | Create administrator account | Primary | [administrators.md](../applications/api-backend/functional/administrators.md) |
| `API-ADM-004` | Update administrator account | Primary | [administrators.md](../applications/api-backend/functional/administrators.md) |
| `API-AUTH-003` | JWT access token verification | Cross-cutting | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-004` | Refresh access credentials | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-005` | Logout and refresh-token revocation | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-006` | Current authenticated account | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-007` | Require username for member login | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-008` | Require password for member login | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-009` | Restrict member login to member portal | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-010` | Restrict admin login to admin portal | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-011` | Require refresh token for credential renewal | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-012` | Reject revoked refresh tokens | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-013` | Rotate refresh token on renewal | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-014` | Check account activity on refresh | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-015` | Require matching login on logout | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-AUTH-016` | Return authenticated login profile | Primary | [authentication.md](../applications/api-backend/functional/authentication.md) |
| `API-INV-005` | Accept invitation and register account | Supporting | [invitations.md](../applications/api-backend/functional/invitations.md) |
| `API-MEM-005` | Create member login | Primary | [members.md](../applications/api-backend/functional/members.md) |
| `API-MEM-006` | Maintain member login | Primary | [members.md](../applications/api-backend/functional/members.md) |

### Angular Admin Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `ADM-AUTH-001` | Admin portal sign-in | Primary | [authentication.md](../applications/admin-portal/functional/authentication.md) |
| `ADM-AUTH-007` | Require sign-in for protected screens | Cross-cutting | [authentication.md](../applications/admin-portal/functional/authentication.md) |
| `ADM-AUTH-008` | Restrict screens by administrator role | Cross-cutting | [authentication.md](../applications/admin-portal/functional/authentication.md) |
| `ADM-FAM-009` | Manage member login access for family members | Primary | [families.md](../applications/admin-portal/functional/families.md) |

### Ionic Member Portal

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `MEM-AUTH-002` | Member portal sign-in | Primary | [authentication.md](../applications/member-portal/functional/authentication.md) |
| `MEM-AUTH-009` | Maintain member session | Primary | [authentication.md](../applications/member-portal/functional/authentication.md) |
| `MEM-AUTH-010` | Sign out | Primary | [authentication.md](../applications/member-portal/functional/authentication.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-AUTHENTICATION` | Primary | [authentication.md](../workflows/authentication.md) |
| `WF-FAMILY-REGISTRATION` | Supporting | [family-registration.md](../workflows/family-registration.md) |
| `WF-INVITATIONS` | Supporting | [invitations.md](../workflows/invitations.md) |

## 6. Related UI Requirements

| Screen | UI requirement IDs | Relationship | Reference |
|---|---|---|---|
| Admin portal login (admin) | `UI-ADM-003-01` … `UI-ADM-003-08` (8) | Primary | [login.md](../applications/admin-portal/ui/screens/auth/login.md) |
| Family Detail (admin) | `UI-ADM-010-01` … `UI-ADM-010-04` (4) | Supporting | [family-detail.md](../applications/admin-portal/ui/screens/families/family-detail.md) |
| Member Login Form (admin) | `UI-ADM-012-01` … `UI-ADM-012-04` (4) | Primary | [member-login-form.md](../applications/admin-portal/ui/screens/families/member-login-form.md) |
| Login (member) | `UI-MEM-007-01` … `UI-MEM-007-04` (4) | Primary | [login.md](../applications/member-portal/ui/screens/login/login.md) |
| Profile (member) | `UI-MEM-008-01` … `UI-MEM-008-04` (4) | Supporting | [profile.md](../applications/member-portal/ui/screens/me/profile.md) |

## 7. Related Technical Requirements

- [admin-portal/technical/authentication.md](../applications/admin-portal/technical/authentication.md)
- [api-backend/technical/authentication.md](../applications/api-backend/technical/authentication.md)
- [member-portal/technical/authentication.md](../applications/member-portal/technical/authentication.md)

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-001`, `NFR-SEC-002`, `NFR-SEC-003`, `NFR-SEC-004`, `NFR-SEC-005`, `NFR-SEC-006`, `NFR-OBS-003`
- **Proposed (not approved):** `NFR-SEC-007`, `NFR-SEC-008`, `NFR-SEC-009`, `NFR-SEC-011`, `NFR-OBS-001`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-002 Family & Member Management](./CAP-002-family-member-management.md) — member profiles own the logins created here
- [CAP-003 Invitations & Member Onboarding](./CAP-003-invitations-member-onboarding.md) — invitation acceptance creates a member login

## 10. Implementation Status

**Implemented.** Verified by targeted inspection: password hashing and verification, refresh-token rotation and revocation, portal-specific login handlers, admin role guards. Not individually checked: `MEM-AUTH-009` (session policy is explicitly undefined in the requirement) and per-endpoint role enforcement (see `NFR-SEC-003`, partial).

## 11. Source-Code Evidence

- `api-backend-php/api/auth/login.php`, `admin-login.php`, `refresh.php` (rotation, lines 49-90), `logout.php:33`, `me.php`; routes in `api/routes.php` (`/auth/*`).
- `api-backend-php/services/auth.php:20,37` (secret validation), `services/identity.php:33,409` (`requirePermission`, `password_verify`).
- `api-backend-php/api/admins/store.php:67` and `api/members/login-store.php:73` (`password_hash`).
- `kcdf-admin-app/src/app/core/guards/auth.guard.ts`, `role.guard.ts`, `core/interceptors/error.interceptor.ts` (refresh on 401); `app.routes.ts` role data.
- `kcdf-parents-app/src/app/core/member-auth.service.ts` (token persistence in `localStorage`, line ~204).
- Database: `user_logins`, `refresh_tokens`; migration `20261010_user_login_types.sql`. Tests: `api-backend-php/tests/phase8-auth-account-types.php`.

Individually source-checked requirements for this capability: `ADM-AUTH-008`, `ADM-FAM-009`, `API-ADM-003`, `API-AUTH-005`, `API-AUTH-012`, `API-AUTH-013`, `API-MEM-005`. All other requirements were content-reviewed for mapping but not individually checked against code.

## 12. Known Gaps

- `API-AUTH-001/002`, `ADM-AUTH-002..006` and `MEM-AUTH-001, 003..008` are absent from their ID sequences (cause unknown).
- No requirement covers login throttling, account lockout, or password policy (proposed `NFR-SEC-007`, `NFR-SEC-008`).
- Member-login management is split across capabilities by design: the credentials belong here, the profile belongs to CAP-002 (boundary to confirm).
- No admin-portal screen for administrator accounts although `API-ADM-*` exist (`/admins` routes have no client).
- `ADM-AUTH-007`, `ADM-AUTH-008` and `API-AUTH-003` are classified cross-cutting: they apply to every protected capability.
