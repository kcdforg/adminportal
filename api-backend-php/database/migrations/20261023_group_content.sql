CREATE TABLE `group_contents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `content_type` ENUM('announcement', 'poll', 'event') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NULL,
    `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'published',
    `content_data` JSON NULL,
    `created_by_user_type` ENUM('member', 'admin') NOT NULL,
    `created_by_user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_group_contents_group_type_status` (`group_id`, `content_type`, `status`, `created_at`),
    CONSTRAINT `fk_group_contents_group` FOREIGN KEY (`group_id`) REFERENCES `parent_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `group_poll_votes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `poll_id` BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `option_indexes` JSON NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_group_poll_votes_poll_member` (`poll_id`, `member_id`),
    INDEX `idx_group_poll_votes_member` (`member_id`),
    CONSTRAINT `fk_group_poll_votes_poll` FOREIGN KEY (`poll_id`) REFERENCES `group_contents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_group_poll_votes_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `group_event_registrations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id` BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('registered', 'cancelled') NOT NULL DEFAULT 'registered',
    `registered_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_group_event_registrations_event_member` (`event_id`, `member_id`),
    INDEX `idx_group_event_registrations_status` (`event_id`, `status`),
    CONSTRAINT `fk_group_event_registrations_event` FOREIGN KEY (`event_id`) REFERENCES `group_contents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_group_event_registrations_member` FOREIGN KEY (`member_id`) REFERENCES `member_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
