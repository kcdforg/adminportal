# CAP-003: Invitations & Member Onboarding

## 1. Purpose

Bring new people into the platform through invitations instead of open registration.

## 2. Scope

Creating, listing, viewing and cancelling invitations, and accepting an invitation to register an account.

## 4. Application Requirements

Titles are shown for orientation only; the linked documents are authoritative. "Supporting" means the requirement serves this capability but is primarily owned by another.

### PHP API Backend

| Requirement ID | Description | Relationship | Reference |
|---|---|---|---|
| `API-INV-001` | Create invitation | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |
| `API-INV-002` | List invitations | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |
| `API-INV-003` | View invitation status | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |
| `API-INV-004` | Cancel invitation | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |
| `API-INV-005` | Accept invitation and register account | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |

## 5. Related Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-INVITATIONS` | Primary | [invitations.md](../workflows/invitations.md) |

## 8. Related Non-Functional Requirements

IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-001`, `NFR-SEC-004`, `NFR-REL-001`, `NFR-REL-002`
- **Proposed (not approved):** `NFR-SEC-008`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).

## 9. Related Capabilities

- [CAP-001 Identity, Authentication & Access Control](./CAP-001-identity-authentication-access-control.md) — acceptance creates a member login
- [CAP-002 Family & Member Management](./CAP-002-family-member-management.md) — accepted invitations link to member/family records

## 10. Implementation Status

**Partially implemented.** API operations exist; invitation acceptance is wired in the member app (route `invitations/:code`). There is no admin-portal screen for creating or managing invitations, and `API-INV-*` were not individually source-checked beyond locking on accept.

## 11. Source-Code Evidence

- `api-backend-php/api/invitations/` (`store.php`, `cancel.php`, `accept.php:59-65`), `services/community-notifications.php:499` (`FOR UPDATE` on accept).
- Routes: `/invitations`, `/invitations/{code}`, `/invitations/{code}/accept`.
- `kcdf-parents-app/src/app/invitations/`, route `invitations/:code` in `app.routes.ts`. Database: `invitations`; tests: `phase6-community-notifications.php`.

## 12. Known Gaps

- No member-portal `MEM-*` or `UI-MEM-*` requirement describes invitation acceptance.
- No admin screen for invitations although `API-INV-001..004` exist.
- Product context mentioned OTP verification; no OTP exists in code or requirements.
- `WF-FAMILY-REGISTRATION` does not reference invitations; the earlier mapping of that workflow to this capability was corrected.
