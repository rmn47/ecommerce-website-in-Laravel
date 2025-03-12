ALTER TABLE `reviews` CHANGE `user_id` `user_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `reviews` 
ADD `type` VARCHAR(10) NOT NULL DEFAULT 'real' AFTER `id`,
ADD `custom_reviewer_name` VARCHAR(100) NULL AFTER `user_id`, 
ADD `custom_reviewer_image` VARCHAR(100) NULL AFTER `custom_reviewer_name`,
ADD `created_at_is_custom` TINYINT(1) NOT NULL DEFAULT '0' AFTER `viewed`;

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`) 
VALUES
(NULL, 'add_custom_review', 'product_review', 'web', current_timestamp(), current_timestamp()),
(NULL, 'edit_custom_review', 'product_review', 'web', current_timestamp(), current_timestamp()),
(NULL, 'view_all_seller_rating_and_followers', 'seller', 'web', current_timestamp(), current_timestamp()),
(NULL, 'edit_seller_custom_followers', 'seller', 'web', current_timestamp(), current_timestamp());

ALTER TABLE `shops` ADD `custom_followers` INT NULL DEFAULT '0' AFTER `bank_payment_status`;

UPDATE `business_settings` SET `value` = '9.4' WHERE `business_settings`.`type` = 'current_version';

COMMIT;