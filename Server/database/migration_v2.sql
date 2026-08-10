USE `camlingua`;

-- 1. Update `users` table
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `phone_number` VARCHAR(20) DEFAULT NULL AFTER `email`;

-- We only drop and add status if it doesn't exist. To be safe, we'll try to add it.
-- This might error if it already exists, but we assume it doesn't.
ALTER TABLE `users` ADD COLUMN `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active' AFTER `role`;
UPDATE `users` SET `status` = IF(`is_active` = 1, 'active', 'inactive');
ALTER TABLE `users` DROP COLUMN `is_active`;

-- 2. Create `languages` table
CREATE TABLE IF NOT EXISTS `languages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Populate `languages` table with distinct languages from translation_history
INSERT IGNORE INTO `languages` (`name`, `code`)
SELECT DISTINCT IFNULL(
  CASE target_lang
    WHEN 'en' THEN 'English'
    WHEN 'fr' THEN 'French'
    WHEN 'ewo' THEN 'Ewondo'
    WHEN 'bam' THEN 'Bamileke'
    WHEN 'ful' THEN 'Fulfulde'
    ELSE UPPER(target_lang)
  END, target_lang
), target_lang FROM `translation_history`
UNION 
SELECT DISTINCT IFNULL(
  CASE source_lang
    WHEN 'en' THEN 'English'
    WHEN 'fr' THEN 'French'
    WHEN 'ewo' THEN 'Ewondo'
    WHEN 'bam' THEN 'Bamileke'
    WHEN 'ful' THEN 'Fulfulde'
    ELSE UPPER(source_lang)
  END, source_lang
), source_lang FROM `translation_history`;

-- 3. Create `settings` table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES 
('site_name', 'CamLingua', 'Name of the website'),
('platform_logo', '', 'URL of the platform logo'),
('default_language', 'en', 'Default interface language'),
('translation_api_provider', 'nllb', 'Provider for translation engine'),
('contact_email', 'support@camlingua.com', 'Email for contact forms');

-- 4. Update `translation_history` table
ALTER TABLE `translation_history` 
  ADD COLUMN `status` ENUM('pending', 'completed', 'failed') DEFAULT 'completed' AFTER `translated_text`;

-- 5. Recreate view `user_with_subscription`
DROP VIEW IF EXISTS `user_with_subscription`;
CREATE VIEW `user_with_subscription` AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.phone_number,
    u.full_name,
    u.bio,
    u.avatar_url,
    u.role,
    u.status,
    u.email_verified,
    u.created_at,
    u.updated_at,
    s.name AS subscription_name,
    s.slug AS subscription_slug,
    us.billing_cycle,
    us.status AS subscription_status,
    us.expires_at AS subscription_expires_at
FROM users u
LEFT JOIN user_subscriptions us ON u.id = us.user_id AND us.status = 'active'
LEFT JOIN subscriptions s ON us.subscription_id = s.id;
