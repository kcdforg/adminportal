-- Run once against an existing installation after taking a database backup.
-- Resolve duplicate profile logins before proceeding:
--   SELECT profile_id, COUNT(*) FROM user_logins GROUP BY profile_id HAVING COUNT(*) > 1;
-- Resolve duplicate admin role rows before proceeding:
--   SELECT profile_id, COUNT(*) FROM admins GROUP BY profile_id HAVING COUNT(*) > 1;
-- Review admin records that cannot be mapped to a login:
--   SELECT admin.* FROM admins AS admin LEFT JOIN user_logins AS login ON login.profile_id = admin.profile_id WHERE login.id IS NULL;
-- Resolve all results before proceeding. Admin records without matching logins
-- have no credentials to migrate; create their login accounts before dropping
-- the old table or explicitly retain their details outside the database.

ALTER TABLE user_logins
    ADD COLUMN user_type ENUM('admin', 'member') NOT NULL DEFAULT 'member' AFTER password_hash,
    ADD COLUMN role ENUM('super_admin', 'program_manager', 'accounts', 'readonly') NULL DEFAULT NULL AFTER user_type,
    ADD COLUMN display_name VARCHAR(150) NULL AFTER username;

UPDATE user_logins AS login
JOIN admins AS admin ON admin.profile_id = login.profile_id
SET login.user_type = 'admin',
    login.role = admin.admin_role,
    login.is_active = CASE WHEN admin.status = 'active' THEN login.is_active ELSE 0 END;

UPDATE user_logins AS login
LEFT JOIN member_profiles AS profile ON profile.id = login.profile_id
SET login.display_name = COALESCE(
    NULLIF(TRIM(CONCAT_WS(' ', profile.first_name, profile.middle_name, profile.last_name)), ''),
    login.username
);

ALTER TABLE user_logins
    ADD UNIQUE KEY uq_user_logins_profile_id (profile_id),
    ADD INDEX idx_user_logins_type_role (user_type, role),
    MODIFY COLUMN profile_id BIGINT UNSIGNED NULL,
    ADD CONSTRAINT chk_user_logins_type_role CHECK (
        (user_type = 'admin' AND role IS NOT NULL)
        OR (user_type = 'member' AND role IS NULL)
    );

ALTER TABLE refresh_tokens
    DROP FOREIGN KEY fk_refresh_tokens_profile,
    MODIFY COLUMN profile_id BIGINT UNSIGNED NULL,
    ADD COLUMN login_id BIGINT UNSIGNED NULL AFTER id;

UPDATE refresh_tokens AS token
JOIN user_logins AS login ON login.profile_id = token.profile_id
SET token.login_id = login.id;

-- All previously issued tokens lack the new portal/login claims. Invalidate
-- them and remove rows that cannot be assigned to a login.
DELETE FROM refresh_tokens;

ALTER TABLE refresh_tokens
    MODIFY COLUMN login_id BIGINT UNSIGNED NOT NULL,
    ADD INDEX idx_refresh_tokens_login_id (login_id),
    ADD CONSTRAINT fk_refresh_tokens_login FOREIGN KEY (login_id) REFERENCES user_logins (id),
    ADD CONSTRAINT fk_refresh_tokens_profile FOREIGN KEY (profile_id) REFERENCES member_profiles (id);

DROP TABLE admins;
