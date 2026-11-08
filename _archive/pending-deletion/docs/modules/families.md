# Families Module

## Scope

Manages families, addresses, family membership, trainers, admins, and the generic entity/relationship system.

---

## Tables

- `addresses` — standalone reusable address store
- `families` — household record
- `family_members` — profile-to-family membership
- `trainers` — trainer role
- `admins` — admin role
- `entities` — generic institution store (schools, colleges, etc.)
- `entity_member_relations` — profile-to-entity relationship

See `docs/01-database.md` for full schema with MySQL types.

---

## Business Rules

### Addresses
1. The `addresses` table stores address data only. It has no concept of who owns it.
2. Owner tables reference addresses via `address_id` FK.
3. An address record can be shared between multiple entities.
4. If a family or trainer has no address, `address_id` is NULL.

### Families
5. Each family has a unique `family_code` (e.g., KCDF-0042), auto-generated on creation.
6. A family must have at least one member with `member_role = primary`.
7. A family can have at most one `primary` member at a time.
8. A family can have multiple `father`, `mother`, `guardian` relationship types.
9. Only one `primary` member per family is allowed.

### Family Members (Access Rules)
10. `member_role = primary` — can view and edit all family data, enroll members, make payments.
11. `member_role = normal` — can view family data; cannot enroll or make payments.
12. `member_role = student` — can view only their own profile and schedule; no family data access.
13. A profile can be a member of multiple families (e.g., a guardian in two families).
14. The same profile cannot appear twice in the same family (unique constraint on family_id + profile_id).

### Trainers
15. A trainer record requires a valid `member_profiles` record.
16. A trainer may or may not be a member of a family — these are independent records.
17. A parent (family member) can simultaneously be a trainer.
18. Trainer codes are auto-generated and unique (e.g., TR-001).
19. A trainer without an address has `address_id = NULL`.

### Admins
20. An admin record requires a valid `member_profiles` record.
21. An admin can simultaneously be a family member or trainer.
22. `super_admin` has access to all system functions including user and role management.
23. `program_manager` can manage programs, batches, sessions, trainers, and attendance.
24. `accounts` can manage payments and view enrollment data.
25. `readonly` can view all data but cannot make any changes.

### Generic Entity System
26. Use `entities` for any institution type: school, college, organization, hospital, association.
27. The `meta` JSON field stores entity-specific data (e.g., board type for schools, specialty for hospitals).
28. `entity_member_relations` records which profile is associated with which entity and how.
29. `is_current = 1` means the relationship is active; `is_current = 0` means it is historical.
30. `relation_context` JSON captures contextual details (e.g., grade, section, designation, department).

---

## Access Control Matrix

| Action | family_primary | family_normal | family_student | trainer | admin |
|---|---|---|---|---|---|
| View own family | Yes | Yes | No | No | Yes |
| Edit family details | Yes | No | No | No | super/pm |
| Add family member | Yes | No | No | No | super/pm |
| Remove family member | Yes | No | No | No | super/pm |
| View trainer list | No | No | No | No | Yes |
| Create trainer | No | No | No | No | super/pm |
| Edit trainer | No | No | No | No | super/pm |
| View admin list | No | No | No | No | super |
| Create admin | No | No | No | No | super |
| View entities | Yes | Yes | No | Yes | Yes |
| Create entity | No | No | No | No | super/pm |

---

## API Endpoints

### Families

#### GET /api/v1/families
Admin only. Returns paginated list of all families.

**Query params:** `status`, `sort`, `order`, `page`, `per_page`

#### POST /api/v1/families
Admin only. Creates a new family (auto-generates `family_code`).

**Request:**
```json
{
  "family_name": "Rajan Family",
  "address": {
    "address_line_1": "12 Temple Street",
    "city": "Coimbatore",
    "state": "Tamil Nadu",
    "postal_code": "641001",
    "country": "India"
  }
}
```

#### GET /api/v1/families/{id}
Admin or primary family member of that family.

#### PUT /api/v1/families/{id}
Admin or primary family member. Updates family name and address.

#### GET /api/v1/families/{id}/members
Admin or any member of that family. Returns all members.

#### POST /api/v1/families/{id}/members
Admin or primary family member. Adds a profile to the family.

**Request:**
```json
{
  "profile_id": 15,
  "relationship_type": "mother",
  "member_role": "normal"
}
```

#### DELETE /api/v1/families/{id}/members/{profile_id}
Admin or primary family member. Removes a member from the family.

---

### Trainers

#### GET /api/v1/trainers
Admin only. Returns paginated trainer list.

#### POST /api/v1/trainers
Admin (super_admin, program_manager) only.

**Request:**
```json
{
  "profile_id": 20,
  "specialization": "Vedic Maths",
  "experience_years": 5,
  "bio": "Experienced trainer...",
  "joined_at": "2024-01-15",
  "address": {
    "address_line_1": "45 Park Road",
    "city": "Coimbatore",
    "state": "Tamil Nadu",
    "postal_code": "641002",
    "country": "India"
  }
}
```

#### GET /api/v1/trainers/{id}
Admin or the trainer themselves.

#### PUT /api/v1/trainers/{id}
Admin (super_admin, program_manager) or trainer themselves (limited fields: bio, specialization only).

---

### Members (Profiles)

#### GET /api/v1/members
Admin only.

#### POST /api/v1/members
Admin only. Creates a new member profile.

**Request:**
```json
{
  "first_name": "Priya",
  "middle_name": null,
  "last_name": "Kumar",
  "date_of_birth": "1990-03-22",
  "gender": "female",
  "mobile": "9876543210",
  "email": "priya@example.com",
  "blood_group": "B+"
}
```

#### GET /api/v1/members/{id}
Admin, the member themselves, or primary family member of the same family.

#### PUT /api/v1/members/{id}
Admin or the member themselves (own profile only).

---

### Entities

#### GET /api/v1/entities
Any authenticated user. Supports `entity_type` filter.

#### POST /api/v1/entities
Admin (super_admin, program_manager) only.

#### GET /api/v1/entities/{id}
Any authenticated user.

#### PUT /api/v1/entities/{id}
Admin only.

#### GET /api/v1/members/{id}/entity-relations
Admin or the member themselves.

#### POST /api/v1/members/{id}/entity-relations
Admin or primary family member of the member's family.

**Request:**
```json
{
  "entity_id": 5,
  "relation_type": "studies_at",
  "start_date": "2024-06-01",
  "is_current": true,
  "relation_context": {
    "grade": "7",
    "section": "B"
  }
}
```

---

## Validation Rules

| Field | Rule |
|---|---|
| `family_name` | required, string, max 255 chars |
| `relationship_type` | required, must be: father, mother, guardian, child |
| `member_role` | required, must be: primary, normal, student |
| `trainer.specialization` | optional, string, max 255 |
| `trainer.experience_years` | optional, integer, min 0, max 60 |
| `trainer.joined_at` | optional, valid date |
| `entity.entity_type` | required, must be valid ENUM value |
| `entity.name` | required, string, max 255 |
| `relation_type` | required, must be: studies_at, works_at, member_of, volunteer_at |
| `address.address_line_1` | required when address is provided |
| `address.city` | required when address is provided |
| `address.country` | required when address is provided |

---

## Module Folder Structure

```
src/Modules/Families/
├── Controllers/
│   ├── FamilyController.php
│   ├── FamilyMemberController.php
│   ├── MemberController.php
│   ├── TrainerController.php
│   ├── AdminController.php
│   └── EntityController.php
├── Services/
│   ├── FamilyService.php
│   ├── MemberService.php
│   ├── TrainerService.php
│   ├── AdminService.php
│   └── EntityService.php
├── Repositories/
│   ├── FamilyRepository.php
│   ├── FamilyMemberRepository.php
│   ├── MemberRepository.php
│   ├── TrainerRepository.php
│   ├── AdminRepository.php
│   ├── AddressRepository.php
│   └── EntityRepository.php
├── Models/
│   ├── Family.php
│   ├── FamilyMember.php
│   ├── Trainer.php
│   ├── Admin.php
│   ├── Address.php
│   └── Entity.php
├── DTOs/
│   ├── CreateFamilyDTO.php
│   ├── CreateMemberDTO.php
│   ├── CreateTrainerDTO.php
│   └── CreateEntityRelationDTO.php
├── Validators/
│   ├── FamilyValidator.php
│   ├── MemberValidator.php
│   └── TrainerValidator.php
└── Policies/
    ├── FamilyPolicy.php
    └── MemberPolicy.php
```
