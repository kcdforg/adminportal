# KCDF Parents — Full Database Reference

## Conventions

- All primary keys: `BIGINT UNSIGNED AUTO_INCREMENT`
- All timestamps: `TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`
- `updated_at`: `TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`
- Soft deletes: via `status` ENUM. Never hard-delete core records.
- Foreign keys: indexed on the child table column
- Character set: `utf8mb4`, collation: `utf8mb4_unicode_ci`
- JSON columns: used only for genuinely flexible metadata, never for queryable fields

---

## Table Index

| # | Table | Module | Description |
|---|---|---|---|
| 1 | `member_profiles` | Auth | Universal person record |
| 2 | `user_logins` | Auth | Login credentials per person |
| 3 | `addresses` | Shared | Reusable address store |
| 4 | `families` | Families | Household/family record |
| 5 | `family_members` | Families | Profile-to-family membership |
| 6 | `trainers` | Families | Trainer role record |
| 7 | `admins` | Families | Admin role record |
| 8 | `entities` | Families | Generic institution store |
| 9 | `entity_member_relations` | Families | Profile-to-entity relationship |
| 10 | `programs` | Academics | Class/workshop/camp/event definition |
| 11 | `student_batches` | Academics | Batch of a program |
| 12 | `batch_members` | Academics | Members enrolled in a batch |
| 13 | `batch_sessions` | Academics | Individual session within a batch |
| 14 | `attendance` | Academics | Per-session attendance record |
| 15 | `enrollments` | Academics | Family enrolls a member into a batch |
| 16 | `payments` | Payments | Payment transactions |
| 17 | `parent_groups` | Community | Community discussion groups |
| 18 | `group_members` | Community | Group membership |
| 19 | `invitations` | Community | Parent-to-parent invitations |
| 20 | `notifications` | Notifications | In-app/push/email notifications |
| 21 | `activity_logs` | Notifications | Audit trail |

---

## 1. member_profiles

Universal person record. Reused by family members, trainers, admins, and any future role.

```sql
CREATE TABLE member_profiles (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name     VARCHAR(100) NOT NULL,
    middle_name    VARCHAR(100) NULL,
    last_name      VARCHAR(100) NOT NULL,
    date_of_birth  DATE NULL,
    gender         ENUM('male', 'female', 'other') NULL,
    mobile         VARCHAR(20) NULL,
    email          VARCHAR(255) NULL,
    photo_url      VARCHAR(500) NULL,
    blood_group    VARCHAR(10) NULL,
    status         ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_mobile (mobile),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 2. user_logins

One login record per person. A profile without a login record cannot authenticate.

```sql
CREATE TABLE user_logins (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id     BIGINT UNSIGNED NOT NULL,
    username       VARCHAR(100) NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at  TIMESTAMP NULL,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_username (username),
    INDEX idx_profile_id (profile_id),
    CONSTRAINT fk_user_logins_profile FOREIGN KEY (profile_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. addresses

Standalone address store. No owner references. Owner tables reference this via `address_id` FK.

```sql
CREATE TABLE addresses (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    address_label  VARCHAR(50) NULL DEFAULT 'home',
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255) NULL,
    city           VARCHAR(100) NOT NULL,
    state          VARCHAR(100) NULL,
    postal_code    VARCHAR(20) NULL,
    country        VARCHAR(100) NOT NULL DEFAULT 'India',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_city (city),
    INDEX idx_state (state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. families

Represents a household. One family may have multiple members. One address via FK.

```sql
CREATE TABLE families (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    family_code  VARCHAR(50) NOT NULL,
    family_name  VARCHAR(255) NOT NULL,
    address_id   BIGINT UNSIGNED NULL,
    status       ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_family_code (family_code),
    INDEX idx_status (status),
    INDEX idx_address_id (address_id),
    CONSTRAINT fk_families_address FOREIGN KEY (address_id) REFERENCES addresses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 5. family_members

Links member profiles to families with relationship type and role.

```sql
CREATE TABLE family_members (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    family_id         BIGINT UNSIGNED NOT NULL,
    profile_id        BIGINT UNSIGNED NOT NULL,
    relationship_type ENUM('father', 'mother', 'guardian', 'child') NOT NULL,
    member_role       ENUM('primary', 'normal', 'student') NOT NULL DEFAULT 'normal',
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_family_profile (family_id, profile_id),
    INDEX idx_family_id (family_id),
    INDEX idx_profile_id (profile_id),
    CONSTRAINT fk_family_members_family FOREIGN KEY (family_id) REFERENCES families(id),
    CONSTRAINT fk_family_members_profile FOREIGN KEY (profile_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 6. trainers

Trainer role record. References member_profiles for identity. Optional address via FK.

```sql
CREATE TABLE trainers (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id        BIGINT UNSIGNED NOT NULL,
    trainer_code      VARCHAR(50) NOT NULL,
    specialization    VARCHAR(255) NULL,
    experience_years  TINYINT UNSIGNED NULL DEFAULT 0,
    bio               TEXT NULL,
    joined_at         DATE NULL,
    address_id        BIGINT UNSIGNED NULL,
    status            ENUM('active', 'inactive', 'on_leave') NOT NULL DEFAULT 'active',
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_trainer_code (trainer_code),
    INDEX idx_profile_id (profile_id),
    INDEX idx_address_id (address_id),
    INDEX idx_status (status),
    CONSTRAINT fk_trainers_profile FOREIGN KEY (profile_id) REFERENCES member_profiles(id),
    CONSTRAINT fk_trainers_address FOREIGN KEY (address_id) REFERENCES addresses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 7. admins

Admin role record. References member_profiles for identity.

```sql
CREATE TABLE admins (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL,
    admin_role ENUM('super_admin', 'program_manager', 'accounts', 'readonly') NOT NULL DEFAULT 'readonly',
    status     ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_profile_id (profile_id),
    INDEX idx_admin_role (admin_role),
    CONSTRAINT fk_admins_profile FOREIGN KEY (profile_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Admin role access levels:
- `super_admin` — full system access
- `program_manager` — manage programs, batches, sessions, trainers
- `accounts` — manage payments, view enrollments
- `readonly` — read-only access to all data

---

## 8. entities

Generic institution store. Replaces separate tables for schools, colleges, hospitals, etc.

```sql
CREATE TABLE entities (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('school', 'college', 'organization', 'hospital', 'association') NOT NULL,
    name        VARCHAR(255) NOT NULL,
    city        VARCHAR(100) NULL,
    state       VARCHAR(100) NULL,
    country     VARCHAR(100) NULL DEFAULT 'India',
    meta        JSON NULL,
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_entity_type (entity_type),
    INDEX idx_status (status),
    INDEX idx_city (city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 9. entity_member_relations

Links member profiles to entities (e.g., student studies at school X).

```sql
CREATE TABLE entity_member_relations (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id        BIGINT UNSIGNED NOT NULL,
    entity_id        BIGINT UNSIGNED NOT NULL,
    relation_type    ENUM('studies_at', 'works_at', 'member_of', 'volunteer_at') NOT NULL,
    start_date       DATE NULL,
    end_date         DATE NULL,
    is_current       TINYINT(1) NOT NULL DEFAULT 1,
    relation_context JSON NULL,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_member_id (member_id),
    INDEX idx_entity_id (entity_id),
    INDEX idx_is_current (is_current),
    CONSTRAINT fk_emr_member FOREIGN KEY (member_id) REFERENCES member_profiles(id),
    CONSTRAINT fk_emr_entity FOREIGN KEY (entity_id) REFERENCES entities(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

`relation_context` JSON examples: `{"grade": "9", "section": "A"}`, `{"designation": "Teacher", "department": "Science"}`

---

## 10. programs

A program is any learning/activity offering: class, workshop, camp, or event.

```sql
CREATE TABLE programs (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(255) NOT NULL,
    program_type ENUM('class', 'workshop', 'camp', 'event') NOT NULL,
    description  TEXT NULL,
    age_group    VARCHAR(100) NULL,
    fee_amount   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status       ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_program_type (program_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 11. student_batches

A batch is a scheduled run of a program, assigned to a trainer with a fixed capacity.

```sql
CREATE TABLE student_batches (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_id BIGINT UNSIGNED NOT NULL,
    batch_name VARCHAR(255) NOT NULL,
    capacity   SMALLINT UNSIGNED NULL,
    trainer_id BIGINT UNSIGNED NULL,
    start_date DATE NULL,
    end_date   DATE NULL,
    status     ENUM('upcoming', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_program_id (program_id),
    INDEX idx_trainer_id (trainer_id),
    INDEX idx_status (status),
    CONSTRAINT fk_batches_program FOREIGN KEY (program_id) REFERENCES programs(id),
    CONSTRAINT fk_batches_trainer FOREIGN KEY (trainer_id) REFERENCES trainers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 12. batch_members

Members assigned to a batch.

```sql
CREATE TABLE batch_members (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id   BIGINT UNSIGNED NOT NULL,
    member_id  BIGINT UNSIGNED NOT NULL,
    joined_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status     ENUM('active', 'dropped', 'completed') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_batch_member (batch_id, member_id),
    INDEX idx_batch_id (batch_id),
    INDEX idx_member_id (member_id),
    CONSTRAINT fk_batch_members_batch FOREIGN KEY (batch_id) REFERENCES student_batches(id),
    CONSTRAINT fk_batch_members_member FOREIGN KEY (member_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 13. batch_sessions

Individual sessions within a batch.

```sql
CREATE TABLE batch_sessions (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id          BIGINT UNSIGNED NOT NULL,
    session_number    SMALLINT UNSIGNED NULL,
    session_title     VARCHAR(255) NULL,
    session_date      DATE NOT NULL,
    start_time        TIME NULL,
    end_time          TIME NULL,
    session_type      ENUM('regular', 'special', 'exam', 'workshop') NOT NULL DEFAULT 'regular',
    status            ENUM('scheduled', 'completed', 'cancelled', 'postponed') NOT NULL DEFAULT 'scheduled',
    trainer_id        BIGINT UNSIGNED NULL,
    topics_covered    TEXT NULL,
    homework          TEXT NULL,
    notes             TEXT NULL,
    attendance_locked TINYINT(1) NOT NULL DEFAULT 0,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_batch_id (batch_id),
    INDEX idx_session_date (session_date),
    INDEX idx_status (status),
    INDEX idx_trainer_id (trainer_id),
    CONSTRAINT fk_sessions_batch FOREIGN KEY (batch_id) REFERENCES student_batches(id),
    CONSTRAINT fk_sessions_trainer FOREIGN KEY (trainer_id) REFERENCES trainers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 14. attendance

Session-based attendance. One record per member per session.

```sql
CREATE TABLE attendance (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_session_id    BIGINT UNSIGNED NOT NULL,
    member_id           BIGINT UNSIGNED NOT NULL,
    attendance_status   ENUM('present', 'absent', 'late', 'excused') NOT NULL,
    remarks             VARCHAR(500) NULL,
    marked_by_member_id BIGINT UNSIGNED NULL,
    marked_at           TIMESTAMP NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_session_member (batch_session_id, member_id),
    INDEX idx_batch_session_id (batch_session_id),
    INDEX idx_member_id (member_id),
    INDEX idx_attendance_status (attendance_status),
    CONSTRAINT fk_attendance_session FOREIGN KEY (batch_session_id) REFERENCES batch_sessions(id),
    CONSTRAINT fk_attendance_member FOREIGN KEY (member_id) REFERENCES member_profiles(id),
    CONSTRAINT fk_attendance_marked_by FOREIGN KEY (marked_by_member_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 15. enrollments

Records a family enrolling a member into a batch. Tracks payment status.

```sql
CREATE TABLE enrollments (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    family_id             BIGINT UNSIGNED NOT NULL,
    member_id             BIGINT UNSIGNED NOT NULL,
    batch_id              BIGINT UNSIGNED NOT NULL,
    enrolled_by_member_id BIGINT UNSIGNED NULL,
    enrolled_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status                ENUM('pending', 'active', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    payment_status        ENUM('unpaid', 'partial', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    fee_amount            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_member_batch (member_id, batch_id),
    INDEX idx_family_id (family_id),
    INDEX idx_member_id (member_id),
    INDEX idx_batch_id (batch_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    CONSTRAINT fk_enrollments_family FOREIGN KEY (family_id) REFERENCES families(id),
    CONSTRAINT fk_enrollments_member FOREIGN KEY (member_id) REFERENCES member_profiles(id),
    CONSTRAINT fk_enrollments_batch FOREIGN KEY (batch_id) REFERENCES student_batches(id),
    CONSTRAINT fk_enrollments_enrolled_by FOREIGN KEY (enrolled_by_member_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 16. payments

Payment transactions. Supports class fees, donations, event fees, and refunds.

```sql
CREATE TABLE payments (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    family_id             BIGINT UNSIGNED NOT NULL,
    enrollment_id         BIGINT UNSIGNED NULL,
    payment_type          ENUM('class_fee', 'donation', 'event_fee', 'refund') NOT NULL,
    amount                DECIMAL(10,2) NOT NULL,
    payment_method        ENUM('cash', 'bank_transfer', 'upi', 'card', 'cheque', 'online') NOT NULL,
    transaction_reference VARCHAR(255) NULL,
    status                ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    notes                 TEXT NULL,
    paid_at               TIMESTAMP NULL,
    created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_family_id (family_id),
    INDEX idx_enrollment_id (enrollment_id),
    INDEX idx_payment_type (payment_type),
    INDEX idx_status (status),
    INDEX idx_paid_at (paid_at),
    CONSTRAINT fk_payments_family FOREIGN KEY (family_id) REFERENCES families(id),
    CONSTRAINT fk_payments_enrollment FOREIGN KEY (enrollment_id) REFERENCES enrollments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 17. parent_groups

Community groups for parents.

```sql
CREATE TABLE parent_groups (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_name  VARCHAR(255) NOT NULL,
    description TEXT NULL,
    visibility  ENUM('public', 'private', 'invite_only') NOT NULL DEFAULT 'public',
    status      ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_visibility (visibility),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 18. group_members

Membership of a profile in a parent group.

```sql
CREATE TABLE group_members (
    id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id  BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status    ENUM('active', 'left', 'banned') NOT NULL DEFAULT 'active',
    UNIQUE KEY uq_group_member (group_id, member_id),
    INDEX idx_group_id (group_id),
    INDEX idx_member_id (member_id),
    CONSTRAINT fk_group_members_group FOREIGN KEY (group_id) REFERENCES parent_groups(id),
    CONSTRAINT fk_group_members_member FOREIGN KEY (member_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 19. invitations

Parent-to-parent invitations via mobile or email.

```sql
CREATE TABLE invitations (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invited_by_member_id BIGINT UNSIGNED NOT NULL,
    invite_mobile        VARCHAR(20) NULL,
    invite_email         VARCHAR(255) NULL,
    invite_code          VARCHAR(50) NOT NULL,
    status               ENUM('pending', 'accepted', 'expired', 'cancelled') NOT NULL DEFAULT 'pending',
    sent_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    accepted_at          TIMESTAMP NULL,
    UNIQUE KEY uq_invite_code (invite_code),
    INDEX idx_invited_by (invited_by_member_id),
    INDEX idx_status (status),
    CONSTRAINT fk_invitations_invited_by FOREIGN KEY (invited_by_member_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 20. notifications

Per-member notifications. Supports in-app, push, and email types.

```sql
CREATE TABLE notifications (
    id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    title     VARCHAR(255) NOT NULL,
    message   TEXT NOT NULL,
    type      ENUM('push', 'email', 'in_app') NOT NULL DEFAULT 'in_app',
    status    ENUM('unread', 'read', 'archived') NOT NULL DEFAULT 'unread',
    read_at   TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_member_id (member_id),
    INDEX idx_status (status),
    INDEX idx_type (type),
    CONSTRAINT fk_notifications_member FOREIGN KEY (member_id) REFERENCES member_profiles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 21. activity_logs

Full audit trail of all mutations. Never deleted. `actor_profile_id` is NULL for system actions.

```sql
CREATE TABLE activity_logs (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_profile_id BIGINT UNSIGNED NULL,
    action           VARCHAR(100) NOT NULL,
    entity_type      VARCHAR(100) NOT NULL,
    entity_id        BIGINT UNSIGNED NOT NULL,
    old_values       JSON NULL,
    new_values       JSON NULL,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_actor_profile_id (actor_profile_id),
    INDEX idx_entity_type_id (entity_type, entity_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Index Strategy

- All FK columns have an index
- All `status` columns have an index (heavily used in WHERE clauses)
- `session_date`, `paid_at`, `created_at` indexed for date-range reporting queries
- Composite unique keys prevent duplicate memberships and attendance records

## Soft Delete Strategy

No `DELETE` statements on core records. Use `status` transitions:
- Set `status = 'inactive'` or `status = 'cancelled'` instead of deleting
- `activity_logs` is append-only — never soft-deleted
- `notifications` use `status = 'archived'` for dismiss

## Reporting Considerations

- Use `created_at` indexes for date range reports
- `payment_type`, `status` on `payments` support financial summaries
- `attendance_status` on `attendance` supports attendance rate calculations
- `enrollment.payment_status` cached from payment records for quick dashboard queries
