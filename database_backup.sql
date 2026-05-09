-- Zaman Kapsülü Veritabanı Yapısı ve Örnek Veriler

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";

-- Veritabanı: `zaman_kapsulu`
CREATE DATABASE IF NOT EXISTS `zaman_kapsulu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `zaman_kapsulu`;

-- Tablo: `USERS`
CREATE TABLE IF NOT EXISTS `USERS` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablo: `CAPSULES`
CREATE TABLE IF NOT EXISTS `CAPSULES` (
  `capsule_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `target_date` date NOT NULL,
  `status` enum('Locked','Unlocked') NOT NULL DEFAULT 'Locked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`capsule_id`),
  KEY `sender_id` (`sender_id`),
  CONSTRAINT `fk_capsules_users` FOREIGN KEY (`sender_id`) REFERENCES `USERS` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablo: `CONTENTS`
CREATE TABLE IF NOT EXISTS `CONTENTS` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `capsule_id` int(11) NOT NULL,
  `content_type` enum('Text','Media') NOT NULL,
  `text_body` text DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  KEY `capsule_id` (`capsule_id`),
  CONSTRAINT `fk_contents_capsules` FOREIGN KEY (`capsule_id`) REFERENCES `CAPSULES` (`capsule_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablo: `CATEGORIES`
CREATE TABLE IF NOT EXISTS `CATEGORIES` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kategoriler tablosuna varsayılan verilerin eklenmesi
INSERT INTO `CATEGORIES` (`category_id`, `category_name`) VALUES
(1, 'Kişisel'),
(2, 'Eğitim'),
(3, 'Eğlence')
ON DUPLICATE KEY UPDATE `category_name`=VALUES(`category_name`);

-- Tablo: `CAPSULE_CATEGORY`
CREATE TABLE IF NOT EXISTS `CAPSULE_CATEGORY` (
  `capsule_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`capsule_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_cc_capsules` FOREIGN KEY (`capsule_id`) REFERENCES `CAPSULES` (`capsule_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cc_categories` FOREIGN KEY (`category_id`) REFERENCES `CATEGORIES` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablo: `RECIPIENTS`
CREATE TABLE IF NOT EXISTS `RECIPIENTS` (
  `recipient_id` int(11) NOT NULL AUTO_INCREMENT,
  `capsule_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`recipient_id`),
  KEY `capsule_id` (`capsule_id`),
  CONSTRAINT `fk_recipients_capsules` FOREIGN KEY (`capsule_id`) REFERENCES `CAPSULES` (`capsule_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablo: `OPENING_LOGS`
CREATE TABLE IF NOT EXISTS `OPENING_LOGS` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `capsule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `opened_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `capsule_id` (`capsule_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_logs_capsules` FOREIGN KEY (`capsule_id`) REFERENCES `CAPSULES` (`capsule_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_logs_users` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
