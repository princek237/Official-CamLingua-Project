-- ============================================================================
-- CamPay Payment Integration Migration
-- Run this against your camlingua database AFTER schema.sql
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- PAYMENTS TABLE
-- Tracks every CamPay payment attempt (collect request).
-- A subscription is only activated once the payment status = 'SUCCESSFUL'.
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`             INT UNSIGNED NOT NULL,
  `subscription_id`     INT UNSIGNED NOT NULL,       -- which plan they are paying for
  `campay_reference`    VARCHAR(100) DEFAULT NULL,   -- reference returned by CamPay /collect/
  `external_reference`  VARCHAR(100) NOT NULL,       -- our own UUID, sent as external_reference
  `phone`               VARCHAR(20)  NOT NULL,        -- payer phone (237xxxxxxxxx)
  `amount`              DECIMAL(10,2) NOT NULL,
  `currency`            VARCHAR(5)   NOT NULL DEFAULT 'XAF',
  `billing_cycle`       ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `operator`            VARCHAR(20)  DEFAULT NULL,   -- MTN or ORANGE (filled on success)
  `status`              ENUM('PENDING','SUCCESSFUL','FAILED') NOT NULL DEFAULT 'PENDING',
  `campay_code`         VARCHAR(50)  DEFAULT NULL,   -- CamPay transaction code on success
  `operator_reference`  VARCHAR(100) DEFAULT NULL,   -- operator-side reference
  `failure_reason`      TEXT         DEFAULT NULL,
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_external_reference` (`external_reference`),
  KEY `idx_campay_reference`  (`campay_reference`),
  KEY `idx_user_id`           (`user_id`),
  KEY `idx_status`            (`status`),
  KEY `idx_created_at`        (`created_at`),
  CONSTRAINT `fk_payment_user`         FOREIGN KEY (`user_id`)         REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payment_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
