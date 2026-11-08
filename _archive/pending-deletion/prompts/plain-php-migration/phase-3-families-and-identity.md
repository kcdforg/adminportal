# Phase 3 — Families and Identity

## Context

Phases 0–2 are complete. Port the Slim Families module using the endpoint
inventory and plain-PHP structure. Keep auth/JWT handling in the shared
initialization; use `services/` only for reusable business rules or operations
that span records.

## Scope

- Member profiles
- Families and family membership
- Trainers and admins
- Addresses
- Entities and entity-member relations

Create endpoint files under `api/<resource>/` using the Phase 0 action names.
Do not recreate Slim's controller/repository/model/DTO structure.

## Preserve

- Existing access rules for admins, profile owners, trainers, primary/normal/
  student family members, and entity relations.
- Existing validation, status values, filtering, sorting, pagination, and
  response shapes.
- Family code generation, duplicate-membership prevention, primary-member
  rules, and trainer/admin profile eligibility.
- Address creation/linking and all multi-table operations atomically.
- Existing activity-log side effects and attribution.

For direct Medoo operations, keep uncomplicated CRUD in endpoint files.
Extract a service for reused logic, authorization/business invariants, or
multi-step operations. Use PDO transactions on the same Medoo PDO connection.
Validate dynamic sort columns and order values against allowlists.

## Exit checks

- Each in-scope registered route in the inventory has a direct endpoint.
- Tests cover role/resource access, validation, duplicates, primary membership,
  relationships, transaction rollback, pagination, and response format.
- The source schema and source Slim backend remain unchanged.
