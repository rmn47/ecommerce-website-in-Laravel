ALTER TABLE `users` ADD `is_suspicious` TINYINT NULL DEFAULT '0' AFTER `banned`;

ALTER TABLE `notification_types` CHANGE `type` `type` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `notification_types` ADD `addon` VARCHAR(50) NULL AFTER `status`;

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`) 
VALUES
(NULL, 'view_notifications', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'mark_customer_suspected', 'customer', 'web', current_timestamp(), current_timestamp());

UPDATE `business_settings` SET `value` = '9.7' WHERE `business_settings`.`type` = 'current_version';

COMMIT;