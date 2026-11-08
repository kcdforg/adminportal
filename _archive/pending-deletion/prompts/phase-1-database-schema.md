# Phase 1 — Database Schema

## Task

Generate a complete, production-ready MySQL 8 database schema for the KCDF Parents platform.

Output a single file: `schema.sql`

---

## What to Generate

Generate `CREATE TABLE` statements for all 21 tables listed below, in dependency order (no forward FK references).

Include for each table:
- Exact MySQL 8 column definitions with types, nullability, defaults
- PRIMARY KEY
- UNIQUE KEY constraints where noted
- INDEX on all FK columns and heavily-filtered columns
- FOREIGN KEY constraints with named constraints
- ENGINE=InnoDB, CHARSET=utf8mb4, COLLATE=utf8mb4_unicode_ci

Add a DROP TABLE IF EXISTS before each CREATE TABLE (in reverse dependency order at the top of the file).

---

## Table Order (dependency-safe)

1. `addresses`
2. `member_profiles`
3. `user_logins`
4. `families`
5. `family_members`
6. `trainers`
7. `admins`
8. `entities`
9. `entity_member_relations`
10. `programs`
11. `student_batches`
12. `batch_members`
13. `batch_sessions`
14. `attendance`
15. `enrollments`
16. `payments`
17. `parent_groups`
18. `group_members`
19. `invitations`
20. `notifications`
21. `activity_logs`

---

## Column Specifications

### addresses
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- address_label: VARCHAR(50) NULL DEFAULT 'home'
- address_line_1: VARCHAR(255) NOT NULL
- address_line_2: VARCHAR(255) NULL
- city: VARCHAR(100) NOT NULL
- state: VARCHAR(100) NULL
- postal_code: VARCHAR(20) NULL
- country: VARCHAR(100) NOT NULL DEFAULT 'India'
- created_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- updated_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
- INDEX: city, state

### member_profiles
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- first_name: VARCHAR(100) NOT NULL
- middle_name: VARCHAR(100) NULL
- last_name: VARCHAR(100) NOT NULL
- date_of_birth: DATE NULL
- gender: ENUM('male','female','other') NULL
- mobile: VARCHAR(20) NULL
- email: VARCHAR(255) NULL
- photo_url: VARCHAR(500) NULL
- blood_group: VARCHAR(10) NULL
- status: ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: mobile, email, status

### user_logins
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- profile_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- username: VARCHAR(100) NOT NULL UNIQUE
- password_hash: VARCHAR(255) NOT NULL
- is_active: TINYINT(1) NOT NULL DEFAULT 1
- last_login_at: TIMESTAMP NULL
- created_at, updated_at
- INDEX: profile_id

### families
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- family_code: VARCHAR(50) NOT NULL UNIQUE
- family_name: VARCHAR(255) NOT NULL
- address_id: BIGINT UNSIGNED NULL FK → addresses(id)
- status: ENUM('active','inactive') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: address_id, status

### family_members
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- family_id: BIGINT UNSIGNED NOT NULL FK → families(id)
- profile_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- relationship_type: ENUM('father','mother','guardian','child') NOT NULL
- member_role: ENUM('primary','normal','student') NOT NULL DEFAULT 'normal'
- created_at, updated_at
- UNIQUE KEY: (family_id, profile_id)
- INDEX: family_id, profile_id

### trainers
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- profile_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- trainer_code: VARCHAR(50) NOT NULL UNIQUE
- specialization: VARCHAR(255) NULL
- experience_years: TINYINT UNSIGNED NULL DEFAULT 0
- bio: TEXT NULL
- joined_at: DATE NULL
- address_id: BIGINT UNSIGNED NULL FK → addresses(id)
- status: ENUM('active','inactive','on_leave') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: profile_id, address_id, status

### admins
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- profile_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- admin_role: ENUM('super_admin','program_manager','accounts','readonly') NOT NULL DEFAULT 'readonly'
- status: ENUM('active','inactive') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: profile_id, admin_role

### entities
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- entity_type: ENUM('school','college','organization','hospital','association') NOT NULL
- name: VARCHAR(255) NOT NULL
- city: VARCHAR(100) NULL
- state: VARCHAR(100) NULL
- country: VARCHAR(100) NULL DEFAULT 'India'
- meta: JSON NULL
- status: ENUM('active','inactive') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: entity_type, status, city

### entity_member_relations
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- entity_id: BIGINT UNSIGNED NOT NULL FK → entities(id)
- relation_type: ENUM('studies_at','works_at','member_of','volunteer_at') NOT NULL
- start_date: DATE NULL
- end_date: DATE NULL
- is_current: TINYINT(1) NOT NULL DEFAULT 1
- relation_context: JSON NULL
- created_at, updated_at
- INDEX: member_id, entity_id, is_current

### programs
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- program_name: VARCHAR(255) NOT NULL
- program_type: ENUM('class','workshop','camp','event') NOT NULL
- description: TEXT NULL
- age_group: VARCHAR(100) NULL
- fee_amount: DECIMAL(10,2) NOT NULL DEFAULT 0.00
- status: ENUM('active','inactive','archived') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: program_type, status

### student_batches
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- program_id: BIGINT UNSIGNED NOT NULL FK → programs(id)
- batch_name: VARCHAR(255) NOT NULL
- capacity: SMALLINT UNSIGNED NULL
- trainer_id: BIGINT UNSIGNED NULL FK → trainers(id)
- start_date: DATE NULL
- end_date: DATE NULL
- status: ENUM('upcoming','active','completed','cancelled') NOT NULL DEFAULT 'upcoming'
- created_at, updated_at
- INDEX: program_id, trainer_id, status

### batch_members
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- batch_id: BIGINT UNSIGNED NOT NULL FK → student_batches(id)
- member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- joined_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- status: ENUM('active','dropped','completed') NOT NULL DEFAULT 'active'
- created_at, updated_at
- UNIQUE KEY: (batch_id, member_id)
- INDEX: batch_id, member_id

### batch_sessions
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- batch_id: BIGINT UNSIGNED NOT NULL FK → student_batches(id)
- session_number: SMALLINT UNSIGNED NULL
- session_title: VARCHAR(255) NULL
- session_date: DATE NOT NULL
- start_time: TIME NULL
- end_time: TIME NULL
- session_type: ENUM('regular','special','exam','workshop') NOT NULL DEFAULT 'regular'
- status: ENUM('scheduled','completed','cancelled','postponed') NOT NULL DEFAULT 'scheduled'
- trainer_id: BIGINT UNSIGNED NULL FK → trainers(id)
- topics_covered: TEXT NULL
- homework: TEXT NULL
- notes: TEXT NULL
- attendance_locked: TINYINT(1) NOT NULL DEFAULT 0
- created_at, updated_at
- INDEX: batch_id, session_date, status, trainer_id

### attendance
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- batch_session_id: BIGINT UNSIGNED NOT NULL FK → batch_sessions(id)
- member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- attendance_status: ENUM('present','absent','late','excused') NOT NULL
- remarks: VARCHAR(500) NULL
- marked_by_member_id: BIGINT UNSIGNED NULL FK → member_profiles(id)
- marked_at: TIMESTAMP NULL
- created_at, updated_at
- UNIQUE KEY: (batch_session_id, member_id)
- INDEX: batch_session_id, member_id, attendance_status

### enrollments
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- family_id: BIGINT UNSIGNED NOT NULL FK → families(id)
- member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- batch_id: BIGINT UNSIGNED NOT NULL FK → student_batches(id)
- enrolled_by_member_id: BIGINT UNSIGNED NULL FK → member_profiles(id)
- enrolled_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- status: ENUM('pending','active','cancelled','completed') NOT NULL DEFAULT 'pending'
- payment_status: ENUM('unpaid','partial','paid','refunded') NOT NULL DEFAULT 'unpaid'
- fee_amount: DECIMAL(10,2) NOT NULL DEFAULT 0.00
- created_at, updated_at
- UNIQUE KEY: (member_id, batch_id)
- INDEX: family_id, member_id, batch_id, status, payment_status

### payments
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- family_id: BIGINT UNSIGNED NOT NULL FK → families(id)
- enrollment_id: BIGINT UNSIGNED NULL FK → enrollments(id)
- payment_type: ENUM('class_fee','donation','event_fee','refund') NOT NULL
- amount: DECIMAL(10,2) NOT NULL
- payment_method: ENUM('cash','bank_transfer','upi','card','cheque','online') NOT NULL
- transaction_reference: VARCHAR(255) NULL
- status: ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending'
- notes: TEXT NULL
- paid_at: TIMESTAMP NULL
- created_at, updated_at
- INDEX: family_id, enrollment_id, payment_type, status, paid_at

### parent_groups
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- group_name: VARCHAR(255) NOT NULL
- description: TEXT NULL
- visibility: ENUM('public','private','invite_only') NOT NULL DEFAULT 'public'
- status: ENUM('active','inactive','archived') NOT NULL DEFAULT 'active'
- created_at, updated_at
- INDEX: visibility, status

### group_members
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- group_id: BIGINT UNSIGNED NOT NULL FK → parent_groups(id)
- member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- joined_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- status: ENUM('active','left','banned') NOT NULL DEFAULT 'active'
- UNIQUE KEY: (group_id, member_id)
- INDEX: group_id, member_id

### invitations
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- invited_by_member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- invite_mobile: VARCHAR(20) NULL
- invite_email: VARCHAR(255) NULL
- invite_code: VARCHAR(50) NOT NULL UNIQUE
- status: ENUM('pending','accepted','expired','cancelled') NOT NULL DEFAULT 'pending'
- sent_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- accepted_at: TIMESTAMP NULL
- INDEX: invited_by_member_id, status

### notifications
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- member_id: BIGINT UNSIGNED NOT NULL FK → member_profiles(id)
- title: VARCHAR(255) NOT NULL
- message: TEXT NOT NULL
- type: ENUM('push','email','in_app') NOT NULL DEFAULT 'in_app'
- status: ENUM('unread','read','archived') NOT NULL DEFAULT 'unread'
- read_at: TIMESTAMP NULL
- created_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- INDEX: member_id, status, type

### activity_logs
- id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- actor_profile_id: BIGINT UNSIGNED NULL (no FK — actor may be deleted, log must persist)
- action: VARCHAR(100) NOT NULL
- entity_type: VARCHAR(100) NOT NULL
- entity_id: BIGINT UNSIGNED NOT NULL
- old_values: JSON NULL
- new_values: JSON NULL
- created_at: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- INDEX: actor_profile_id, (entity_type, entity_id), created_at

---

## Additional Requirements

1. Add a header comment block at the top of schema.sql with: project name, date, description
2. Set `SET NAMES utf8mb4;` and `SET FOREIGN_KEY_CHECKS = 0;` at the top
3. Set `SET FOREIGN_KEY_CHECKS = 1;` at the bottom
4. All named constraints follow the pattern: `fk_{child_table}_{column}` and `uq_{table}_{column(s)}`
5. Add a comment above each table block: `-- TABLE: table_name`
6. Do NOT include any INSERT statements
7. Do NOT include any stored procedures or triggers
8. Output must be valid MySQL 8 syntax only

---

## Output

Single file: `schema.sql`

Place it in the root of the `kcdf-api-backend` project under `database/schema.sql`
