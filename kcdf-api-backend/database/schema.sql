-- =============================================================================
-- KCDF Parents Platform — Database Schema
-- Project: kcdf-api-backend
-- Date: 2026-07-03
-- Description: Full MySQL 8 schema for the KCDF Parents platform.
--              Covers identity, families, academics, payments, community,
--              notifications, and audit logging.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables in reverse dependency order
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `invitations`;
DROP TABLE IF EXISTS `group_members`;
DROP TABLE IF EXISTS `parent_groups`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `batch_sessions`;
DROP TABLE IF EXISTS `batch_members`;
DROP TABLE IF EXISTS `student_batches`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `entity_member_relations`;
DROP TABLE IF EXISTS `entities`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `trainers`;
DROP TABLE IF EXISTS `family_members`;
DROP TABLE IF EXISTS `families`;
DROP TABLE IF EXISTS `user_logins`;
DROP TABLE IF EXISTS `refresh_tokens`;
DROP TABLE IF EXISTS `member_profiles`;
DROP TABLE IF EXISTS `addresses`;

-- =============================================================================
-- TABLE: addresses
-- Standalone reusable address store. No owner references.
-- Owner tables (families, trainers) carry address_id FK.
-- =============================================================================
CREATE TABLE `addresses` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `address_label`  VARCHAR(50) NULL DEFAULT 'home',
    `address_line_1` VARCHAR(255) NOT NULL,
    `address_line_2` VARCHAR(255) NULL,
    `city`           VARCHAR(100) NOT NULL,
    `state`          VARCHAR(100) NULL,
    `postal_code`    VARCHAR(20) NULL,
    `country`        VARCHAR(100) NOT NULL DEFAULT 'India',
    `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_addresses_city` (`city`),
    INDEX `idx_addresses_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: member_profiles
-- Universal person record. Reused by family members, trainers, admins,
-- volunteers, and any future roles.
-- =============================================================================
CREATE TABLE `member_profiles` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name`    VARCHAR(100) NOT NULL,
    `middle_name`   VARCHAR(100) NULL,
    `last_name`     VARCHAR(100) NOT NULL,
    `date_of_birth` DATE NULL,
    `gender`        ENUM('male', 'female', 'other') NULL,
    `mobile`        VARCHAR(20) NULL,
    `email`         VARCHAR(255) NULL,
    `photo_url`     VARCHAR(500) NULL,
    `blood_group`   VARCHAR(10) NULL,
    `status`        ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_member_profiles_mobile` (`mobile`),
    INDEX `idx_member_profiles_email` (`email`),
    INDEX `idx_member_profiles_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: user_logins
-- Login credentials. One per person. Profile without a login cannot authenticate.
-- =============================================================================
CREATE TABLE `user_logins` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile_id`    BIGINT UNSIGNED NOT NULL,
    `username`      VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` TIMESTAMP NULL,
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_logins_username` (`username`),
    INDEX `idx_user_logins_profile_id` (`profile_id`),
    CONSTRAINT `fk_user_logins_profile` FOREIGN KEY (`profile_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: refresh_tokens
-- Stores hashed refresh tokens for JWT rotation and revocation on logout.
-- =============================================================================
CREATE TABLE `refresh_tokens` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile_id` BIGINT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `revoked_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_refresh_tokens_token_hash` (`token_hash`),
    INDEX `idx_refresh_tokens_profile_id` (`profile_id`),
    CONSTRAINT `fk_refresh_tokens_profile` FOREIGN KEY (`profile_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: families
-- Represents a household/family. References addresses via FK.
-- =============================================================================
CREATE TABLE `families` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `family_code` VARCHAR(50) NOT NULL,
    `family_name` VARCHAR(255) NOT NULL,
    `address_id`  BIGINT UNSIGNED NULL,
    `status`      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_families_family_code` (`family_code`),
    INDEX `idx_families_address_id` (`address_id`),
    INDEX `idx_families_status` (`status`),
    CONSTRAINT `fk_families_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: family_members
-- Links member profiles to families with relationship type and role.
-- Access rules: primary=full family access, normal=normal, student=self only.
-- =============================================================================
CREATE TABLE `family_members` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `family_id`         BIGINT UNSIGNED NOT NULL,
    `profile_id`        BIGINT UNSIGNED NOT NULL,
    `relationship_type` ENUM('father', 'mother', 'guardian', 'child') NOT NULL,
    `member_role`       ENUM('primary', 'normal', 'student') NOT NULL DEFAULT 'normal',
    `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_family_members_family_profile` (`family_id`, `profile_id`),
    INDEX `idx_family_members_family_id` (`family_id`),
    INDEX `idx_family_members_profile_id` (`profile_id`),
    CONSTRAINT `fk_family_members_family` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`),
    CONSTRAINT `fk_family_members_profile` FOREIGN KEY (`profile_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: trainers
-- Trainer role record. Reuses member_profiles for identity.
-- Optional address via addresses FK.
-- =============================================================================
CREATE TABLE `trainers` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile_id`       BIGINT UNSIGNED NOT NULL,
    `trainer_code`     VARCHAR(50) NOT NULL,
    `specialization`   VARCHAR(255) NULL,
    `experience_years` TINYINT UNSIGNED NULL DEFAULT 0,
    `bio`              TEXT NULL,
    `joined_at`        DATE NULL,
    `address_id`       BIGINT UNSIGNED NULL,
    `status`           ENUM('active', 'inactive', 'on_leave') NOT NULL DEFAULT 'active',
    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_trainers_trainer_code` (`trainer_code`),
    INDEX `idx_trainers_profile_id` (`profile_id`),
    INDEX `idx_trainers_address_id` (`address_id`),
    INDEX `idx_trainers_status` (`status`),
    CONSTRAINT `fk_trainers_profile` FOREIGN KEY (`profile_id`) REFERENCES `member_profiles` (`id`),
    CONSTRAINT `fk_trainers_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: admins
-- Admin role record. admin_role controls access level within the admin portal.
-- =============================================================================
CREATE TABLE `admins` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile_id` BIGINT UNSIGNED NOT NULL,
    `admin_role` ENUM('super_admin', 'program_manager', 'accounts', 'readonly') NOT NULL DEFAULT 'readonly',
    `status`     ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_admins_profile_id` (`profile_id`),
    INDEX `idx_admins_admin_role` (`admin_role`),
    INDEX `idx_admins_status` (`status`),
    CONSTRAINT `fk_admins_profile` FOREIGN KEY (`profile_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: entities
-- Generic institution store. entity_type differentiates school/college/org/etc.
-- meta JSON stores entity-type-specific data.
-- =============================================================================
CREATE TABLE `entities` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `entity_type` ENUM('school', 'college', 'organization', 'hospital', 'association') NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `city`        VARCHAR(100) NULL,
    `state`       VARCHAR(100) NULL,
    `country`     VARCHAR(100) NULL DEFAULT 'India',
    `meta`        JSON NULL,
    `status`      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_entities_entity_type` (`entity_type`),
    INDEX `idx_entities_status` (`status`),
    INDEX `idx_entities_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: entity_member_relations
-- Links member profiles to external entities.
-- relation_context JSON: grade, section, designation, department, etc.
-- =============================================================================
CREATE TABLE `entity_member_relations` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`        BIGINT UNSIGNED NOT NULL,
    `entity_id`        BIGINT UNSIGNED NOT NULL,
    `relation_type`    ENUM('studies_at', 'works_at', 'member_of', 'volunteer_at') NOT NULL,
    `start_date`       DATE NULL,
    `end_date`         DATE NULL,
    `is_current`       TINYINT(1) NOT NULL DEFAULT 1,
    `relation_context` JSON NULL,
    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_emr_member_id` (`member_id`),
    INDEX `idx_emr_entity_id` (`entity_id`),
    INDEX `idx_emr_is_current` (`is_current`),
    CONSTRAINT `fk_emr_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`),
    CONSTRAINT `fk_emr_entity` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: programs
-- Defines a class, workshop, camp, or event offering.
-- =============================================================================
CREATE TABLE `programs` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_name` VARCHAR(255) NOT NULL,
    `program_type` ENUM('class', 'workshop', 'camp', 'event') NOT NULL,
    `description`  TEXT NULL,
    `age_group`    VARCHAR(100) NULL,
    `fee_amount`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status`       ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_programs_program_type` (`program_type`),
    INDEX `idx_programs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: student_batches
-- A scheduled run of a program. Assigned to a trainer with optional capacity.
-- =============================================================================
CREATE TABLE `student_batches` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_id` BIGINT UNSIGNED NOT NULL,
    `batch_name` VARCHAR(255) NOT NULL,
    `capacity`   SMALLINT UNSIGNED NULL,
    `trainer_id` BIGINT UNSIGNED NULL,
    `start_date` DATE NULL,
    `end_date`   DATE NULL,
    `status`     ENUM('upcoming', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_student_batches_program_id` (`program_id`),
    INDEX `idx_student_batches_trainer_id` (`trainer_id`),
    INDEX `idx_student_batches_status` (`status`),
    CONSTRAINT `fk_batches_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`),
    CONSTRAINT `fk_batches_trainer` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: batch_members
-- Members assigned to a batch. Unique per (batch_id, member_id).
-- =============================================================================
CREATE TABLE `batch_members` (
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `batch_id`  BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`    ENUM('active', 'dropped', 'completed') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_batch_members_batch_member` (`batch_id`, `member_id`),
    INDEX `idx_batch_members_batch_id` (`batch_id`),
    INDEX `idx_batch_members_member_id` (`member_id`),
    CONSTRAINT `fk_batch_members_batch` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`id`),
    CONSTRAINT `fk_batch_members_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: batch_sessions
-- Individual sessions within a batch.
-- attendance_locked prevents further changes after session is finalized.
-- =============================================================================
CREATE TABLE `batch_sessions` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `batch_id`          BIGINT UNSIGNED NOT NULL,
    `session_number`    SMALLINT UNSIGNED NULL,
    `session_title`     VARCHAR(255) NULL,
    `session_date`      DATE NOT NULL,
    `start_time`        TIME NULL,
    `end_time`          TIME NULL,
    `session_type`      ENUM('regular', 'special', 'exam', 'workshop') NOT NULL DEFAULT 'regular',
    `status`            ENUM('scheduled', 'completed', 'cancelled', 'postponed') NOT NULL DEFAULT 'scheduled',
    `trainer_id`        BIGINT UNSIGNED NULL,
    `topics_covered`    TEXT NULL,
    `homework`          TEXT NULL,
    `notes`             TEXT NULL,
    `attendance_locked` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_batch_sessions_batch_id` (`batch_id`),
    INDEX `idx_batch_sessions_session_date` (`session_date`),
    INDEX `idx_batch_sessions_status` (`status`),
    INDEX `idx_batch_sessions_trainer_id` (`trainer_id`),
    CONSTRAINT `fk_sessions_batch` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`id`),
    CONSTRAINT `fk_sessions_trainer` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: attendance
-- Session-based attendance. One record per (session, member).
-- =============================================================================
CREATE TABLE `attendance` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `batch_session_id`    BIGINT UNSIGNED NOT NULL,
    `member_id`           BIGINT UNSIGNED NOT NULL,
    `attendance_status`   ENUM('present', 'absent', 'late', 'excused') NOT NULL,
    `remarks`             VARCHAR(500) NULL,
    `marked_by_member_id` BIGINT UNSIGNED NULL,
    `marked_at`           TIMESTAMP NULL,
    `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attendance_session_member` (`batch_session_id`, `member_id`),
    INDEX `idx_attendance_batch_session_id` (`batch_session_id`),
    INDEX `idx_attendance_member_id` (`member_id`),
    INDEX `idx_attendance_status` (`attendance_status`),
    CONSTRAINT `fk_attendance_session` FOREIGN KEY (`batch_session_id`) REFERENCES `batch_sessions` (`id`),
    CONSTRAINT `fk_attendance_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`),
    CONSTRAINT `fk_attendance_marked_by` FOREIGN KEY (`marked_by_member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: enrollments
-- Records a family enrolling a member into a batch.
-- fee_amount is snapshotted at enrollment time.
-- payment_status is recalculated from payments table.
-- =============================================================================
CREATE TABLE `enrollments` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `family_id`             BIGINT UNSIGNED NOT NULL,
    `member_id`             BIGINT UNSIGNED NOT NULL,
    `batch_id`              BIGINT UNSIGNED NOT NULL,
    `enrolled_by_member_id` BIGINT UNSIGNED NULL,
    `enrolled_at`           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`                ENUM('pending', 'active', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `payment_status`        ENUM('unpaid', 'partial', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    `fee_amount`            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_enrollments_member_batch` (`member_id`, `batch_id`),
    INDEX `idx_enrollments_family_id` (`family_id`),
    INDEX `idx_enrollments_member_id` (`member_id`),
    INDEX `idx_enrollments_batch_id` (`batch_id`),
    INDEX `idx_enrollments_status` (`status`),
    INDEX `idx_enrollments_payment_status` (`payment_status`),
    CONSTRAINT `fk_enrollments_family` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`),
    CONSTRAINT `fk_enrollments_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`),
    CONSTRAINT `fk_enrollments_batch` FOREIGN KEY (`batch_id`) REFERENCES `student_batches` (`id`),
    CONSTRAINT `fk_enrollments_enrolled_by` FOREIGN KEY (`enrolled_by_member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: payments
-- Financial transactions. Supports class fees, donations, event fees, refunds.
-- Once status = completed, record is immutable. Record a new refund payment instead.
-- =============================================================================
CREATE TABLE `payments` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `family_id`             BIGINT UNSIGNED NOT NULL,
    `enrollment_id`         BIGINT UNSIGNED NULL,
    `payment_type`          ENUM('class_fee', 'donation', 'event_fee', 'refund') NOT NULL,
    `amount`                DECIMAL(10,2) NOT NULL,
    `payment_method`        ENUM('cash', 'bank_transfer', 'upi', 'card', 'cheque', 'online') NOT NULL,
    `transaction_reference` VARCHAR(255) NULL,
    `status`                ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `notes`                 TEXT NULL,
    `paid_at`               TIMESTAMP NULL,
    `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_payments_family_id` (`family_id`),
    INDEX `idx_payments_enrollment_id` (`enrollment_id`),
    INDEX `idx_payments_payment_type` (`payment_type`),
    INDEX `idx_payments_status` (`status`),
    INDEX `idx_payments_paid_at` (`paid_at`),
    CONSTRAINT `fk_payments_family` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`),
    CONSTRAINT `fk_payments_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: parent_groups
-- Community groups for parents.
-- visibility: public (open join), private (admin-managed), invite_only.
-- =============================================================================
CREATE TABLE `parent_groups` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_name`  VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `visibility`  ENUM('public', 'private', 'invite_only') NOT NULL DEFAULT 'public',
    `status`      ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_parent_groups_visibility` (`visibility`),
    INDEX `idx_parent_groups_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: group_members
-- Membership of a profile in a parent group.
-- =============================================================================
CREATE TABLE `group_members` (
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id`  BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`    ENUM('active', 'left', 'banned') NOT NULL DEFAULT 'active',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_group_members_group_member` (`group_id`, `member_id`),
    INDEX `idx_group_members_group_id` (`group_id`),
    INDEX `idx_group_members_member_id` (`member_id`),
    CONSTRAINT `fk_group_members_group` FOREIGN KEY (`group_id`) REFERENCES `parent_groups` (`id`),
    CONSTRAINT `fk_group_members_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: invitations
-- Parent-to-parent invitations. Expire 7 days after sent_at.
-- invite_code is unique and auto-generated.
-- =============================================================================
CREATE TABLE `invitations` (
    `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invited_by_member_id` BIGINT UNSIGNED NOT NULL,
    `invite_mobile`        VARCHAR(20) NULL,
    `invite_email`         VARCHAR(255) NULL,
    `invite_code`          VARCHAR(50) NOT NULL,
    `status`               ENUM('pending', 'accepted', 'expired', 'cancelled') NOT NULL DEFAULT 'pending',
    `sent_at`              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `accepted_at`          TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_invitations_invite_code` (`invite_code`),
    INDEX `idx_invitations_invited_by` (`invited_by_member_id`),
    INDEX `idx_invitations_status` (`status`),
    CONSTRAINT `fk_invitations_invited_by` FOREIGN KEY (`invited_by_member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: notifications
-- Per-member notifications. Supports in_app, push, and email types.
-- Notifications are never deleted — only archived.
-- =============================================================================
CREATE TABLE `notifications` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`  BIGINT UNSIGNED NOT NULL,
    `title`      VARCHAR(255) NOT NULL,
    `message`    TEXT NOT NULL,
    `type`       ENUM('push', 'email', 'in_app') NOT NULL DEFAULT 'in_app',
    `status`     ENUM('unread', 'read', 'archived') NOT NULL DEFAULT 'unread',
    `read_at`    TIMESTAMP NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_notifications_member_id` (`member_id`),
    INDEX `idx_notifications_status` (`status`),
    INDEX `idx_notifications_type` (`type`),
    CONSTRAINT `fk_notifications_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: activity_logs
-- Append-only audit trail. actor_profile_id has no FK (actor may be deleted;
-- log must persist). Never updated or deleted.
-- =============================================================================
CREATE TABLE `activity_logs` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `actor_profile_id` BIGINT UNSIGNED NULL,
    `action`           VARCHAR(100) NOT NULL,
    `entity_type`      VARCHAR(100) NOT NULL,
    `entity_id`        BIGINT UNSIGNED NOT NULL,
    `old_values`       JSON NULL,
    `new_values`       JSON NULL,
    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_activity_logs_actor` (`actor_profile_id`),
    INDEX `idx_activity_logs_entity` (`entity_type`, `entity_id`),
    INDEX `idx_activity_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- END OF SCHEMA
-- Tables: 22 (including refresh_tokens)
-- =============================================================================
