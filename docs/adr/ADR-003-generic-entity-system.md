# ADR-003: Generic Entity and Relationship System

## Status

Accepted

## Date

2026-07-03

## Context

Members of KCDF need to record their associations with external institutions:
- A child studies at a school
- A parent works at a company
- A member is part of an external association
- Future: a member volunteers at an NGO, receives treatment at a hospital

Two approaches were considered:

1. **Separate tables per institution type** — `schools`, `colleges`, `organizations`, `hospitals`, each with their own columns and a separate relationship table per type
2. **Generic entity table** — single `entities` table with `entity_type` to differentiate, and a single `entity_member_relations` table for all relationships

## Decision

Use a **generic entity + relationship system**.

```sql
CREATE TABLE entities (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('school', 'college', 'organization', 'hospital', 'association') NOT NULL,
    name        VARCHAR(255) NOT NULL,
    city        VARCHAR(100),
    state       VARCHAR(100),
    country     VARCHAR(100) DEFAULT 'India',
    meta        JSON,   -- entity-type-specific data
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

CREATE TABLE entity_member_relations (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id        BIGINT UNSIGNED NOT NULL,  -- FK → member_profiles
    entity_id        BIGINT UNSIGNED NOT NULL,  -- FK → entities
    relation_type    ENUM('studies_at', 'works_at', 'member_of', 'volunteer_at') NOT NULL,
    start_date       DATE,
    end_date         DATE,
    is_current       TINYINT(1) NOT NULL DEFAULT 1,
    relation_context JSON,  -- grade, section, designation, department, etc.
    created_at       TIMESTAMP,
    updated_at       TIMESTAMP
);
```

## Rationale

- The number of institution types is open-ended. New types (temples, NGOs, clubs) would require new tables under the separate-tables approach, each needing migrations and new API endpoints.
- Most institution data is the same regardless of type: a name, a location, and optional metadata. The `meta` JSON field handles type-specific data without schema changes.
- Relationship context (grade, designation, etc.) varies by relation type. Storing this in `relation_context` JSON is appropriate since it is not queried — it is displayed alongside the relationship.
- The generic approach reduces the schema to 2 tables instead of 10+, simplifying the codebase significantly.

## Consequences

**Positive:**
- Adding a new entity type requires only adding an ENUM value — no new table or migration
- Single API endpoint handles all institution types (`/api/v1/entities?entity_type=school`)
- Relationship data is centralized in one table
- `meta` and `relation_context` JSON fields provide flexibility without schema bloat

**Negative:**
- Type-specific queries require filtering by `entity_type` — slightly less efficient than a dedicated table
- The `meta` JSON field is not queryable with standard indexes — advanced search on institution metadata requires full-text or JSON path queries
- The `entity_type` ENUM must be updated in a migration when a new type is added (minor migration, not a schema redesign)

## JSON Field Usage

`entities.meta` examples by entity_type:
- `school`: `{"board": "CBSE", "type": "private"}`
- `hospital`: `{"specialty": "Pediatrics", "beds": 200}`
- `organization`: `{"industry": "IT", "size": "SME"}`

`entity_member_relations.relation_context` examples by relation_type:
- `studies_at`: `{"grade": "9", "section": "A", "roll_number": "25"}`
- `works_at`: `{"designation": "Software Engineer", "department": "Engineering"}`
- `member_of`: `{"role": "Treasurer"}`
- `volunteer_at`: `{"area": "Education"}`

## Future Considerations

If a specific entity type grows complex enough to need its own queryable fields (e.g., schools need a board-specific search), a dedicated extension table can be created:
```
school_details: id, entity_id FK, board, affiliation_number, ...
```
This extends the generic entity without breaking the generic system.
