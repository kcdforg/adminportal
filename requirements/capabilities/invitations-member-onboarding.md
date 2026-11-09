# Module: Invitations & Member Onboarding

> **Module ID:** MOD-003  
> **Previous broad capability:** CAP-003 (retired as a capability ID; see [ID migration](./id-migration.md))

## Purpose

Bring new people into the platform through invitations instead of open registration.

## Scope

Creating, listing, viewing and cancelling invitations, and accepting an invitation to register an account.

## Atomic business capabilities

The CAP IDs below identify coherent business abilities; detailed requirements remain authoritative in the linked application documents. Classification is documentation-level and is not a claim of complete source-code verification.

### CAP-022 — Invitation creation and administration

**Business outcome:** invitation creation and administration within the invitations & member onboarding module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-INV-001` | Create invitation | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) | <!-- Mapping requires review -->
| `API-INV-002` | List invitations | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) | <!-- Mapping requires review -->
| `API-INV-004` | Cancel invitation | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) | <!-- Mapping requires review -->

### CAP-023 — Invitation acceptance and account onboarding

**Business outcome:** invitation acceptance and account onboarding within the invitations & member onboarding module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-INV-005` | Accept invitation and register account | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |

### CAP-024 — Invitation status and eligibility

**Business outcome:** invitation status and eligibility within the invitations & member onboarding module.

| Requirement ID / UI screen | Description | Relationship | Source |
|---|---|---|---|
| `API-INV-003` | View invitation status | Primary | [invitations.md](../applications/api-backend/functional/invitations.md) |

## Related workflows and UI

### Workflows

| Workflow | Relationship | Reference |
|---|---|---|
| `WF-INVITATIONS` | Primary | [invitations.md](../workflows/invitations.md) |





## Technical references



## Non-functional references

The following are historical module-level NFR relationships; see the [NFR index](../non-functional/index.md) for unchanged NFR definitions and approval statuses.



IDs are defined in [../non-functional/index.md](../non-functional/index.md).

- **Baseline (verified, pending owner acceptance):** `NFR-SEC-001`, `NFR-SEC-004`, `NFR-REL-001`, `NFR-REL-002`
- **Proposed (not approved):** `NFR-SEC-008`
- System-wide NFRs that apply to every capability are listed once in [index.md](./index.md).



## Implementation evidence and gaps

Source inspection and implementation-status statements below were carried forward from the previous module-level catalog. They must **not** be interpreted as independently verified for every atomic CAP.

### Prior module-level assessment

**Partially implemented.** API operations exist; invitation acceptance is wired in the member app (route `invitations/:code`). There is no admin-portal screen for creating or managing invitations, and `API-INV-*` were not individually source-checked beyond locking on accept.



### Prior source evidence

- `api-backend-php/api/invitations/` (`store.php`, `cancel.php`, `accept.php:59-65`), `services/community-notifications.php:499` (`FOR UPDATE` on accept).
- Routes: `/invitations`, `/invitations/{code}`, `/invitations/{code}/accept`.
- `kcdf-parents-app/src/app/invitations/`, route `invitations/:code` in `app.routes.ts`. Database: `invitations`; tests: `phase6-community-notifications.php`.



### Known gaps

- No member-portal `MEM-*` or `UI-MEM-*` requirement describes invitation acceptance.
- No admin screen for invitations although `API-INV-001..004` exist.
- Product context mentioned OTP verification; no OTP exists in code or requirements.
- `WF-FAMILY-REGISTRATION` does not reference invitations; the earlier mapping of that workflow to this capability was corrected.


## Mapping review notes

- Automated classification assigned 3 requirement/screen rows to a provisional CAP because the title did not clearly identify a sub-capability. Review rows marked `Mapping requires review`.
- Shared workflows, UI screens, and NFRs are shown at module level; do not assume every reference applies to every atomic CAP.
