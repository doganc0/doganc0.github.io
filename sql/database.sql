CREATE TABLE IF NOT EXISTS `tbl_admin` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `site_logo` VARCHAR(255) NOT NULL DEFAULT '',
  `site_phone` VARCHAR(50) NOT NULL DEFAULT '',
  `form_avatar` VARCHAR(255) NOT NULL DEFAULT '',
  `redirect_url` VARCHAR(255) NOT NULL DEFAULT '',
  `notification_email` VARCHAR(255) NOT NULL DEFAULT '',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_insurance_types` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `image_path` VARCHAR(255) NOT NULL DEFAULT '',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_form_steps` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `insurance_type_id` INT UNSIGNED NOT NULL,
  `step_title` VARCHAR(150) NOT NULL,
  `step_order` INT NOT NULL DEFAULT 1,
  FOREIGN KEY (`insurance_type_id`) REFERENCES `tbl_insurance_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_form_fields` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `step_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `field_type` ENUM('text','email','phone','number','dropdown','checkbox','date','textarea') NOT NULL DEFAULT 'text',
  `placeholder` VARCHAR(255) NOT NULL DEFAULT '',
  `options` TEXT,
  `is_required` TINYINT(1) NOT NULL DEFAULT 1,
  `field_order` INT NOT NULL DEFAULT 1,
  FOREIGN KEY (`step_id`) REFERENCES `tbl_form_steps`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_submissions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `insurance_type_id` INT UNSIGNED NOT NULL,
  `submission_data` JSON NOT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'Yeni',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`insurance_type_id`) REFERENCES `tbl_insurance_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tbl_admin` (`username`, `password`) VALUES
('admin', '$2y$12$NyHF7Pr/sx6fRm.Q0EG/ceLjp/bGX2lxdA5OVCGWv8sNBjhQKYa9K')
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`);

INSERT INTO `tbl_settings` (`id`, `site_logo`, `site_phone`, `form_avatar`, `redirect_url`, `notification_email`)
VALUES (1, '', '+90 850 000 00 00', '', 'https://magnussigorta.com', 'info@magnussigorta.com')
ON DUPLICATE KEY UPDATE `site_phone` = VALUES(`site_phone`);
