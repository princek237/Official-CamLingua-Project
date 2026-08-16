-- ============================================================================
-- Migration: Role Audit Columns
-- Adds role_assigned_by and role_assigned_at to the users table.
-- Run this ONCE against your camlingua database.
-- ============================================================================

USE `camlingua`;

-- Add audit columns (IF NOT EXISTS guards for safe re-runs)
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `role_assigned_by`  INT UNSIGNED DEFAULT NULL
        COMMENT 'ID of the admin who last changed this user''s role'
        AFTER `role`,
    ADD COLUMN IF NOT EXISTS `role_assigned_at`  TIMESTAMP NULL DEFAULT NULL
        COMMENT 'Timestamp of the last role change'
        AFTER `role_assigned_by`;

-- Foreign key (soft — allows the assigning admin to be deleted without cascade)
-- Only add if the constraint doesn't already exist
SET @fk_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'camlingua'
      AND TABLE_NAME        = 'users'
      AND CONSTRAINT_NAME   = 'fk_users_role_assigned_by'
);

SET @sql = IF(
    @fk_exists = 0,
    'ALTER TABLE `users` ADD CONSTRAINT `fk_users_role_assigned_by`
     FOREIGN KEY (`role_assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT 1'  -- no-op if already exists
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Stamp the existing seeded admin row as self-assigned
UPDATE `users`
SET    `role_assigned_by` = `id`,
       `role_assigned_at` = `created_at`
WHERE  `role` = 'admin'
  AND  `role_assigned_by` IS NULL;

-- ============================================================================
-- END OF MIGRATION
-- ============================================================================
