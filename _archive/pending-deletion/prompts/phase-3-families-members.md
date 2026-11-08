# Phase 3 — Families + Members Module

## Context

You are continuing development of `kcdf-api-backend` (Slim Framework 4).
Phase 1 (schema) and Phase 2 (Slim scaffold + Auth) are already complete.

The project structure, middleware, `BaseController`, `BaseRepository`, and Eloquent connection are already in place.

This phase implements the **Families module** covering:
- `member_profiles` management (CRUD)
- `addresses` (create and link to families/trainers)
- `families` (CRUD + member management)
- `family_members` (add/remove members, role management)
- `trainers` (CRUD)
- `admins` (CRUD)
- `entities` (generic entity CRUD)
- `entity_member_relations` (link profiles to entities)

Do NOT implement Academics, Payments, Community, or Notifications in this phase.

---

## Module Location

```
src/Modules/Families/
├── Controllers/
│   ├── MemberController.php
│   ├── FamilyController.php
│   ├── FamilyMemberController.php
│   ├── TrainerController.php
│   ├── AdminController.php
│   └── EntityController.php
├── Services/
│   ├── MemberService.php
│   ├── FamilyService.php
│   ├── TrainerService.php
│   ├── AdminService.php
│   ├── AddressService.php
│   └── EntityService.php
├── Repositories/
│   ├── MemberRepository.php
│   ├── FamilyRepository.php
│   ├── FamilyMemberRepository.php
│   ├── TrainerRepository.php
│   ├── AdminRepository.php
│   ├── AddressRepository.php
│   └── EntityRepository.php
├── Models/
│   ├── MemberProfile.php       ← move from Auth module or share
│   ├── Family.php
│   ├── FamilyMember.php
│   ├── Trainer.php
│   ├── Admin.php
│   ├── Address.php
│   ├── Entity.php
│   └── EntityMemberRelation.php
├── DTOs/
│   ├── CreateMemberDTO.php
│   ├── CreateFamilyDTO.php
│   ├── AddFamilyMemberDTO.php
│   ├── CreateTrainerDTO.php
│   ├── CreateAdminDTO.php
│   ├── CreateEntityDTO.php
│   └── CreateEntityRelationDTO.php
├── Validators/
│   ├── MemberValidator.php
│   ├── FamilyValidator.php
│   ├── TrainerValidator.php
│   └── EntityValidator.php
├── Policies/
│   ├── FamilyPolicy.php
│   └── MemberPolicy.php
└── routes.php
```

---

## Endpoints to Implement

### Members
- GET /api/v1/members — Admin only, paginated, filter by status
- POST /api/v1/members — Admin only, create profile
- GET /api/v1/members/{id} — Admin or own profile or primary family member of same family
- PUT /api/v1/members/{id} — Admin or own profile only

### Families
- GET /api/v1/families — Admin only, paginated, filter by status
- POST /api/v1/families — Admin only, auto-generate family_code (format: KCDF-{zero-padded-id})
- GET /api/v1/families/{id} — Admin or primary member of that family
- PUT /api/v1/families/{id} — Admin or primary member of that family
- GET /api/v1/families/{id}/members — Admin or any member of that family
- POST /api/v1/families/{id}/members — Admin or primary member of that family
- DELETE /api/v1/families/{id}/members/{profile_id} — Admin or primary member of that family

### Trainers
- GET /api/v1/trainers — Admin only, paginated, filter by status
- POST /api/v1/trainers — Admin (super_admin, program_manager) only
- GET /api/v1/trainers/{id} — Admin or the trainer themselves
- PUT /api/v1/trainers/{id} — Admin only (or trainer limited to bio/specialization)

### Admins
- GET /api/v1/admins — super_admin only
- POST /api/v1/admins — super_admin only
- GET /api/v1/admins/{id} — super_admin only
- PUT /api/v1/admins/{id} — super_admin only

### Entities
- GET /api/v1/entities — Any authenticated user, filter by entity_type
- POST /api/v1/entities — Admin only
- GET /api/v1/entities/{id} — Any authenticated user
- PUT /api/v1/entities/{id} — Admin only

### Entity Relations
- GET /api/v1/members/{id}/entity-relations — Admin or own member
- POST /api/v1/members/{id}/entity-relations — Admin or primary family member of the member
- DELETE /api/v1/members/{id}/entity-relations/{relation_id} — Admin or primary family member

---

## Business Rules to Enforce

1. family_code auto-generated: format `KCDF-{id zero-padded to 4 digits}` — generate after insert using the new ID
2. A family must have exactly one `primary` member. Enforce on add: reject if a primary already exists when adding another primary.
3. Cannot add same profile twice to same family (unique constraint, return DUPLICATE_ENTRY error)
4. When creating a trainer: profile_id must exist and not already be a trainer
5. When creating an admin: profile_id must exist and not already be an admin
6. Addresses: when a family or trainer is created with an address object, create the address record first, then set address_id

---

## Address Handling Pattern

When creating a Family with an address:
```
POST /api/v1/families
{
  "family_name": "...",
  "address": {
    "address_line_1": "...",
    "city": "...",
    "state": "...",
    "postal_code": "...",
    "country": "India"
  }
}
```

- FamilyService creates the address row first via AddressRepository → gets address_id
- Then creates the family row with address_id set

When updating only address:
- If family.address_id is null → create a new address row, set address_id
- If family.address_id exists → update the existing address row in place

---

## Validation Rules

Members:
- first_name: required, string, max 100
- last_name: required, string, max 100
- email: optional, valid email, unique in member_profiles
- mobile: optional, digits only, 10 chars minimum
- gender: optional, must be: male, female, other
- date_of_birth: optional, valid date, not in future

Families:
- family_name: required, string, max 255
- address.address_line_1: required if address provided
- address.city: required if address provided

Family Members:
- profile_id: required, must exist
- relationship_type: required, one of: father, mother, guardian, child
- member_role: required, one of: primary, normal, student

Trainers:
- profile_id: required, must exist, must not already be a trainer
- specialization: optional, string, max 255
- experience_years: optional, integer, 0–60
- joined_at: optional, valid date

Entities:
- entity_type: required, one of: school, college, organization, hospital, association
- name: required, string, max 255

Entity Relations:
- entity_id: required, must exist
- relation_type: required, one of: studies_at, works_at, member_of, volunteer_at
- start_date: optional, valid date
- relation_context: optional, valid JSON object

---

## Rules

- All access control enforced in Policy classes, called from Services
- Services throw domain exceptions (e.g., UnauthorizedException, NotFoundException, DuplicateException)
- Controllers catch exceptions and map to appropriate HTTP responses
- No raw SQL — use Eloquent query builder through Repositories
- Paginate all list endpoints (default 20 per page, max 100)
