-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 10:44 AM
-- Server version: 8.0.45
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `somatrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int UNSIGNED NOT NULL,
  `admin_id` int UNSIGNED NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int UNSIGNED DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `aha_votes`
--

CREATE TABLE `aha_votes` (
  `id` int UNSIGNED NOT NULL,
  `step_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `vote_type` enum('helpful','breakthrough') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'helpful',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bi-award',
  `criteria_type` enum('streak','journeys_completed','steps_completed','clones','aha_votes','consistent','community_helper','committed','diligent','aha_votes_received') COLLATE utf8mb4_unicode_ci NOT NULL,
  `criteria_value` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `name`, `description`, `icon`, `criteria_type`, `criteria_value`, `created_at`) VALUES
(1, 'First Step', 'Complete your first step', 'bi-star', 'steps_completed', 1, '2026-04-05 06:20:59'),
(2, 'Week Warrior', '7-day learning streak', 'bi-fire', 'streak', 7, '2026-04-05 06:20:59'),
(3, 'Month Master', '30-day learning streak', 'bi-trophy', 'streak', 30, '2026-04-05 06:20:59'),
(4, 'Journey Complete', 'Complete your first journey', 'bi-flag', 'journeys_completed', 1, '2026-04-05 06:20:59'),
(5, 'Pathfinder', 'Complete 5 journeys', 'bi-compass', 'journeys_completed', 5, '2026-04-05 06:20:59'),
(6, 'Trailblazer', 'Complete 10 journeys', 'bi-map', 'journeys_completed', 10, '2026-04-05 06:20:59'),
(7, 'Inspiring', 'Get 10 Aha! votes', 'bi-lightbulb', 'aha_votes', 10, '2026-04-05 06:20:59'),
(8, 'Thought Leader', 'Get 50 Aha! votes', 'bi-lightning', 'aha_votes', 50, '2026-04-05 06:20:59'),
(9, 'Influencer', 'Have 5 journey clones', 'bi-share', 'clones', 5, '2026-04-05 06:20:59'),
(10, 'Pioneer', 'Have 25 journey clones', 'bi-rocket', 'clones', 25, '2026-04-05 06:20:59'),
(11, 'Step Machine', 'Complete 50 steps', 'bi-check2-all', 'steps_completed', 50, '2026-04-05 06:20:59'),
(12, 'Centurion', 'Complete 100 steps', 'bi-award', 'steps_completed', 100, '2026-04-05 06:20:59'),
(13, 'The Metronome', 'Maintain a 14-day learning streak', 'bi-stopwatch', 'consistent', 14, '2026-04-05 06:20:59'),
(14, 'The Planner', 'Complete 1 journey on time', 'bi-calendar-check', 'committed', 1, '2026-04-05 06:20:59'),
(15, 'The Finisher', 'Complete 3 journeys on time', 'bi-check-all', 'committed', 3, '2026-04-05 06:20:59'),
(16, 'Good Samaritan', 'Give 10 community interactions', 'bi-heart', 'community_helper', 10, '2026-04-05 06:20:59'),
(17, 'Community Spark', 'Give 50 community interactions', 'bi-balloon-heart', 'community_helper', 50, '2026-04-05 06:20:59'),
(18, 'The Chronicler', 'Complete 1 journey with logs on every step', 'bi-journal-check', 'diligent', 1, '2026-04-05 06:20:59'),
(19, 'The Scribe', 'Complete 3 journeys with logs on every step', 'bi-vector-pen', 'diligent', 3, '2026-04-05 06:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'bi-folder',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `created_at`) VALUES
(1, 'Web Development', 'web-development', 'bi-globe', '2026-04-05 06:20:59'),
(2, 'Mobile Development', 'mobile-development', 'bi-phone', '2026-04-05 06:20:59'),
(3, 'Data Science', 'data-science', 'bi-graph-up', '2026-04-05 06:20:59'),
(4, 'Machine Learning', 'machine-learning', 'bi-cpu', '2026-04-05 06:20:59'),
(5, 'DevOps', 'devops', 'bi-gear', '2026-04-05 06:20:59'),
(6, 'Cybersecurity', 'cybersecurity', 'bi-shield-lock', '2026-04-05 06:20:59'),
(7, 'Cloud Computing', 'cloud-computing', 'bi-cloud', '2026-04-05 06:20:59'),
(8, 'Game Development', 'game-development', 'bi-controller', '2026-04-05 06:20:59'),
(9, 'UI/UX Design', 'ui-ux-design', 'bi-palette', '2026-04-05 06:20:59'),
(10, 'Blockchain', 'blockchain', 'bi-link-45deg', '2026-04-05 06:20:59'),
(11, 'Algorithms', 'algorithms', 'bi-diagram-3', '2026-04-05 06:20:59'),
(12, 'Databases', 'databases', 'bi-server', '2026-04-05 06:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `cloned_journeys`
--

CREATE TABLE `cloned_journeys` (
  `id` int UNSIGNED NOT NULL,
  `original_journey_id` int UNSIGNED NOT NULL,
  `cloned_journey_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `last_synced_at` datetime DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cloned_journeys`
--

INSERT INTO `cloned_journeys` (`id`, `original_journey_id`, `cloned_journey_id`, `user_id`, `last_synced_at`, `is_synced`, `created_at`) VALUES
(1, 4, 8, 5, NULL, 1, '2026-04-05 11:36:56'),
(2, 7, 9, 5, NULL, 1, '2026-04-05 11:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','read','replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_logs`
--

CREATE TABLE `daily_logs` (
  `id` int UNSIGNED NOT NULL,
  `step_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `log_date` date NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_snippet` text COLLATE utf8mb4_unicode_ci,
  `code_language` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_links` json DEFAULT NULL,
  `github_commit_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wakatime_minutes` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_logs`
--

INSERT INTO `daily_logs` (`id`, `step_id`, `user_id`, `log_date`, `content`, `code_snippet`, `code_language`, `youtube_url`, `external_links`, `github_commit_url`, `wakatime_minutes`, `created_at`, `updated_at`) VALUES
(1, 16, 2, '2026-04-05', 'Security first! I locked down my AWS environment. Created an IAM admin user with MFA instead of using root.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(2, 17, 2, '2026-04-05', 'Networking is intimidating but essential. Built my own VPC from scratch with public/private subnets.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(3, 18, 2, '2026-04-05', 'Deployed my first cloud asset! Uploaded static portfolio files to an S3 bucket configured for public read access.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(4, 19, 2, '2026-04-05', 'Booted up my first virtual server. Launching EC2 was fast, but configuring Security Groups for port 22 and 80 took time.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(5, 20, 2, '2026-04-05', 'Provisioned a PostgreSQL instance inside my private subnet using RDS. Native backups are amazing.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(6, 21, 2, '2026-04-05', 'Used Route 53 to set up A-records pointing to my EC2 Elastic IP. Also generated a free SSL certificate.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(7, 22, 2, '2026-04-05', 'The final boss: automation! Set up a basic CI/CD pipeline. Pushing to GitHub now auto-deploys to EC2.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(8, 23, 3, '2026-04-05', 'Before drawing screens, I spent today mapping target users. Built two core personas to prevent feature creep.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(9, 24, 3, '2026-04-05', 'Started pushing pixels, kept it strictly grayscale. Much easier to figure out UX flow without color distractions.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(10, 25, 3, '2026-04-05', 'Applied the brand colors and typography scale. Linked screens together using Figmas prototype tab.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(11, 26, 3, '2026-04-05', 'Prepared final file for engineers. Made SVG icons exportable and ensured strict 8pt grid spacing.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(12, 33, 4, '2026-04-05', 'Set up the basic Express server today and created a simple GET route. Tested in Postman successfully.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(13, 34, 4, '2026-04-05', 'Hooked up the database using MongoDB Atlas. Wrote my first Mongoose schemas.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(14, 35, 4, '2026-04-05', 'API is functional. Mapped out all CRUD routes. Forgot to parse JSON body on PUT requests initially, but fixed it.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `journeys`
--

CREATE TABLE `journeys` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` int UNSIGNED DEFAULT NULL,
  `visibility` enum('public','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `status` enum('active','completed','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `total_steps` int UNSIGNED NOT NULL DEFAULT '0',
  `completed_steps` int UNSIGNED NOT NULL DEFAULT '0',
  `clone_count` int UNSIGNED NOT NULL DEFAULT '0',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journeys`
--

INSERT INTO `journeys` (`id`, `user_id`, `title`, `slug`, `description`, `category_id`, `visibility`, `status`, `is_featured`, `total_steps`, `completed_steps`, `clone_count`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 2, 'Machine Learning with TensorFlow', 'ml-tensorflow-gideon', 'A 10-step deep dive into neural networks.', 4, 'public', 'active', 0, 10, 7, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(2, 2, 'Frontend Web Development Roadmap', 'frontend-web-gideon', 'Mastering HTML, CSS, JS, and React.', 1, 'public', 'completed', 0, 5, 5, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(3, 2, 'AWS Cloud Deployment Essentials', 'aws-cloud-gideon', 'Taking local web apps and deploying them to the cloud using AWS.', 7, 'public', 'completed', 0, 7, 7, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(4, 3, 'UI/UX Foundations: Figma to Web', 'ui-ux-figma-sarah', 'User research, wireframing, and interactive prototypes.', 9, 'public', 'completed', 0, 4, 4, 1, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(5, 3, 'Cross-Platform Apps with React Native', 'react-native-sarah', 'Native mobile applications for iOS and Android using Expo.', 2, 'public', 'completed', 0, 6, 6, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(6, 4, 'Backend APIs with Node.js & Express', 'node-express-mike', 'Building robust RESTful APIs with MongoDB.', 1, 'public', 'active', 0, 5, 3, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(7, 4, 'Docker & Containerization for Beginners', 'docker-basics-mike', 'Escape \"it works on my machine\" syndrome.', 5, 'public', 'completed', 0, 4, 4, 1, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(8, 5, 'UI/UX Foundations: Figma to Web', 'ui-ux-figma-elena-clone', 'User research, wireframing, and interactive prototypes.', 9, 'public', 'active', 0, 4, 0, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(9, 5, 'Docker & Containerization for Beginners', 'docker-basics-elena-clone', 'Escape \"it works on my machine\" syndrome.', 5, 'public', 'active', 0, 4, 0, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type` enum('sync_update','comment','aha_vote','badge','clone','system') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `private_notes`
--

CREATE TABLE `private_notes` (
  `id` int UNSIGNED NOT NULL,
  `step_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `note_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `steps`
--

CREATE TABLE `steps` (
  `id` int UNSIGNED NOT NULL,
  `journey_id` int UNSIGNED NOT NULL,
  `step_number` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `estimated_days` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `steps`
--

INSERT INTO `steps` (`id`, `journey_id`, `step_number`, `title`, `description`, `status`, `estimated_days`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Python & NumPy Basics', 'Reviewing core data manipulation.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(2, 1, 2, 'Pandas DataFrames', 'Cleaning datasets for training.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(3, 1, 3, 'Intro to Scikit-Learn', 'Basic linear regression.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(4, 1, 4, 'TensorFlow Setup', 'Installing TF and configuring GPUs.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(5, 1, 5, 'Building a Dense Neural Network', 'Creating the first sequential model.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(6, 1, 6, 'Loss Functions & Optimizers', 'Understanding Adam and Cross-Entropy.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(7, 1, 7, 'Convolutional Neural Networks (CNNs)', 'Image classification architectures.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(8, 1, 8, 'Recurrent Neural Networks (RNNs)', 'Time-series data and text prediction.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(9, 1, 9, 'Transfer Learning', 'Using pre-trained models like ResNet.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(10, 1, 10, 'Model Deployment', 'Exporting to TensorFlow Lite.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(11, 2, 1, 'HTML5 Semantics', 'Structuring web pages correctly.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(12, 2, 2, 'Advanced CSS Flexbox & Grid', 'Mastering modern layouts.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(13, 2, 3, 'JavaScript DOM Manipulation', 'Making static pages interactive.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(14, 2, 4, 'Async JS & API Fetching', 'Handling promises and external data.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(15, 2, 5, 'React.js Fundamentals', 'Components, State, and Props.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(16, 3, 1, 'IAM Security', 'Securing the root account and creating roles.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(17, 3, 2, 'Virtual Private Clouds (VPC)', 'Public and private subnets.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(18, 3, 3, 'S3 Cloud Storage', 'Hosting static assets in buckets.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(19, 3, 4, 'EC2 Virtual Servers', 'Provisioning Linux instances.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(20, 3, 5, 'RDS Managed Databases', 'Setting up a PostgreSQL database layer.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(21, 3, 6, 'Route 53 Domains', 'Connecting domains to Elastic IPs.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(22, 3, 7, 'CI/CD Pipeline Automation', 'Using GitHub Actions for deployment.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(23, 4, 1, 'User Research & Personas', 'Defining the target audience.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(24, 4, 2, 'Wireframing & User Flow', 'Low-fidelity layout drafting.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(25, 4, 3, 'High-Fidelity Prototyping', 'Applying brand colors and UI design.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(26, 4, 4, 'Developer Handoff', 'Exporting assets and design systems.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(27, 5, 1, 'Environment Setup (Expo)', 'Initializing the mobile app framework.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(28, 5, 2, 'Core Native Components', 'Using View, Text, and Image components.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(29, 5, 3, 'Styling & Mobile Layouts', 'Mastering React Native Flexbox.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(30, 5, 4, 'State Management', 'Using hooks for mobile interactions.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(31, 5, 5, 'Stack Navigation', 'Moving between app screens securely.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(32, 5, 6, 'Hardware APIs (Camera)', 'Accessing native phone features.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(33, 6, 1, 'Node & Express Setup', 'Initializing the backend server.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(34, 6, 2, 'MongoDB & Mongoose', 'Connecting the NoSQL database.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(35, 6, 3, 'CRUD Operations', 'Building REST endpoints.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(36, 6, 4, 'JWT Authentication', 'Securing routes with tokens.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(37, 6, 5, 'Error Handling & Deployment', 'Global middleware and cloud hosting.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(38, 7, 1, 'The Container Concept', 'Understanding kernels vs VMs.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(39, 7, 2, 'Writing a Dockerfile', 'Packaging apps into images.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(40, 7, 3, 'Volumes & Persistence', 'Saving database state locally.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(41, 7, 4, 'Docker Compose', 'Orchestrating multi-container setups.', 'completed', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(42, 8, 1, 'User Research & Personas', 'Defining the target audience.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(43, 8, 2, 'Wireframing & User Flow', 'Low-fidelity layout drafting.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(44, 8, 3, 'High-Fidelity Prototyping', 'Applying brand colors and UI design.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(45, 8, 4, 'Developer Handoff', 'Exporting assets and design systems.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(46, 9, 1, 'The Container Concept', 'Understanding kernels vs VMs.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(47, 9, 2, 'Writing a Dockerfile', 'Packaging apps into images.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(48, 9, 3, 'Volumes & Persistence', 'Saving database state locally.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(49, 9, 4, 'Docker Compose', 'Orchestrating multi-container setups.', 'pending', 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `step_comments`
--

CREATE TABLE `step_comments` (
  `id` int UNSIGNED NOT NULL,
  `step_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `streaks`
--

CREATE TABLE `streaks` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `streak_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default-avatar.png',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `current_streak` int UNSIGNED NOT NULL DEFAULT '0',
  `longest_streak` int UNSIGNED NOT NULL DEFAULT '0',
  `last_activity_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `avatar`, `bio`, `role`, `is_active`, `current_streak`, `longest_streak`, `last_activity_date`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@sericsoft.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Admin User', 'default-avatar.png', NULL, 'admin', 1, 0, 0, '2026-04-05', '2026-04-05 06:20:59', '2026-04-05 11:31:07'),
(2, 'gideon', 'gideon@sericsoft.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Gideon Mwiti', 'default-avatar.png', NULL, 'user', 1, 0, 0, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(3, 'sarahtech', 'sarah@example.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'SarahTech', 'default-avatar.png', NULL, 'user', 1, 0, 0, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(4, 'devmike', 'mike@example.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'DevMike', 'default-avatar.png', NULL, 'user', 1, 0, 0, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(5, 'elenadesign', 'elena@example.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'ElenaDesign', 'default-avatar.png', NULL, 'user', 1, 0, 0, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `badge_id` int UNSIGNED NOT NULL,
  `earned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alog_admin` (`admin_id`),
  ADD KEY `idx_alog_action` (`action`);

--
-- Indexes for table `aha_votes`
--
ALTER TABLE `aha_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_vote` (`step_id`,`user_id`),
  ADD KEY `fk_votes_user` (`user_id`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cloned_journeys`
--
ALTER TABLE `cloned_journeys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_clone` (`original_journey_id`,`user_id`),
  ADD KEY `idx_clone_user` (`user_id`),
  ADD KEY `fk_clone_cloned` (`cloned_journey_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contact_status` (`status`),
  ADD KEY `idx_contact_email` (`email`),
  ADD KEY `idx_contact_created` (`created_at`);

--
-- Indexes for table `daily_logs`
--
ALTER TABLE `daily_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_step_user_date` (`step_id`,`user_id`,`log_date`),
  ADD KEY `idx_logs_user` (`user_id`),
  ADD KEY `idx_logs_date` (`log_date`);

--
-- Indexes for table `journeys`
--
ALTER TABLE `journeys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_slug` (`user_id`,`slug`),
  ADD KEY `idx_journeys_visibility` (`visibility`),
  ADD KEY `idx_journeys_status` (`status`),
  ADD KEY `idx_journeys_featured` (`is_featured`),
  ADD KEY `idx_journeys_category` (`category_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`),
  ADD KEY `idx_notif_read` (`is_read`);

--
-- Indexes for table `private_notes`
--
ALTER TABLE `private_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_note_step_user` (`step_id`,`user_id`),
  ADD KEY `fk_notes_user` (`user_id`);

--
-- Indexes for table `steps`
--
ALTER TABLE `steps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_journey_step` (`journey_id`,`step_number`),
  ADD KEY `idx_steps_status` (`status`);

--
-- Indexes for table `step_comments`
--
ALTER TABLE `step_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_step` (`step_id`),
  ADD KEY `idx_comments_user` (`user_id`),
  ADD KEY `fk_comments_parent` (`parent_id`);

--
-- Indexes for table `streaks`
--
ALTER TABLE `streaks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_streak` (`user_id`,`streak_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_active` (`is_active`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_badge` (`user_id`,`badge_id`),
  ADD KEY `fk_ubadge_badge` (`badge_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aha_votes`
--
ALTER TABLE `aha_votes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cloned_journeys`
--
ALTER TABLE `cloned_journeys`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_logs`
--
ALTER TABLE `daily_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `journeys`
--
ALTER TABLE `journeys`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_notes`
--
ALTER TABLE `private_notes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `step_comments`
--
ALTER TABLE `step_comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `streaks`
--
ALTER TABLE `streaks`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `fk_alog_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `aha_votes`
--
ALTER TABLE `aha_votes`
  ADD CONSTRAINT `fk_votes_step` FOREIGN KEY (`step_id`) REFERENCES `steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_votes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cloned_journeys`
--
ALTER TABLE `cloned_journeys`
  ADD CONSTRAINT `fk_clone_cloned` FOREIGN KEY (`cloned_journey_id`) REFERENCES `journeys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clone_original` FOREIGN KEY (`original_journey_id`) REFERENCES `journeys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clone_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_logs`
--
ALTER TABLE `daily_logs`
  ADD CONSTRAINT `fk_logs_step` FOREIGN KEY (`step_id`) REFERENCES `steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journeys`
--
ALTER TABLE `journeys`
  ADD CONSTRAINT `fk_journeys_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_journeys_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `private_notes`
--
ALTER TABLE `private_notes`
  ADD CONSTRAINT `fk_notes_step` FOREIGN KEY (`step_id`) REFERENCES `steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `steps`
--
ALTER TABLE `steps`
  ADD CONSTRAINT `fk_steps_journey` FOREIGN KEY (`journey_id`) REFERENCES `journeys` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `step_comments`
--
ALTER TABLE `step_comments`
  ADD CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `step_comments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_comments_step` FOREIGN KEY (`step_id`) REFERENCES `steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `streaks`
--
ALTER TABLE `streaks`
  ADD CONSTRAINT `fk_streaks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `fk_ubadge_badge` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ubadge_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
