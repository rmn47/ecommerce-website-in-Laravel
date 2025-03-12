-- Note
CREATE TABLE `notes` (
  `id` bigint(20) NOT NULL,
  `note_type` varchar(50) NOT NULL,
  `description` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

-- Note Translation
CREATE TABLE `note_translations` (
  `id` bigint(20) NOT NULL,
  `note_id` bigint(20) NOT NULL,
  `description` longtext NOT NULL,
  `lang` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `note_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `note_translations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `shops` 
ADD `top_banner_image` LONGTEXT NULL AFTER `bank_payment_status`, 
ADD `top_banner_link` LONGTEXT NULL AFTER `top_banner_image`, 
ADD `slider_images` LONGTEXT NULL AFTER `top_banner_link`,
ADD `slider_links` LONGTEXT NULL AFTER `slider_images`,
ADD `banner_full_width_1_images` LONGTEXT NULL AFTER `slider_links`,
ADD `banner_full_width_1_links` LONGTEXT NULL AFTER `banner_full_width_1_images`,
ADD `banners_half_width_images` LONGTEXT NULL AFTER `banner_full_width_1_links`,
ADD `banners_half_width_links` LONGTEXT NULL AFTER `banners_half_width_images`,
ADD `banner_full_width_2_images` LONGTEXT NULL AFTER `banners_half_width_links`,
ADD `banner_full_width_2_links` LONGTEXT NULL AFTER `banner_full_width_2_images`;

-- Warranty
CREATE TABLE `warranties` (
  `id` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `logo` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `warranties`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `warranties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Warranty Translations
CREATE TABLE `warranty_translations` (
  `id` bigint(20) NOT NULL,
  `warranty_id` bigint(20) NOT NULL,
  `text` varchar(50) NOT NULL,
  `lang` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `warranty_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `warranty_translations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `products` 
  ADD `has_warranty` TINYINT NOT NULL DEFAULT '0' AFTER `frequently_bought_selection_type`, 
  ADD `warranty_id` INT NULL AFTER `has_warranty`, 
  ADD `warranty_note_id` INT NULL AFTER `warranty_id`;

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`) 
VALUES
(NULL, 'view_product_warranties', 'product_warranty', 'web', current_timestamp(), current_timestamp()),
(NULL, 'add_product_warranty', 'product_warranty', 'web', current_timestamp(), current_timestamp()),
(NULL, 'edit_product_warranty', 'product_warranty', 'web', current_timestamp(), current_timestamp()),
(NULL, 'delete_product_warranty', 'product_warranty', 'web', current_timestamp(), current_timestamp()),
(NULL, 'view_notes', 'note', 'web', current_timestamp(), current_timestamp()),
(NULL, 'add_note', 'note', 'web', current_timestamp(), current_timestamp()),
(NULL, 'edit_note', 'note', 'web', current_timestamp(), current_timestamp()),
(NULL, 'delete_note', 'note', 'web', current_timestamp(), current_timestamp());


UPDATE `business_settings` SET `value` = '9.5' WHERE `business_settings`.`type` = 'current_version';

COMMIT;