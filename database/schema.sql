-- =============================================
-- SomaTrack Database Schema
-- Collaborative Step-by-Step Learning Platform
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `somatrack` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `somatrack`;

-- =============================================
-- USERS TABLE
-- =============================================
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
    `bio` TEXT DEFAULT NULL,
    `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `current_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `longest_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `last_activity_date` DATE DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CATEGORIES TABLE (for journey tagging)
-- =============================================
CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'bi-folder',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- JOURNEYS TABLE
-- =============================================
CREATE TABLE `journeys` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `visibility` ENUM('public','private') NOT NULL DEFAULT 'public',
    `status` ENUM('active','completed','archived') NOT NULL DEFAULT 'active',
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `total_steps` INT UNSIGNED NOT NULL DEFAULT 0,
    `completed_steps` INT UNSIGNED NOT NULL DEFAULT 0,
    `clone_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_slug` (`user_id`, `slug`),
    INDEX `idx_journeys_visibility` (`visibility`),
    INDEX `idx_journeys_status` (`status`),
    INDEX `idx_journeys_featured` (`is_featured`),
    INDEX `idx_journeys_category` (`category_id`),
    CONSTRAINT `fk_journeys_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_journeys_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- STEPS TABLE (journey milestones)
-- =============================================
CREATE TABLE `steps` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `journey_id` INT UNSIGNED NOT NULL,
    `step_number` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
    `estimated_days` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_journey_step` (`journey_id`, `step_number`),
    INDEX `idx_steps_status` (`status`),
    CONSTRAINT `fk_steps_journey` FOREIGN KEY (`journey_id`) REFERENCES `journeys`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DAILY LOGS TABLE
-- =============================================
CREATE TABLE `daily_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `step_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `log_date` DATE NOT NULL,
    `content` TEXT NOT NULL,
    `code_snippet` TEXT DEFAULT NULL,
    `code_language` VARCHAR(50) DEFAULT NULL,
    `youtube_url` VARCHAR(500) DEFAULT NULL,
    `external_links` JSON DEFAULT NULL,
    `github_commit_url` VARCHAR(500) DEFAULT NULL,
    `wakatime_minutes` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_step_user_date` (`step_id`, `user_id`, `log_date`),
    INDEX `idx_logs_user` (`user_id`),
    INDEX `idx_logs_date` (`log_date`),
    CONSTRAINT `fk_logs_step` FOREIGN KEY (`step_id`) REFERENCES `steps`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CLONED JOURNEYS TABLE
-- =============================================
CREATE TABLE `cloned_journeys` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `original_journey_id` INT UNSIGNED NOT NULL,
    `cloned_journey_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `last_synced_at` DATETIME DEFAULT NULL,
    `is_synced` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_clone` (`original_journey_id`, `user_id`),
    INDEX `idx_clone_user` (`user_id`),
    CONSTRAINT `fk_clone_original` FOREIGN KEY (`original_journey_id`) REFERENCES `journeys`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_clone_cloned` FOREIGN KEY (`cloned_journey_id`) REFERENCES `journeys`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_clone_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- PRIVATE NOTES (for cloned journeys)
-- =============================================
CREATE TABLE `private_notes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `step_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `note_content` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_note_step_user` (`step_id`, `user_id`),
    CONSTRAINT `fk_notes_step` FOREIGN KEY (`step_id`) REFERENCES `steps`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- STEP COMMENTS TABLE
-- =============================================
CREATE TABLE `step_comments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `step_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `content` TEXT NOT NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_comments_step` (`step_id`),
    INDEX `idx_comments_user` (`user_id`),
    CONSTRAINT `fk_comments_step` FOREIGN KEY (`step_id`) REFERENCES `steps`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `step_comments`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- AHA VOTES TABLE
-- =============================================
CREATE TABLE `aha_votes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `step_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `vote_type` ENUM('helpful','breakthrough') NOT NULL DEFAULT 'helpful',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_vote` (`step_id`, `user_id`),
    CONSTRAINT `fk_votes_step` FOREIGN KEY (`step_id`) REFERENCES `steps`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_votes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- STREAKS TABLE
-- =============================================
CREATE TABLE `streaks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `streak_date` DATE NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_streak` (`user_id`, `streak_date`),
    CONSTRAINT `fk_streaks_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- BADGES TABLE
-- =============================================
CREATE TABLE `badges` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-award',
    `criteria_type` ENUM('streak','journeys_completed','steps_completed','clones','aha_votes') NOT NULL,
    `criteria_value` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- USER BADGES TABLE
-- =============================================
CREATE TABLE `user_badges` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `badge_id` INT UNSIGNED NOT NULL,
    `earned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_badge` (`user_id`, `badge_id`),
    CONSTRAINT `fk_ubadge_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ubadge_badge` FOREIGN KEY (`badge_id`) REFERENCES `badges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- NOTIFICATIONS TABLE
-- =============================================
CREATE TABLE `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('sync_update','comment','aha_vote','badge','clone','system') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(500) DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user` (`user_id`),
    INDEX `idx_notif_read` (`is_read`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- ADMIN LOGS TABLE
-- =============================================
CREATE TABLE `admin_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `target_type` VARCHAR(50) DEFAULT NULL,
    `target_id` INT UNSIGNED DEFAULT NULL,
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_alog_admin` (`admin_id`),
    INDEX `idx_alog_action` (`action`),
    CONSTRAINT `fk_alog_admin` FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CONTACT MESSAGES TABLE
-- =============================================
CREATE TABLE `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `admin_reply` TEXT DEFAULT NULL,
    `status` ENUM('new','read','replied') NOT NULL DEFAULT 'new',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `replied_at` DATETIME DEFAULT NULL,
    INDEX `idx_contact_status` (`status`),
    INDEX `idx_contact_email` (`email`),
    INDEX `idx_contact_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SEED DEFAULT DATA
-- =============================================

-- Default categories
INSERT INTO `categories` (`name`, `slug`, `icon`) VALUES
('Web Development', 'web-development', 'bi-globe'),
('Mobile Development', 'mobile-development', 'bi-phone'),
('Data Science', 'data-science', 'bi-graph-up'),
('Machine Learning', 'machine-learning', 'bi-cpu'),
('DevOps', 'devops', 'bi-gear'),
('Cybersecurity', 'cybersecurity', 'bi-shield-lock'),
('Cloud Computing', 'cloud-computing', 'bi-cloud'),
('Game Development', 'game-development', 'bi-controller'),
('UI/UX Design', 'ui-ux-design', 'bi-palette'),
('Blockchain', 'blockchain', 'bi-link-45deg'),
('Algorithms', 'algorithms', 'bi-diagram-3'),
('Databases', 'databases', 'bi-server');

-- Default badges
INSERT INTO `badges` (`name`, `description`, `icon`, `criteria_type`, `criteria_value`) VALUES
('First Step', 'Complete your first step', 'bi-star', 'steps_completed', 1),
('Week Warrior', '7-day learning streak', 'bi-fire', 'streak', 7),
('Month Master', '30-day learning streak', 'bi-trophy', 'streak', 30),
('Journey Complete', 'Complete your first journey', 'bi-flag', 'journeys_completed', 1),
('Pathfinder', 'Complete 5 journeys', 'bi-compass', 'journeys_completed', 5),
('Trailblazer', 'Complete 10 journeys', 'bi-map', 'journeys_completed', 10),
('Inspiring', 'Get 10 Aha! votes', 'bi-lightbulb', 'aha_votes', 10),
('Thought Leader', 'Get 50 Aha! votes', 'bi-lightning', 'aha_votes', 50),
('Influencer', 'Have 5 journey clones', 'bi-share', 'clones', 5),
('Pioneer', 'Have 25 journey clones', 'bi-rocket', 'clones', 25),
('Step Machine', 'Complete 50 steps', 'bi-check2-all', 'steps_completed', 50),
('Centurion', 'Complete 100 steps', 'bi-award', 'steps_completed', 100);

-- Default admin account (password: Admin@123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`) VALUES
('admin', 'admin@somatrack.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', 'admin');
