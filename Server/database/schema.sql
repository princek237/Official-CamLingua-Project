-- ============================================================================
-- CamLingua Database Schema
-- MySQL 5.7+ / MariaDB 10.2+
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `camlingua` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `camlingua`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables in reverse order of dependencies
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `languages`;
DROP TABLE IF EXISTS `translation_history`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `user_subscriptions`;
DROP TABLE IF EXISTS `subscriptions`;
DROP TABLE IF EXISTS `users`;

-- ============================================================================
-- USERS TABLE
-- ============================================================================
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `avatar_url` VARCHAR(500) DEFAULT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  `email_verified` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SUBSCRIPTIONS TABLE (Plan definitions)
-- ============================================================================
CREATE TABLE `subscriptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `price_monthly` DECIMAL(10,2) DEFAULT 0.00,
  `price_yearly` DECIMAL(10,2) DEFAULT 0.00,
  `features` JSON DEFAULT NULL,
  `limits` JSON DEFAULT NULL COMMENT 'e.g., {"translations_per_day": 5, "max_chars": 500}',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default plans
INSERT INTO `subscriptions` (`name`, `slug`, `description`, `price_monthly`, `price_yearly`, `features`, `limits`) VALUES
('Free', 'free', 'Perfect for occasional translations and language exploration.', 0.00, 0.00,
 '["500 chars per translation", "5 translations per day", "5 languages", "7 day history"]',
 '{"translations_per_day": 5, "max_chars": 500, "languages_count": 5, "history_days": 7}'),
('Pro', 'pro', 'For developers, institutions, and businesses.', 9900.00, 95040.00,
 '["Unlimited translations", "20+ languages", "90 day history", "Audio pronunciation", "Priority support"]',
 '{"translations_per_day": -1, "max_chars": -1, "languages_count": -1, "history_days": 90}'),
('Premium', 'premium', 'Full API access and team collaboration.', 19900.00, 190080.00,
 '["Everything in Pro", "API access", "Unlimited history", "Custom glossaries", "Team seats (10)", "Dedicated manager"]',
 '{"translations_per_day": -1, "max_chars": -1, "languages_count": -1, "history_days": -1, "api_access": true, "team_seats": 10}');

-- ============================================================================
-- USER_SUBSCRIPTIONS TABLE (User's active subscription)
-- ============================================================================
CREATE TABLE `user_subscriptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `subscription_id` INT UNSIGNED NOT NULL,
  `status` ENUM('active', 'cancelled', 'expired', 'trial') DEFAULT 'active',
  `billing_cycle` ENUM('monthly', 'yearly') DEFAULT 'monthly',
  `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_sub_user` (`user_id`),
  KEY `fk_user_sub_subscription` (`subscription_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_user_sub_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_sub_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TRANSLATION_HISTORY TABLE
-- ============================================================================
CREATE TABLE `translation_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `source_lang` VARCHAR(10) NOT NULL,
  `target_lang` VARCHAR(10) NOT NULL,
  `source_text` TEXT NOT NULL,
  `translated_text` TEXT NOT NULL,
  `status` ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
  `is_favorite` TINYINT(1) DEFAULT 0,
  `translation_engine` VARCHAR(50) DEFAULT 'nllb-200' COMMENT 'nllb-200, mock, etc',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_trans_user` (`user_id`),
  KEY `idx_user_created` (`user_id`, `created_at`),
  KEY `idx_is_favorite` (`is_favorite`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_trans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CONTACT_MESSAGES TABLE
-- ============================================================================
CREATE TABLE `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL COMMENT 'NULL if guest/not logged in',
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_contact_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INDEXES for performance
-- ============================================================================
-- Additional composite indexes added inline above

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- Sample Admin User (password: admin123)
-- ============================================================================
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`, `status`, `email_verified`) VALUES
('admin', 'admin@camlingua.com', '$2y$10$bcBrxHkHHxXODegD1ZH82O/Oj2IxwQEcDZlat9cuLpXWRVPnvuVw.', 'CamLingua Admin', 'admin', 'active', 1);

-- Assign admin to Free plan by default
INSERT INTO `user_subscriptions` (`user_id`, `subscription_id`, `status`) VALUES
(1, 1, 'active');

-- ============================================================================
-- Views (optional) for convenience
-- ============================================================================

-- View: user_with_subscription
CREATE OR REPLACE VIEW `user_with_subscription` AS
SELECT 
    u.id,
    u.username,
    u.email,
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

-- ============================================================================
-- Sample data for testing (optional)
-- ============================================================================

-- Sample user: test@camlingua.com / password: test123
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `bio`) VALUES
('testuser', 'test@camlingua.com', '$2y$10$6n0AfaYA9DiS8bkRTKPUA.YgmSWfGAxfSJf7wFDGcHG11pff0mfam', 'Test User', 'A test user exploring CamLingua');

-- Assign test user to Free plan
INSERT INTO `user_subscriptions` (`user_id`, `subscription_id`, `status`) VALUES
(2, 1, 'active');

-- Sample translation history
INSERT INTO `translation_history` (`user_id`, `source_lang`, `target_lang`, `source_text`, `translated_text`, `is_favorite`) VALUES
(2, 'en', 'fr', 'Hello, how are you?', 'Bonjour, comment allez-vous?', 0),
(2, 'en', 'ewo', 'Good morning', 'Mboë', 1),
(2, 'fr', 'bam', 'Thank you very much', 'Ndo ndo', 0);

-- Sample contact message
INSERT INTO `contact_messages` (`user_id`, `full_name`, `email`, `subject`, `message`) VALUES
(2, 'Test User', 'test@camlingua.com', 'technical', 'I have a question about translation limits.');

-- ============================================================================
-- ============================================================================
-- LANGUAGES TABLE
-- ============================================================================
CREATE TABLE `languages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `languages` (`name`, `code`) VALUES
('English', 'en'),
('French', 'fr'),
('Ewondo', 'ewo'),
('Bamileke', 'bam'),
('Fulfulde', 'ful');

-- ============================================================================
-- SETTINGS TABLE
-- ============================================================================
CREATE TABLE `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES 
('site_name', 'CamLingua', 'Name of the website'),
('platform_logo', '', 'URL of the platform logo'),
('default_language', 'en', 'Default interface language'),
('translation_api_provider', 'nllb', 'Provider for translation engine'),
('contact_email', 'support@camlingua.com', 'Email for contact forms');

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
