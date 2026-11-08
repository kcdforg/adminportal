Design a production-ready architecture, database schema, backend structure, frontend architecture, and scalability plan for a system called “KCDF Parents”.

--------------------------------------------------
SYSTEM OVERVIEW
--------------------------------------------------

The platform consists of:

1. Parent Application
2. Admin Portal
3. Shared Slim Framework API Backend

--------------------------------------------------
TECH STACK
--------------------------------------------------

Frontend:
- Angular
- Ionic + Angular for Parent App
- Angular SPA for Admin Portal
- Angular Material for Admin UI

Backend:
- Slim Framework 4
- REST API architecture
- JWT authentication

Database:
- MySQL 8

Future:
- Redis caching
- Queue workers
- Horizontal scaling
- Future microservice extraction

--------------------------------------------------
DEPLOYMENT ARCHITECTURE
--------------------------------------------------

parents.xyz.com
    → Ionic Angular App

admin.xyz.com
    → Angular Admin SPA

api.xyz.com
    → Slim Framework API Backend

--------------------------------------------------
PROJECT STRUCTURE
--------------------------------------------------

Use SEPARATE frontend applications.

Recommended repositories/projects:

kcdf-api-backend
    → Slim Framework API

kcdf-admin-app
    → Angular Admin SPA

kcdf-parents-app
    → Ionic + Angular

Do NOT embed Angular applications inside Slim project.

Slim backend should remain API-only.

--------------------------------------------------
IMPORTANT ARCHITECTURAL PRINCIPLES
--------------------------------------------------

1. Use SINGLE monolithic Slim API backend initially
2. Internally modularize by business domains
3. Design APIs cleanly for future microservice extraction
4. Avoid over-engineering
5. Avoid premature microservices
6. Use relational modeling where appropriate
7. Use generic reusable entity system
8. Use JSON fields only where flexibility is useful
9. Keep architecture scalable and maintainable
10. Design for long-term production use

--------------------------------------------------
BACKEND ARCHITECTURE
--------------------------------------------------

Use modular monolith architecture inside Slim.

Recommended structure:

/src
    /Modules
        /Auth
        /Families
        /Academics
        /Payments
        /Notifications
        /Community

Each module should contain:
- Controllers
- Services
- Repositories
- Models
- Validators
- Policies
- DTOs

Use:
- service layer architecture
- thin controllers
- business logic inside services
- clean separation of concerns

--------------------------------------------------
FUTURE MICROSERVICE READINESS
--------------------------------------------------

The architecture should support future extraction into independent services such as:
- Auth Service
- Family Service
- Academic Service
- Payment Service
- Notification Service

Each future microservice may later become:
- separate Slim application
- separate deployment
- separate scaling unit

Prepare the monolith accordingly.

--------------------------------------------------
SCALING STRATEGY
--------------------------------------------------

Initial Architecture:
- Single Slim API backend
- Single MySQL database

Future Scaling:
- Load balancer
- Multiple Slim API instances
- Redis caching
- Queue workers
- MySQL read replicas
- Event-driven modules
- Future microservices

--------------------------------------------------
CORE DOMAIN MODEL
--------------------------------------------------

Use PERSON-BASED ARCHITECTURE.

Separate:
- PERSON identity
from:
- BUSINESS roles

--------------------------------------------------
COMMON PROFILE MODEL
--------------------------------------------------

Use:

member_profiles

This represents a PERSON and should be reusable for:
- family members
- trainers
- admins
- volunteers
- future roles

--------------------------------------------------
MEMBER PROFILES
--------------------------------------------------

member_profiles fields:
- id
- first_name
- middle_name
- last_name
- date_of_birth
- gender
- mobile
- email
- photo_url
- blood_group
- status
- created_at
- updated_at

--------------------------------------------------
AUTHENTICATION
--------------------------------------------------

Each person should have individual login.

Examples:
- father login
- mother login
- guardian login
- student login
- trainer login
- admin login

Use:

user_logins
- id
- profile_id
- username
- password_hash
- is_active
- last_login_at
- created_at
- updated_at

Use:
- JWT authentication
- refresh tokens support
- role-aware authorization

--------------------------------------------------
ADDRESSES (SHARED / REUSABLE)
--------------------------------------------------

Use a standalone addresses table that stores address data only.
The table has no knowledge of who owns the address.

Use:

addresses
- id
- address_label (e.g. home, office, primary)
- address_line_1
- address_line_2
- city
- state
- postal_code
- country
- created_at
- updated_at

Owner tables (families, trainers, and any future tables) reference this table via address_id.
The same address record can be shared across multiple entities if needed.
Ownership and association are defined at the owner table level, not inside addresses.

--------------------------------------------------
FAMILY MODEL
--------------------------------------------------

families
- represents a household/family

Fields:
- id
- family_code
- family_name
- address_id (FK → addresses)
- status
- created_at
- updated_at

--------------------------------------------------
FAMILY MEMBERS
--------------------------------------------------

family_members
- links profiles to family

Fields:
- id
- family_id
- profile_id

relationship_type:
- father
- mother
- guardian
- child

member_role:
- primary
- normal
- student

Rules:
- primary → full family access
- normal → normal family access
- student → self access only

--------------------------------------------------
TRAINERS
--------------------------------------------------

Trainers must be modeled separately.

Use:

trainers
- id
- profile_id
- trainer_code
- specialization
- experience_years
- bio
- joined_at
- address_id (FK → addresses)
- status
- created_at
- updated_at

Important:
- trainer reuses member_profiles
- trainer may or may not belong to family
- parent can later become trainer
- trainer address is stored via address_id, independent of any family address

--------------------------------------------------
ADMINS
--------------------------------------------------

admins
- id
- profile_id
- admin_role
- status
- created_at
- updated_at

--------------------------------------------------
GENERIC ENTITY SYSTEM
--------------------------------------------------

Use a reusable generic entity table instead of separate:
- schools
- colleges
- organizations
- institutions
- hospitals

Use:

entities
- id
- entity_type
- name
- city
- state
- country
- meta JSON
- status
- created_at
- updated_at

Examples of entity_type:
- school
- college
- organization
- hospital
- association

--------------------------------------------------
GENERIC RELATIONSHIP SYSTEM
--------------------------------------------------

Use generic relationships between profiles and entities.

Use:

entity_member_relations
- id
- member_id
- entity_id
- relation_type
- start_date
- end_date
- is_current
- relation_context JSON
- created_at
- updated_at

Examples of relation_type:
- studies_at
- works_at
- member_of
- volunteer_at

relation_context examples:
- grade
- section
- designation
- department

--------------------------------------------------
ACADEMIC DOMAIN
--------------------------------------------------

Programs represent:
- classes
- workshops
- camps
- events

--------------------------------------------------
PROGRAMS
--------------------------------------------------

programs
- id
- program_name
- program_type
- description
- age_group
- fee_amount
- status
- created_at
- updated_at

--------------------------------------------------
STUDENT BATCHES
--------------------------------------------------

student_batches
- id
- program_id
- batch_name
- capacity
- trainer_id
- start_date
- end_date
- status
- created_at
- updated_at

--------------------------------------------------
BATCH MEMBERS
--------------------------------------------------

batch_members
- id
- batch_id
- member_id
- joined_at
- status
- created_at
- updated_at

--------------------------------------------------
BATCH SESSIONS
--------------------------------------------------

The system should support:
- daily sessions
- weekly sessions
- adhoc sessions
- cancelled sessions
- special sessions

Use:

batch_sessions
- id
- batch_id
- session_number
- session_title
- session_date
- start_time
- end_time

session_type:
- regular
- special
- exam
- workshop

status:
- scheduled
- completed
- cancelled
- postponed

- trainer_id
- topics_covered TEXT
- homework TEXT
- notes TEXT
- attendance_locked BOOLEAN
- created_at
- updated_at

--------------------------------------------------
ATTENDANCE
--------------------------------------------------

Attendance must be session-based.

Use:

attendance
- id
- batch_session_id
- member_id

attendance_status:
- present
- absent
- late
- excused

- remarks
- marked_by_member_id
- marked_at
- created_at
- updated_at

--------------------------------------------------
ENROLLMENTS
--------------------------------------------------

enrollments
- id
- family_id
- member_id
- batch_id
- enrolled_by_member_id
- enrolled_at
- status
- payment_status
- fee_amount
- created_at
- updated_at

--------------------------------------------------
PAYMENTS
--------------------------------------------------

The payment system must support:
- class fees
- donations
- event fees
- partial payments
- refunds
- multiple transactions

Use:

payments
- id
- family_id
- enrollment_id
- payment_type
- amount
- payment_method
- transaction_reference
- status
- paid_at
- created_at
- updated_at

--------------------------------------------------
COMMUNITY FEATURES
--------------------------------------------------

parent_groups
- id
- group_name
- description
- visibility
- status
- created_at
- updated_at

group_members
- id
- group_id
- member_id
- joined_at
- status

--------------------------------------------------
INVITATIONS
--------------------------------------------------

Parents can invite other parents.

Use:

invitations
- id
- invited_by_member_id
- invite_mobile
- invite_email
- invite_code
- status
- sent_at
- accepted_at

--------------------------------------------------
NOTIFICATIONS
--------------------------------------------------

notifications
- id
- member_id
- title
- message
- type
- status
- read_at
- created_at

Supports:
- push notifications
- email
- in-app notifications

--------------------------------------------------
AUDIT LOGGING
--------------------------------------------------

activity_logs
- id
- actor_profile_id
- action
- entity_type
- entity_id
- old_values JSON
- new_values JSON
- created_at

--------------------------------------------------
PARENT APPLICATION FEATURES
--------------------------------------------------

The Parent App should support:
- Login/logout
- Family management
- Add/edit family members
- Student enrollments
- Attendance viewing
- Batch/session schedules
- Payments/donations history
- Parent groups
- Notifications
- Invitations
- Future assignments/homework

Students should later be able to:
- login
- see attendance
- see schedules
- see assignments

--------------------------------------------------
ADMIN PORTAL FEATURES
--------------------------------------------------

The Admin Portal should support:
- Parent management
- Family management
- Trainer management
- Program management
- Batch management
- Session scheduling
- Attendance management
- Payment tracking
- Donation tracking
- Parent groups
- Notifications
- Reports
- Audit logs

--------------------------------------------------
ANGULAR FRONTEND ARCHITECTURE
--------------------------------------------------

Use Angular architecture suitable for enterprise applications.

Recommended:
- feature modules
- shared modules
- core module
- Angular Material
- reusable API service layer
- route guards
- interceptors
- typed DTO models
- reusable validators

Recommended Angular structure:

/src/app
    /core
    /shared
    /features

Use:
- standalone components
- Angular signals where appropriate
- centralized API layer
- JWT interceptors
- role-based route guards

--------------------------------------------------
API DESIGN EXPECTATIONS
--------------------------------------------------

Use RESTful API design.

Example routes:

/api/v1/auth/*
/api/v1/families/*
/api/v1/members/*
/api/v1/trainers/*
/api/v1/programs/*
/api/v1/batches/*
/api/v1/sessions/*
/api/v1/attendance/*
/api/v1/payments/*

Use:
- DTOs
- validation
- pagination
- filtering
- JWT middleware
- role-based access control

--------------------------------------------------
DATABASE EXPECTATIONS
--------------------------------------------------

Generate:
1. Full database schema
2. CREATE TABLE statements
3. Foreign keys
4. Index recommendations
5. Query optimization suggestions
6. Soft delete recommendations
7. Audit logging recommendations
8. Reporting considerations
9. Future scaling recommendations

--------------------------------------------------
IMPORTANT CONSTRAINTS
--------------------------------------------------

- Keep architecture practical
- Avoid unnecessary abstractions
- Avoid over-engineering
- Keep maintainability high
- Production-ready but realistic
- Suitable for long-term business growth