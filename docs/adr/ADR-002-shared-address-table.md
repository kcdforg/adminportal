# ADR-002: Shared Address Table

## Status

Accepted

## Date

2026-07-03

## Context

Multiple entity types in the system need to store address information:
- Families have a home address
- Trainers have an optional personal/office address
- Future entity types (e.g., organizational contacts) may also need addresses

Three approaches were considered:

1. **Inline address fields on each table** — duplicate `address_line_1`, `city`, etc. columns directly on `families`, `trainers`, etc.
2. **Polymorphic address table** — single `addresses` table with `addressable_type` and `addressable_id` columns pointing back to the owner
3. **Standalone address table with FK on owner** — `addresses` table stores only address data; owner tables hold `address_id` FK

## Decision

Use a **standalone `addresses` table** with `address_id` FK on each owner table.

```sql
-- addresses has no owner references
CREATE TABLE addresses (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    address_label  VARCHAR(50),
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255),
    city           VARCHAR(100) NOT NULL,
    state          VARCHAR(100),
    postal_code    VARCHAR(20),
    country        VARCHAR(100) NOT NULL DEFAULT 'India',
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);

-- Owner tables carry the FK
ALTER TABLE families ADD COLUMN address_id BIGINT UNSIGNED NULL REFERENCES addresses(id);
ALTER TABLE trainers ADD COLUMN address_id BIGINT UNSIGNED NULL REFERENCES addresses(id);
```

## Rationale

- The polymorphic pattern (approach 2) adds complexity: you cannot enforce FK constraints on `addressable_id` because MySQL cannot have a FK that points to different tables conditionally.
- Inline fields (approach 1) duplicate schema across tables and make it impossible to share an address between entities.
- The standalone table (approach 3) is clean, normalized, and FK-enforced. The address table has no awareness of who owns it — ownership is defined at the consumer level.
- The same address record can be shared between multiple entities if needed (e.g., a trainer who lives at the same address as their family).

## Consequences

**Positive:**
- Address data is fully normalized — no duplication
- FK constraints are properly enforced
- Adding a new owner type requires only adding an `address_id` column to the new table
- Address records can be reused/shared across entities

**Negative:**
- Querying an entity with its address requires a JOIN
- Deleting or reassigning an address requires checking all references (no cascading since `address_id` is nullable)

## Usage Pattern

When creating a family or trainer with an address:
1. Create the address record first → get `address_id`
2. Set `address_id` on the family/trainer record

When updating an address:
- Update the `addresses` record directly (all entities referencing it see the update)
- Or create a new address and update `address_id` on the owner (preserves history)
