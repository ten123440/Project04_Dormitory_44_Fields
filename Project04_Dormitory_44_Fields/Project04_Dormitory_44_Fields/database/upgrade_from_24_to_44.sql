-- For the original 24-field database only. Backup first. Run ONCE.
-- Prefer php artisan migrate for Laravel projects. Do not run both methods.

CREATE TABLE `buildings` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `building_code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `address` TEXT NULL,
  `total_floors` SMALLINT UNSIGNED NOT NULL,
  `contact_phone` VARCHAR(20) NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `users`
  ADD COLUMN `student_code` VARCHAR(30) NULL UNIQUE,
  ADD COLUMN `faculty` VARCHAR(150) NULL,
  ADD COLUMN `major` VARCHAR(150) NULL;

ALTER TABLE `room_types`
  ADD COLUMN `description` TEXT NULL,
  ADD COLUMN `capacity` SMALLINT UNSIGNED NULL,
  ADD COLUMN `deposit_amount` DECIMAL(10,2) NULL;

ALTER TABLE `rooms`
  ADD COLUMN `building_id` BIGINT UNSIGNED NULL,
  ADD CONSTRAINT rooms_building_id_foreign FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE RESTRICT;

ALTER TABLE `bookings`
  ADD COLUMN `check_out_date` DATE NULL,
  ADD COLUMN `monthly_rate` DECIMAL(10,2) NULL,
  ADD COLUMN `deposit_amount` DECIMAL(10,2) NULL,
  ADD COLUMN `notes` TEXT NULL,
  ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT NULL;
