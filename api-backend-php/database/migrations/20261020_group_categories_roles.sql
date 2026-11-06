-- Back up the database before applying this migration.
-- Existing groups receive an "Uncategorized" category. The category column
-- stays nullable so deployments can migrate in stages without losing data.
-- Existing memberships remain ordinary members; there is no reliable legacy
-- ownership field from which an owner can be inferred.

CREATE TABLE `group_categories` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(150) NOT NULL,
    `description`   TEXT NULL,
    `status`        ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_group_categories_name` (`category_name`),
    INDEX `idx_group_categories_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `group_categories` (`category_name`, `description`, `status`)
VALUES ('Uncategorized', 'Default category assigned during group migration.', 'active');

ALTER TABLE `parent_groups`
    ADD COLUMN `category_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD INDEX `idx_parent_groups_category_id` (`category_id`),
    ADD CONSTRAINT `fk_parent_groups_category`
        FOREIGN KEY (`category_id`) REFERENCES `group_categories` (`id`);

UPDATE `parent_groups`
SET `category_id` = (
    SELECT `id` FROM `group_categories` WHERE `category_name` = 'Uncategorized'
);

ALTER TABLE `group_members`
    ADD COLUMN `member_role` ENUM('owner', 'admin', 'moderator', 'member')
        NOT NULL DEFAULT 'member' AFTER `status`,
    ADD INDEX `idx_group_members_group_status_role` (`group_id`, `status`, `member_role`);

-- Before deploying role-gated member operations, review and explicitly assign
-- an owner to each existing group using an approved, active member account. Do not
-- promote all existing members automatically:
--
-- SELECT g.id, g.group_name, gm.member_id, gm.status, gm.member_role
-- FROM parent_groups AS g
-- LEFT JOIN group_members AS gm ON gm.group_id = g.id
-- ORDER BY g.id, gm.member_id;
--
-- For each approved owner, run:
-- UPDATE group_members
-- SET member_role = 'owner'
-- WHERE group_id = <approved_group_id>
--   AND member_id = <approved_member_id>
--   AND status = 'active';
--
-- If the approved member has no membership row, create the membership and
-- ownership together:
-- INSERT INTO group_members (group_id, member_id, status, member_role)
-- VALUES (<approved_group_id>, <approved_member_id>, 'active', 'owner');
