# Programs

## Requirement summary

- This document captures the current working flow in plain language.
- It supports planning, review, and backlog alignment.
- Runtime validation is still required before sign-off.

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.


### MEM-PRG-002 — Display courses in Ionic app

**Type:** Functional
**Owner:** member-portal
**Legacy ID:** `PRG-002`
**Actor:** Authenticated administrators and members.

**Requirement:** The Ionic courses page displays sample course content; it is not yet an end-to-end API-backed program listing.

**Source-grounded acceptance review (static; runtime unverified):**
- **Authentication gate (source inspection):** Verify the behavior expressed by `$jwt = checkAuth();` at `api-backend-php/api/programs/index.php:7`. Runtime outcome not established.
- **API success response (source inspection):** Verify the behavior expressed by `jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);` at `api-backend-php/api/programs/index.php:10`. Runtime outcome not established.

**Source references:**
- `api-backend-php/api/programs/index.php`, `api-backend-php/api/programs/show.php`.

**Source review:** Evidence of code or a documented integration gap; not an end-to-end runtime verification.
**Implementation state:** unassessed (do not assume completed).