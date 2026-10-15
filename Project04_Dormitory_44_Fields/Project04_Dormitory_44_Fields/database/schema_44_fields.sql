-- Fresh EMPTY database only. Select target database in phpMyAdmin first.
-- Do not import when using Laravel migrations. No existing data is dropped.

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NOT NULL UNIQUE,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `student_code` VARCHAR(30) NULL UNIQUE,
  `faculty` VARCHAR(150) NULL,
  `major` VARCHAR(150) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `room_types` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `monthly_rate` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` TEXT NULL,
  `capacity` SMALLINT UNSIGNED NULL,
  `deposit_amount` DECIMAL(10,2) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `rooms` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `room_number` VARCHAR(255) NOT NULL UNIQUE,
  `room_type_id` BIGINT UNSIGNED NOT NULL,
  `floor` INT NOT NULL,
  `status` ENUM('available','occupied','maintenance') NOT NULL DEFAULT 'available',
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `building_id` BIGINT UNSIGNED NULL,
  CONSTRAINT rooms_room_type_id_foreign FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
  CONSTRAINT rooms_building_id_foreign FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bookings` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tracking_code` VARCHAR(255) NOT NULL UNIQUE,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `room_id` BIGINT UNSIGNED NOT NULL,
  `check_in_date` DATE NOT NULL,
  `status` ENUM('pending','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `check_out_date` DATE NULL,
  `monthly_rate` DECIMAL(10,2) NULL,
  `deposit_amount` DECIMAL(10,2) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT bookings_room_id_foreign FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
