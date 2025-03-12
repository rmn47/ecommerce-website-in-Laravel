ALTER TABLE `notes` 
ADD `user_id` INT NOT NULL AFTER `id`,
ADD `seller_access` TINYINT NOT NULL DEFAULT '0' COMMENT 'Seller can access admin note;\r\n0 = No\r\n1 = Yes' AFTER `description`;

UPDATE notes
SET user_id = (
    SELECT id 
    FROM users 
    WHERE user_type = 'admin'
    LIMIT 1
);

ALTER TABLE `orders` ADD `delivered_date` TIMESTAMP NULL AFTER `notified`;

ALTER TABLE `personal_access_tokens` ADD `expires_at` TIMESTAMP NULL AFTER `last_used_at`;

UPDATE `business_settings` SET `value` = '9.6' WHERE `business_settings`.`type` = 'current_version';

COMMIT;