-- Patch for existing installs: soft-delete support on family_members
-- Run once against an already-installed database.

ALTER TABLE `family_members`
    ADD COLUMN `status` ENUM('active', 'removed') NOT NULL DEFAULT 'active' AFTER `member_role`,
    ADD INDEX `idx_family_members_status` (`status`);
