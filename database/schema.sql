-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 12:50 PM
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

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'delete_journey', 'journey', 9, 'Deleted: Docker & Containerization for Beginners', '::1', '2026-04-06 11:31:01'),
(2, 1, 'toggle_featured', 'journey', 5, 'Toggled featured status', '::1', '2026-04-06 11:31:10'),
(3, 1, 'toggle_featured', 'journey', 1, 'Toggled featured status', '::1', '2026-04-06 11:31:26'),
(4, 1, 'toggle_featured', 'journey', 7, 'Toggled featured status', '::1', '2026-04-06 11:31:31'),
(5, 1, 'toggle_featured', 'journey', 1, 'Toggled featured status', '::1', '2026-04-06 11:34:58'),
(6, 1, 'toggle_featured', 'journey', 2, 'Toggled featured status', '::1', '2026-04-06 11:35:01'),
(7, 1, 'toggle_featured', 'journey', 3, 'Toggled featured status', '::1', '2026-04-06 11:39:34'),
(8, 1, 'toggle_featured', 'journey', 3, 'Toggled featured status', '::1', '2026-04-06 11:39:57');

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

--
-- Dumping data for table `aha_votes`
--

INSERT INTO `aha_votes` (`id`, `step_id`, `user_id`, `vote_type`, `created_at`) VALUES
(2, 11, 3, 'breakthrough', '2026-04-06 13:46:23'),
(3, 11, 4, 'breakthrough', '2026-04-06 13:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bi-award',
  `criteria_type` enum('streak','journeys_completed','steps_completed','clones','aha_votes','consistent','community_helper','committed','diligent','aha_votes_received') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(13, 'The Metronome', 'Maintain a 14-day learning streak', 'bi-stopwatch', 'consistent', 14, '2026-04-05 12:27:00'),
(14, 'The Planner', 'Complete 1 journey on time', 'bi-calendar-check', 'committed', 1, '2026-04-05 12:27:00'),
(15, 'The Finisher', 'Complete 3 journeys on time', 'bi-check-all', 'committed', 3, '2026-04-05 12:27:00'),
(16, 'Good Samaritan', 'Give 10 community interactions', 'bi-heart', 'community_helper', 10, '2026-04-05 12:27:00'),
(17, 'Community Spark', 'Give 50 community interactions', 'bi-balloon-heart', 'community_helper', 50, '2026-04-05 12:27:00'),
(18, 'The Chronicler', 'Complete 1 journey with logs on every step', 'bi-journal-check', 'diligent', 1, '2026-04-05 12:27:00'),
(19, 'The Scribe', 'Complete 3 journeys with logs on every step', 'bi-vector-pen', 'diligent', 3, '2026-04-05 12:27:00');

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
(3, 2, 10, 5, '2026-04-06 13:43:56', 1, '2026-04-06 13:43:56'),
(4, 5, 11, 5, '2026-04-06 13:45:14', 1, '2026-04-06 13:45:14'),
(5, 2, 12, 3, '2026-04-06 13:46:39', 1, '2026-04-06 13:46:39'),
(6, 2, 13, 4, '2026-04-06 13:47:55', 1, '2026-04-06 13:47:55');

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
(14, 35, 4, '2026-04-05', 'API is functional. Mapped out all CRUD routes. Forgot to parse JSON body on PUT requests initially, but fixed it.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(15, 11, 2, '2026-04-06', 'Kicked off the Frontend roadmap today! I\'ve used standard <div> tags for absolutely everything in the past, but today I learned how crucial semantic tags like <header>, <nav>, <main>, and <footer> actually are. Not only do they improve SEO, but they make the website accessible for screen readers. Plus, my codebase already looks so much cleaner and easier to read.', '<header>\r\n  <h1>SomaTrack Learning</h1>\r\n  <nav>\r\n    <ul>\r\n      <li><a href=\"#dashboard\">Dashboard</a></li>\r\n      <li><a href=\"#journeys\">Journeys</a></li>\r\n    </ul>\r\n  </nav>\r\n</header>\r\n<main>\r\n  <article>\r\n    <h2>My First Entry</h2>\r\n    <p>Documenting the process.</p>\r\n  </article>\r\n</main>\r\n<footer>\r\n  <p>&copy; 2026 Sericsoft Innovations Ltd</p>\r\n</footer>', 'html', 'https://www.youtube.com/watch?v=UB1O30fR-EE', '[\"https://www.w3schools.com/html/html5_semantic_elements.asp\"]', 'https://github.com/', NULL, '2026-04-06 10:46:06', '2026-04-06 10:54:37'),
(16, 12, 2, '2026-04-06', 'Spent the whole day moving from Flexbox to CSS Grid. I always used Flexbox for everything, but I realized today that Flexbox is meant for 1D layouts (rows OR columns), while Grid is built for 2D layouts (rows AND columns simultaneously). The biggest breakthrough was learning grid-template-areas. Mapping out a complex dashboard layout went from a div-nesting nightmare to just a few lines of ASCII-like string mapping in my CSS. It feels like a superpower!', '/* My new favorite way to build page skeletons */\r\n.dashboard-container {\r\n  display: grid;\r\n  grid-template-columns: 250px 1fr;\r\n  grid-template-rows: 80px 1fr 60px;\r\n  grid-template-areas: \r\n    \"sidebar header\"\r\n    \"sidebar main\"\r\n    \"sidebar footer\";\r\n  height: 100vh;\r\n}\r\n\r\n.sidebar { grid-area: sidebar; }\r\n.header { grid-area: header; }\r\n.main { grid-area: main; }\r\n.footer { grid-area: footer; }', 'css', 'https://www.youtube.com/watch?v=jV8B24rSN5o', '[\"https://css-tricks.com/snippets/css/complete-guide-grid/\"]', 'https://github.com/', NULL, '2026-04-06 11:04:00', '2026-04-06 11:04:28'),
(17, 13, 2, '2026-04-06', 'Finally making things move! I spent today learning how to connect my JavaScript logic to the actual HTML using the Document Object Model (DOM). The biggest mind-blowing moment was learning about \"Event Delegation.\" Instead of attaching 50 separate event listeners to 50 individual buttons in a list, I can just attach ONE listener to the parent container and catch the events as they \"bubble up.\" This is going to save so much memory and make my code way cleaner!', '// BAD: Adding listeners to every single button (Memory heavy)\r\n// document.querySelectorAll(\'.delete-btn\').forEach(btn => {...})\r\n\r\n// GOOD: Event Delegation (One listener on the parent container)\r\nconst tableBody = document.querySelector(\'#data-table-body\');\r\n\r\ntableBody.addEventListener(\'click\', function(event) {\r\n  // Check if what we clicked was actually a delete button\r\n  if (event.target.classList.contains(\'delete-btn\')) {\r\n    const rowId = event.target.dataset.id;\r\n    console.log(`Deleting row ${rowId}`);\r\n    // Code to remove the element from the DOM...\r\n    event.target.closest(\'tr\').remove();\r\n  }\r\n});', 'javascript', 'https://www.youtube.com/watch?v=XF1_MlZ5l6M', '[\"https://developer.mozilla.org/en-US/docs/Web/JavaScript\"]', 'https://github.com/', NULL, '2026-04-06 11:11:34', '2026-04-06 11:13:18'),
(18, 14, 2, '2026-04-06', 'Today was all about talking to the outside world! I initially learned how to fetch external data using .then() Promise chains, but it quickly turned into a nested, confusing mess. Switching to async / await completely changed the game. The code looks synchronous and reads top-to-bottom, making it incredibly easy to reason about. I successfully pulled in a list of dummy users from an external REST API and dynamically rendered their profile cards to the DOM.', '// Clean API fetching with async/await and error handling\r\nasync function fetchUserData() {\r\n  try {\r\n    // Show loading spinner here\r\n    const response = await fetch(\'https://jsonplaceholder.typicode.com/users\');\r\n    \r\n    // fetch() doesn\'t automatically throw on HTTP errors like 404!\r\n    if (!response.ok) {\r\n      throw new Error(`HTTP error! Status: ${response.status}`);\r\n    }\r\n    \r\n    const users = await response.json();\r\n    renderUsersToDOM(users);\r\n    \r\n  } catch (error) {\r\n    console.error(\'Failed to fetch users:\', error);\r\n    // Show error UI to the user\r\n  } finally {\r\n    // Hide loading spinner here, regardless of success or failure\r\n  }\r\n}\r\n\r\nfetchUserData();', 'javascript', 'https://www.youtube.com/watch?v=V_Kr9OSfDeU', '[\"https://jsonplaceholder.typicode.com/\"]', 'https://github.com/', NULL, '2026-04-06 11:18:38', '2026-04-06 11:19:11'),
(19, 15, 2, '2026-04-06', 'Reached the final step! Moving from manual DOM manipulation to React\'s declarative style feels like literal magic. Instead of writing steps to update the UI (document.getElementById().innerText = ...), I just declare what the UI should look like based on the current State. If the state changes, React automatically re-renders the necessary components. I also learned how to pass data down the tree using Props, making my UI elements highly reusable. Built a fully functional tracking card component today!', 'import React, { useState } from \'react\';\r\n\r\n// Receiving \'title\' and \'totalDays\' as Props\r\nconst JourneyCard = ({ title, totalDays }) => {\r\n  // Using State to track progress\r\n  const [daysCompleted, setDaysCompleted] = useState(0);\r\n\r\n  const logDay = () => {\r\n    if (daysCompleted < totalDays) {\r\n      // Never mutate state directly! Always use the setter.\r\n      setDaysCompleted(prevDays => prevDays + 1);\r\n    }\r\n  };\r\n\r\n  return (\r\n    <div className=\"journey-card\">\r\n      <h3>{title}</h3>\r\n      <p>Progress: {daysCompleted} / {totalDays} days</p>\r\n      <button onClick={logDay}>Log Progress</button>\r\n    </div>\r\n  );\r\n};\r\n\r\nexport default JourneyCard;', 'javascript', 'https://www.youtube.com/watch?v=SqcY0GlETPk', '[\"https://react.dev/learn/state-a-components-memory\"]', 'https://github.com/', NULL, '2026-04-06 11:27:30', '2026-04-06 11:27:30'),
(20, 27, 3, '2026-04-06', 'I always thought building mobile apps meant downloading massive gigabytes of Android Studio and Xcode, but today I learned about Expo. It completely skips the heavy native compilation step for beginners! I initialized my first project using the terminal, downloaded the \'Expo Go\' app on my actual iPhone, scanned the QR code, and boom—my code was running live on my phone. The best part is \"Fast Refresh.\" Every time I hit save in VS Code, the app updates on my phone instantly without rebuilding. It feels like web development!', '# The exact commands I used to get started today:\r\n# 1. Initialize the new React Native project\r\nnpx create-expo-app my-mobile-app\r\n\r\n# 2. Navigate into the folder\r\ncd my-mobile-app\r\n\r\n# 3. Start the Metro bundler server\r\nnpx expo start\r\n\r\n# (Then I just scanned the QR code with my phone camera!)', 'bash', 'https://www.youtube.com/watch?v=0-S5a0eXPoc', '[\"https://reactnative.dev/docs/environment-setup\"]', 'https://github.com/', NULL, '2026-04-06 11:51:22', '2026-04-06 11:51:22'),
(21, 28, 3, '2026-04-06', 'Coming from a web development background, my muscle memory kept trying to type <div> and <p>. Today I had to completely rewire my brain for Native Components. I learned that React Native uses <View> instead of divs, <Text> instead of paragraphs or headings, and <Image> instead of standard img tags. The coolest part is understanding why: these components actually compile down to real, native iOS and Android UI elements under the hood, not just a web view! Built my first native profile card today.', 'import React from \'react\';\r\nimport { View, Text, Image, StyleSheet } from \'react-native\';\r\n\r\nexport default function ProfileCard() {\r\n  return (\r\n    // <View> is your new <div>\r\n    <View style={styles.cardContainer}>\r\n      <Image \r\n        source={{ uri: \'https://reactnative.dev/img/tiny_logo.png\' }} \r\n        style={styles.avatar} \r\n      />\r\n      {/* All text MUST be wrapped in a <Text> component */}\r\n      <Text style={styles.nameText}>Ezekiel Ayuoyi</Text>\r\n      <Text style={styles.roleText}>Software Innovator</Text>\r\n    </View>\r\n  );\r\n}\r\n\r\nconst styles = StyleSheet.create({\r\n  cardContainer: { padding: 20, alignItems: \'center\' },\r\n  avatar: { width: 100, height: 100, borderRadius: 50 },\r\n  nameText: { fontSize: 20, fontWeight: \'bold\', marginTop: 10 },\r\n  roleText: { fontSize: 16, color: \'gray\' }\r\n});', 'javascript', 'https://www.youtube.com/watch?v=UCbRTaX6i7g', '[\"https://reactnative.dev/docs/intro-react-native-components\"]', 'https://github.com/', NULL, '2026-04-06 12:05:20', '2026-04-06 12:11:20'),
(22, 29, 3, '2026-04-06', 'I thought my CSS Flexbox knowledge would transfer over perfectly, but I hit a massive wall today! I spent an hour wondering why my navigation bar items were stacking on top of each other instead of sitting side-by-side. The breakthrough? React Native Flexbox defaults to flexDirection: \'column\' instead of \'row\' like the web does! Because phone screens are vertical, the default axis is flipped. Once I explicitly set flexDirection: \'row\', everything snapped into place. I also learned how to use flex: 1 to make containers dynamically fill the remaining screen space.', 'import React from \'react\';\r\nimport { View, Text, StyleSheet } from \'react-native\';\r\n\r\nexport default function LayoutDemo() {\r\n  return (\r\n    // \'flex: 1\' makes this container take up the whole screen\r\n    <View style={styles.container}>\r\n      <View style={styles.header}>\r\n        <Text style={styles.headerText}>Sericsoft Mobile</Text>\r\n      </View>\r\n      \r\n      {/* The main content area */}\r\n      <View style={styles.content}>\r\n        <View style={styles.box} />\r\n        <View style={styles.box} />\r\n        <View style={styles.box} />\r\n      </View>\r\n    </View>\r\n  );\r\n}\r\n\r\nconst styles = StyleSheet.create({\r\n  container: { flex: 1, backgroundColor: \'#f0f0f0\' },\r\n  header: { \r\n    height: 60, \r\n    justifyContent: \'center\', \r\n    alignItems: \'center\', \r\n    backgroundColor: \'#6200ee\' \r\n  },\r\n  headerText: { color: \'white\', fontWeight: \'bold\' },\r\n  content: { \r\n    flex: 1, \r\n    flexDirection: \'row\', // OVERRIDING THE DEFAULT COLUMN BEHAVIOR\r\n    justifyContent: \'space-around\', \r\n    alignItems: \'center\' \r\n  },\r\n  box: { width: 50, height: 50, backgroundColor: \'coral\' }\r\n});', 'javascript', 'https://www.youtube.com/watch?v=MJ7P1JUyuFA', '[\"https://reactnative.dev/docs/flexbox\"]', 'https://github.com/', NULL, '2026-04-06 12:16:42', '2026-04-06 12:16:42'),
(23, 30, 3, '2026-04-06', 'UI is great, but today I learned how to make the app actually \"remember\" things using the useState hook. It’s fascinating how similar the logic is to the web frontend, but applied to native mobile components. I built my first interactive element: a custom habit-tracking button. Instead of standard HTML buttons, I learned to use <TouchableOpacity> to get that native mobile tap-feedback (the slight fade effect when you press it). Wiring it up to state means the UI automatically re-renders the counter every time the button is tapped. It feels so powerful!', 'import React, { useState } from \'react\';\r\nimport { View, Text, TouchableOpacity, StyleSheet } from \'react-native\';\r\n\r\nexport default function HabitTracker() {\r\n  // Initializing state with a default value of 0\r\n  const [streak, setStreak] = useState(0);\r\n\r\n  const handleTap = () => {\r\n    // Best practice: passing a callback to the setter when relying on previous state\r\n    setStreak(prevStreak => prevStreak + 1);\r\n  };\r\n\r\n  return (\r\n    <View style={styles.container}>\r\n      <Text style={styles.titleText}>Coding Streak: {streak} days</Text>\r\n      \r\n      {/* TouchableOpacity gives native visual feedback on press */}\r\n      <TouchableOpacity style={styles.button} onPress={handleTap}>\r\n        <Text style={styles.buttonText}>Log Today\'s Code</Text>\r\n      </TouchableOpacity>\r\n    </View>\r\n  );\r\n}\r\n\r\nconst styles = StyleSheet.create({\r\n  container: { padding: 20, alignItems: \'center\' },\r\n  titleText: { fontSize: 24, marginBottom: 20 },\r\n  button: { backgroundColor: \'#6200ee\', padding: 15, borderRadius: 8 },\r\n  buttonText: { color: \'white\', fontWeight: \'bold\' }\r\n});', 'javascript', 'https://www.youtube.com/watch?v=vbMwKmzNZb4', '[\"https://react.dev/reference/react/useState\"]', 'https://github.com/', NULL, '2026-04-06 12:21:44', '2026-04-06 12:21:44'),
(24, 31, 3, '2026-04-06', 'Coming from web development, I was used to changing URLs to move between pages. Today, I learned that mobile apps don\'t really have \"pages\"—they have screens stacked on top of each other like a deck of cards! I implemented React Navigation (specifically the Stack Navigator). When a user clicks a button to go to a new screen, the app \"pushes\" that new screen onto the top of the stack. When they hit the back button, it \"pops\" it off the stack to reveal the previous screen underneath. It\'s a completely different mental model, but it makes perfect sense for how mobile phones actually feel and work!', 'import React from \'react\';\r\nimport { NavigationContainer } from \'@react-navigation/native\';\r\nimport { createNativeStackNavigator } from \'@react-navigation/native-stack\';\r\nimport HomeScreen from \'./screens/HomeScreen\';\r\nimport DetailsScreen from \'./screens/DetailsScreen\';\r\n\r\n// Initialize the Stack\r\nconst Stack = createNativeStackNavigator();\r\n\r\nexport default function App() {\r\n  return (\r\n    <NavigationContainer>\r\n      {/* The Stack Navigator manages the routing history */}\r\n      <Stack.Navigator initialRouteName=\"Home\">\r\n        <Stack.Screen \r\n          name=\"Home\" \r\n          component={HomeScreen} \r\n          options={{ title: \'My Dashboard\' }} \r\n        />\r\n        <Stack.Screen \r\n          name=\"Details\" \r\n          component={DetailsScreen} \r\n        />\r\n      </Stack.Navigator>\r\n    </NavigationContainer>\r\n  );\r\n}\r\n\r\n// Inside HomeScreen.js, we would use:\r\n// <Button onPress={() => navigation.navigate(\'Details\')} />', 'javascript', 'https://www.youtube.com/watch?v=izZv6a99Roo', '[\"https://reactnavigation.org/docs/getting-started\"]', 'https://github.com/', NULL, '2026-04-06 12:27:31', '2026-04-06 12:27:31'),
(25, 32, 3, '2026-04-06', 'Reached the final step! Today I learned how to escape the \"browser sandbox\" and actually tap into the phone\'s physical hardware. On the web, accessing a webcam is a bit clunky, but with React Native and expo-camera, it\'s incredibly smooth. The biggest learning curve wasn\'t the camera component itself, but understanding mobile Permissions. I had to write logic to explicitly ask the user for permission to use their lens before the app would render the view. Once granted, I built a custom shutter button that captures the photo URI and saves it directly to local state so it can be previewed!', 'import React, { useState, useEffect } from \'react\';\r\nimport { StyleSheet, Text, View, TouchableOpacity } from \'react-native\';\r\nimport { Camera } from \'expo-camera\';\r\n\r\nexport default function CameraApp() {\r\n  const [hasPermission, setHasPermission] = useState(null);\r\n  const [type, setType] = useState(Camera.Constants.Type.back);\r\n\r\n  useEffect(() => {\r\n    (async () => {\r\n      // Prompting the user for physical camera access\r\n      const { status } = await Camera.requestCameraPermissionsAsync();\r\n      setHasPermission(status === \'granted\');\r\n    })();\r\n  }, []);\r\n\r\n  if (hasPermission === null) return <View />;\r\n  if (hasPermission === false) return <Text>No access to camera</Text>;\r\n\r\n  return (\r\n    <View style={styles.container}>\r\n      <Camera style={styles.camera} type={type}>\r\n        <View style={styles.buttonContainer}>\r\n          <TouchableOpacity\r\n            style={styles.button}\r\n            onPress={() => {\r\n              // Flip between front and back camera\r\n              setType(\r\n                type === Camera.Constants.Type.back\r\n                  ? Camera.Constants.Type.front\r\n                  : Camera.Constants.Type.back\r\n              );\r\n            }}>\r\n            <Text style={styles.text}> Flip Camera </Text>\r\n          </TouchableOpacity>\r\n        </View>\r\n      </Camera>\r\n    </View>\r\n  );\r\n}\r\n\r\nconst styles = StyleSheet.create({\r\n  container: { flex: 1 },\r\n  camera: { flex: 1 },\r\n  buttonContainer: { flex: 1, backgroundColor: \'transparent\', flexDirection: \'row\', margin: 20 },\r\n  button: { flex: 0.1, alignSelf: \'flex-end\', alignItems: \'center\' },\r\n  text: { fontSize: 18, color: \'white\', marginBottom: 10 },\r\n});', 'javascript', 'https://www.youtube.com/watch?v=9EoKurp6V0I', '[\"https://docs.expo.dev/versions/latest/sdk/camera/\"]', 'https://github.com/', NULL, '2026-04-06 12:33:01', '2026-04-06 12:33:01'),
(26, 38, 4, '2026-04-06', 'I\'ve always used Virtual Machines (VMs) for running different environments, but they take up gigabytes of RAM and take forever to boot because they have to emulate entire hardware stacks and run a full Guest OS. Today, the concept of a \"Container\" finally clicked. Docker doesn\'t emulate hardware; it just isolates processes! Containers share the underlying Host OS kernel, meaning they don\'t need their own operating system. They are incredibly lightweight (often just a few megabytes) and spin up in less than a second.', '# Testing how fast a container boots compared to a VM\r\n# This downloads and drops me into a fully isolated Ubuntu environment in ~2 seconds!\r\ndocker run -it ubuntu /bin/bash\r\n\r\n# Running this command inside the container proves the concept...\r\nuname -r \r\n# ...it outputs the exact same kernel version as my host machine!\r\n# The container is using my machine\'s kernel, just in an isolated space.', 'bash', 'https://www.youtube.com/watch?v=a1M_thDTqmU', '[\"https://www.ibm.com/topics/containers\"]', 'https://github.com/', NULL, '2026-04-06 12:39:41', '2026-04-06 12:39:41'),
(27, 39, 4, '2026-04-06', 'Up until today, I was just pulling pre-made images from Docker Hub. Today, I learned how to package our own custom applications by writing a Dockerfile. It\'s essentially a blueprint or a recipe script. You start with a base environment (like a lightweight version of Linux with Node.js installed), copy your source code into it, install your dependencies, and then tell it what command to run when the container starts. The syntax is surprisingly simple, but understanding that every single line creates a new \"Layer\" in the image completely changed how I think about building software!', '# Start from a lightweight Alpine Linux image with Node 18 installed\r\nFROM node:18-alpine\r\n\r\n# Set the working directory inside the container\r\nWORKDIR /usr/src/app\r\n\r\n# COPY package.json FIRST (Crucial for layer caching!)\r\nCOPY package*.json ./\r\n\r\n# Install dependencies inside the container\r\nRUN npm install\r\n\r\n# Copy the rest of the application code\r\nCOPY . .\r\n\r\n# Expose the port the app runs on\r\nEXPOSE 3000\r\n\r\n# Command to start the application\r\nCMD [\"node\", \"server.js\"]', 'other', 'https://www.youtube.com/watch?v=WmcdMiyqfZs', '[\"https://docs.docker.com/engine/reference/builder/\"]', 'https://github.com/', NULL, '2026-04-06 12:44:54', '2026-04-06 12:45:37'),
(28, 40, 4, '2026-04-06', 'I had a terrifying realization today: containers are meant to be disposable. If you spin up a database inside a container, add a bunch of users, and then the container stops or crashes... all that data is completely wiped out forever! To fix this, I learned about \"Volumes\". A volume basically punches a hole through the container\'s isolated file system and connects a specific folder inside the container directly to my laptop\'s physical hard drive. Now, I can completely destroy my PostgreSQL container, spin up a brand new one, and the data is still sitting safely on my machine, ready to be attached again!', '# How I ran a Postgres database with persistent storage today:\r\n\r\ndocker run -d \\\r\n  --name my-postgres-db \\\r\n  -e POSTGRES_PASSWORD=mysecretpassword \\\r\n  -v pgdata:/var/lib/postgresql/data \\\r\n  postgres:15\r\n\r\n# The \'-v pgdata:/var/lib/postgresql/data\' flag is the magic part.\r\n# It tells Docker to take the \'pgdata\' volume on my host machine and \r\n# map it to the folder inside the container where Postgres saves its files.', 'bash', 'https://www.youtube.com/watch?v=p2PH_YPCsis', '[\"https://docs.docker.com/storage/volumes/\"]', 'https://github.com/', NULL, '2026-04-06 12:50:23', '2026-04-06 12:50:23'),
(29, 41, 4, '2026-04-06', 'Up until today, I was manually typing out massive docker run commands in the terminal with 10 different flags just to start my database, and then doing it again to start my API. It was exhausting. Today I learned about Docker Compose. Instead of manual terminal commands, you declare your entire infrastructure in a single docker-compose.yml file. I can define my Node app, my Postgres database, the network that connects them, and the volumes that save their data all in one place. Now, I just type docker compose up -d and my entire full-stack application boots up together in perfect harmony. It\'s like magic!', '# docker-compose.yml\r\nversion: \'3.8\'\r\n\r\nservices:\r\n  # The Backend API Container\r\n  api:\r\n    build: ./api\r\n    ports:\r\n      - \"3000:3000\"\r\n    depends_on:\r\n      - database\r\n    environment:\r\n      - DB_HOST=database\r\n      - DB_USER=admin\r\n\r\n  # The Postgres Database Container\r\n  database:\r\n    image: postgres:15\r\n    environment:\r\n      - POSTGRES_PASSWORD=secret\r\n    volumes:\r\n      - pgdata:/var/lib/postgresql/data\r\n\r\n# Defining the persistent volume\r\nvolumes:\r\n  pgdata:', 'other', 'https://www.youtube.com/watch?v=SXwC9fSwct8', '[\"https://docs.docker.com/compose/\"]', 'https://github.com/', NULL, '2026-04-06 13:03:43', '2026-04-06 13:03:43');

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
(1, 2, 'Machine Learning with TensorFlow', 'ml-tensorflow-gideon', 'A 10-step deep dive into neural networks.', 4, 'public', 'active', 0, 10, 7, 0, 3, '2026-04-05 11:36:56', '2026-04-06 11:45:10'),
(2, 2, 'Frontend Web Development in 3 weeks', 'frontend-web-development-in-3-weeks', 'Mastering HTML, CSS, JS, and React.', 1, 'public', 'completed', 1, 5, 5, 3, 5, '2026-04-05 11:36:56', '2026-04-06 13:47:55'),
(3, 2, 'AWS Cloud Deployment Essentials', 'aws-cloud-gideon', 'Taking local web apps and deploying them to the cloud using AWS.', 7, 'public', 'completed', 0, 7, 7, 0, 1, '2026-04-05 11:36:56', '2026-04-06 11:39:57'),
(4, 3, 'UI/UX Foundations: Figma to Web', 'ui-ux-figma-sarah', 'User research, wireframing, and interactive prototypes.', 9, 'public', 'completed', 0, 4, 4, 1, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(5, 3, 'Cross-Platform Apps with React Native', 'react-native-sarah', 'Native mobile applications for iOS and Android using Expo.', 2, 'public', 'completed', 1, 6, 6, 1, 2, '2026-04-05 11:36:56', '2026-04-06 13:45:14'),
(6, 4, 'Backend APIs with Node.js & Express', 'node-express-mike', 'Building robust RESTful APIs with MongoDB.', 1, 'public', 'active', 0, 5, 3, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(7, 4, 'Docker & Containerization for Beginners', 'docker-basics-mike', 'Escape \"it works on my machine\" syndrome.', 5, 'public', 'completed', 1, 4, 4, 1, 1, '2026-04-05 11:36:56', '2026-04-06 12:35:33'),
(8, 5, 'UI/UX Foundations: Figma to Web', 'ui-ux-figma-elena-clone', 'User research, wireframing, and interactive prototypes.', 9, 'public', 'active', 0, 4, 0, 0, 0, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(10, 5, 'Frontend Web Development in 3 weeks', 'frontend-web-development-in-3-weeks', 'Mastering HTML, CSS, JS, and React.', 1, 'private', 'active', 0, 5, 0, 0, 1, '2026-04-06 13:43:56', '2026-04-06 13:43:56'),
(11, 5, 'Cross-Platform Apps with React Native', 'cross-platform-apps-with-react-native', 'Native mobile applications for iOS and Android using Expo.', 2, 'private', 'active', 0, 6, 0, 0, 1, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(12, 3, 'Frontend Web Development in 3 weeks', 'frontend-web-development-in-3-weeks', 'Mastering HTML, CSS, JS, and React.', 1, 'private', 'active', 0, 5, 0, 0, 1, '2026-04-06 13:46:39', '2026-04-06 13:46:39'),
(13, 4, 'Frontend Web Development in 3 weeks', 'frontend-web-development-in-3-weeks', 'Mastering HTML, CSS, JS, and React.', 1, 'private', 'active', 0, 5, 0, 0, 1, '2026-04-06 13:47:55', '2026-04-06 13:47:55');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 2, 'badge', 'New Badge Earned!', 'You earned the \"First Step\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 10:46:06'),
(2, 2, 'badge', 'New Badge Earned!', 'You earned the \"Journey Complete\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 10:46:06'),
(3, 2, 'badge', 'New Badge Earned!', 'You earned the \"The Planner\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 10:46:06'),
(4, 2, 'badge', 'New Badge Earned!', 'You earned the \"The Chronicler\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 10:46:06'),
(5, 3, 'badge', 'New Badge Earned!', 'You earned the \"First Step\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 11:51:22'),
(6, 3, 'badge', 'New Badge Earned!', 'You earned the \"Journey Complete\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 11:51:22'),
(7, 3, 'badge', 'New Badge Earned!', 'You earned the \"The Planner\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 11:51:22'),
(8, 3, 'badge', 'New Badge Earned!', 'You earned the \"The Chronicler\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 11:51:22'),
(9, 2, 'aha_vote', 'New Aha! Vote', '@Max marked \"HTML5 Semantics\" as Breakthrough', 'http://localhost/soma-track/journey/step.php?id=11', 0, '2026-04-06 12:00:55'),
(10, 4, 'badge', 'New Badge Earned!', 'You earned the \"First Step\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 12:39:41'),
(11, 4, 'badge', 'New Badge Earned!', 'You earned the \"Journey Complete\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 12:39:41'),
(12, 4, 'badge', 'New Badge Earned!', 'You earned the \"The Planner\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 12:39:41'),
(13, 4, 'badge', 'New Badge Earned!', 'You earned the \"The Chronicler\" badge!', 'http://localhost/soma-track/user/profile.php', 1, '2026-04-06 13:03:43'),
(14, 2, 'clone', 'Journey Cloned!', '@Sam cloned your journey \"Frontend Web Development in 3 weeks\"', 'http://localhost/soma-track/journey/view.php?id=2', 0, '2026-04-06 13:43:56'),
(15, 3, 'clone', 'Journey Cloned!', '@Sam cloned your journey \"Cross-Platform Apps with React Native\"', 'http://localhost/soma-track/journey/view.php?id=5', 1, '2026-04-06 13:45:14'),
(16, 2, 'aha_vote', 'New Aha! Vote', '@Max marked \"HTML5 Semantics\" as Breakthrough', 'http://localhost/soma-track/journey/step.php?id=11', 0, '2026-04-06 13:46:23'),
(17, 2, 'clone', 'Journey Cloned!', '@Max cloned your journey \"Frontend Web Development in 3 weeks\"', 'http://localhost/soma-track/journey/view.php?id=2', 0, '2026-04-06 13:46:39'),
(18, 2, 'aha_vote', 'New Aha! Vote', '@Moraa marked \"HTML5 Semantics\" as Breakthrough', 'http://localhost/soma-track/journey/step.php?id=11', 0, '2026-04-06 13:47:47'),
(19, 2, 'clone', 'Journey Cloned!', '@Moraa cloned your journey \"Frontend Web Development in 3 weeks\"', 'http://localhost/soma-track/journey/view.php?id=2', 0, '2026-04-06 13:47:55');

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

INSERT INTO `steps` (`id`, `journey_id`, `step_number`, `title`, `description`, `status`, `is_draft`, `estimated_days`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Python & NumPy Basics', 'Reviewing core data manipulation.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(2, 1, 2, 'Pandas DataFrames', 'Cleaning datasets for training.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(3, 1, 3, 'Intro to Scikit-Learn', 'Basic linear regression.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(4, 1, 4, 'TensorFlow Setup', 'Installing TF and configuring GPUs.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(5, 1, 5, 'Building a Dense Neural Network', 'Creating the first sequential model.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(6, 1, 6, 'Loss Functions & Optimizers', 'Understanding Adam and Cross-Entropy.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(7, 1, 7, 'Convolutional Neural Networks (CNNs)', 'Image classification architectures.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(8, 1, 8, 'Recurrent Neural Networks (RNNs)', 'Time-series data and text prediction.', 'in_progress', 0, 1, '2026-04-05 11:36:56', '2026-04-06 11:45:10'),
(9, 1, 9, 'Transfer Learning', 'Using pre-trained models like ResNet.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(10, 1, 10, 'Model Deployment', 'Exporting to TensorFlow Lite.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(11, 2, 1, 'HTML5 Semantics', 'Structuring web pages correctly.', 'completed', 0, 4, '2026-04-05 11:36:56', '2026-04-06 10:38:38'),
(12, 2, 2, 'Advanced CSS Flexbox & Grid', 'Mastering modern layouts.', 'completed', 0, 4, '2026-04-05 11:36:56', '2026-04-06 10:38:48'),
(13, 2, 3, 'JavaScript DOM Manipulation', 'Making static pages interactive.', 'completed', 0, 4, '2026-04-05 11:36:56', '2026-04-06 10:39:02'),
(14, 2, 4, 'Async JS & API Fetching', 'Handling promises and external data.', 'completed', 0, 4, '2026-04-05 11:36:56', '2026-04-06 10:39:14'),
(15, 2, 5, 'React.js Fundamentals', 'Components, State, and Props.', 'completed', 0, 4, '2026-04-05 11:36:56', '2026-04-06 10:39:25'),
(16, 3, 1, 'IAM Security', 'Securing the root account and creating roles.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(17, 3, 2, 'Virtual Private Clouds (VPC)', 'Public and private subnets.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(18, 3, 3, 'S3 Cloud Storage', 'Hosting static assets in buckets.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(19, 3, 4, 'EC2 Virtual Servers', 'Provisioning Linux instances.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(20, 3, 5, 'RDS Managed Databases', 'Setting up a PostgreSQL database layer.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(21, 3, 6, 'Route 53 Domains', 'Connecting domains to Elastic IPs.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(22, 3, 7, 'CI/CD Pipeline Automation', 'Using GitHub Actions for deployment.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(23, 4, 1, 'User Research & Personas', 'Defining the target audience.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(24, 4, 2, 'Wireframing & User Flow', 'Low-fidelity layout drafting.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(25, 4, 3, 'High-Fidelity Prototyping', 'Applying brand colors and UI design.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(26, 4, 4, 'Developer Handoff', 'Exporting assets and design systems.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(27, 5, 1, 'Environment Setup (Expo)', 'Initializing the mobile app framework.', 'completed', 0, 5, '2026-04-05 11:36:56', '2026-04-06 12:07:10'),
(28, 5, 2, 'Core Native Components', 'Using View, Text, and Image components.', 'completed', 0, 5, '2026-04-05 11:36:56', '2026-04-06 12:07:19'),
(29, 5, 3, 'Styling & Mobile Layouts', 'Mastering React Native Flexbox.', 'completed', 0, 5, '2026-04-05 11:36:56', '2026-04-06 12:07:30'),
(30, 5, 4, 'State Management', 'Using hooks for mobile interactions.', 'completed', 0, 5, '2026-04-05 11:36:56', '2026-04-06 12:07:39'),
(31, 5, 5, 'Stack Navigation', 'Moving between app screens securely.', 'completed', 0, 5, '2026-04-05 11:36:56', '2026-04-06 12:07:49'),
(32, 5, 6, 'Hardware APIs (Camera)', 'Accessing native phone features.', 'completed', 0, 5, '2026-04-05 11:36:56', '2026-04-06 12:07:58'),
(33, 6, 1, 'Node & Express Setup', 'Initializing the backend server.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(34, 6, 2, 'MongoDB & Mongoose', 'Connecting the NoSQL database.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(35, 6, 3, 'CRUD Operations', 'Building REST endpoints.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(36, 6, 4, 'JWT Authentication', 'Securing routes with tokens.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(37, 6, 5, 'Error Handling & Deployment', 'Global middleware and cloud hosting.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(38, 7, 1, 'The Container Concept', 'Understanding kernels vs VMs.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(39, 7, 2, 'Writing a Dockerfile', 'Packaging apps into images.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(40, 7, 3, 'Volumes & Persistence', 'Saving database state locally.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(41, 7, 4, 'Docker Compose', 'Orchestrating multi-container setups.', 'completed', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(42, 8, 1, 'User Research & Personas', 'Defining the target audience.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(43, 8, 2, 'Wireframing & User Flow', 'Low-fidelity layout drafting.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(44, 8, 3, 'High-Fidelity Prototyping', 'Applying brand colors and UI design.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(45, 8, 4, 'Developer Handoff', 'Exporting assets and design systems.', 'pending', 0, 1, '2026-04-05 11:36:56', '2026-04-05 11:36:56'),
(50, 10, 1, 'HTML5 Semantics', 'Structuring web pages correctly.', 'pending', 0, 4, '2026-04-06 13:43:56', '2026-04-06 13:43:56'),
(51, 10, 2, 'Advanced CSS Flexbox & Grid', 'Mastering modern layouts.', 'pending', 0, 4, '2026-04-06 13:43:56', '2026-04-06 13:43:56'),
(52, 10, 3, 'JavaScript DOM Manipulation', 'Making static pages interactive.', 'pending', 0, 4, '2026-04-06 13:43:56', '2026-04-06 13:43:56'),
(53, 10, 4, 'Async JS & API Fetching', 'Handling promises and external data.', 'pending', 0, 4, '2026-04-06 13:43:56', '2026-04-06 13:43:56'),
(54, 10, 5, 'React.js Fundamentals', 'Components, State, and Props.', 'pending', 0, 4, '2026-04-06 13:43:56', '2026-04-06 13:43:56'),
(55, 11, 1, 'Environment Setup (Expo)', 'Initializing the mobile app framework.', 'pending', 0, 5, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(56, 11, 2, 'Core Native Components', 'Using View, Text, and Image components.', 'pending', 0, 5, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(57, 11, 3, 'Styling & Mobile Layouts', 'Mastering React Native Flexbox.', 'pending', 0, 5, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(58, 11, 4, 'State Management', 'Using hooks for mobile interactions.', 'pending', 0, 5, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(59, 11, 5, 'Stack Navigation', 'Moving between app screens securely.', 'pending', 0, 5, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(60, 11, 6, 'Hardware APIs (Camera)', 'Accessing native phone features.', 'pending', 0, 5, '2026-04-06 13:45:14', '2026-04-06 13:45:14'),
(61, 12, 1, 'HTML5 Semantics', 'Structuring web pages correctly.', 'pending', 0, 4, '2026-04-06 13:46:39', '2026-04-06 13:46:39'),
(62, 12, 2, 'Advanced CSS Flexbox & Grid', 'Mastering modern layouts.', 'pending', 0, 4, '2026-04-06 13:46:39', '2026-04-06 13:46:39'),
(63, 12, 3, 'JavaScript DOM Manipulation', 'Making static pages interactive.', 'pending', 0, 4, '2026-04-06 13:46:39', '2026-04-06 13:46:39'),
(64, 12, 4, 'Async JS & API Fetching', 'Handling promises and external data.', 'pending', 0, 4, '2026-04-06 13:46:39', '2026-04-06 13:46:39'),
(65, 12, 5, 'React.js Fundamentals', 'Components, State, and Props.', 'pending', 0, 4, '2026-04-06 13:46:39', '2026-04-06 13:46:39'),
(66, 13, 1, 'HTML5 Semantics', 'Structuring web pages correctly.', 'pending', 0, 4, '2026-04-06 13:47:55', '2026-04-06 13:47:55'),
(67, 13, 2, 'Advanced CSS Flexbox & Grid', 'Mastering modern layouts.', 'pending', 0, 4, '2026-04-06 13:47:55', '2026-04-06 13:47:55'),
(68, 13, 3, 'JavaScript DOM Manipulation', 'Making static pages interactive.', 'pending', 0, 4, '2026-04-06 13:47:55', '2026-04-06 13:47:55'),
(69, 13, 4, 'Async JS & API Fetching', 'Handling promises and external data.', 'pending', 0, 4, '2026-04-06 13:47:55', '2026-04-06 13:47:55'),
(70, 13, 5, 'React.js Fundamentals', 'Components, State, and Props.', 'pending', 0, 4, '2026-04-06 13:47:55', '2026-04-06 13:47:55');

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

--
-- Dumping data for table `step_comments`
--

INSERT INTO `step_comments` (`id`, `step_id`, `user_id`, `parent_id`, `content`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 11, 2, NULL, 'Problem: I tried wrapping my entire page layout inside an <article> tag and ran my site through an accessibility checker. It threw a bunch of warnings.\r\nSolution: It turns out <main> should be the outer wrapper for the core content of the page, and you should only have ONE <main> tag per page. <article> is meant for specific, self-contained pieces of content inside the main area (like a blog post or a product card). Swapping them fixed the accessibility warnings!', 0, '2026-04-06 11:00:39', '2026-04-06 11:00:39'),
(2, 12, 2, NULL, 'Creator Note: A lot of people who clone this path ask me when to use Flexbox vs. Grid. My biggest takeaway after finishing this step: Use Flexbox for 1-dimensional layouts (like aligning items in a navigation bar or a row of buttons). Use Grid for 2-dimensional layouts (like the overall page skeleton or a complex photo gallery). Don\'t try to force Flexbox to do a Grid\'s job!', 0, '2026-04-06 11:08:06', '2026-04-06 11:08:06'),
(3, 12, 2, NULL, 'Problem I ran into today: My CSS Grid was blowing past the screen width and causing horizontal scrolling whenever I added a long, unbroken string of text.\r\nSolution: I learned that 1fr doesn\'t just mean \'take up remaining space\'; it means \'take up space but respect the minimum content width\'. Changing my column definition from 1fr to minmax(0, 1fr) forced the grid to respect the container boundary and wrap the text properly. Leaving this here to save you all a massive headache!', 0, '2026-04-06 11:08:26', '2026-04-06 11:08:26'),
(4, 14, 2, NULL, 'Creator Note: Pay close attention to lines 8-10 in my code snippet! A massive trap I fell into: the native fetch() API only rejects a promise on a complete network failure (like the user losing WiFi). It does NOT reject on HTTP errors like a 404 Not Found or a 500 Server Error. You always have to manually check if (!response.ok) before trying to parse the JSON. Do not skip that step!', 0, '2026-04-06 11:20:02', '2026-04-06 11:20:02'),
(5, 15, 2, NULL, 'Creator Note: The biggest mistake I made when starting React today was trying to update state like a normal variable (e.g., daysCompleted = 2;). React will completely ignore this and your UI won\'t update! State is strictly immutable. Always, always use the setter function provided by useState.', 0, '2026-04-06 11:28:06', '2026-04-06 11:28:06'),
(6, 27, 3, NULL, 'Creator Note: If your app gets stuck on \'Downloading JavaScript bundle...\' on your phone, check your Wi-Fi! Your phone and your computer MUST be connected to the exact same Wi-Fi network for Expo Go to find your local server. Also, turn off your VPN if you have one running on your laptop. That tripped me up for 20 minutes today!', 0, '2026-04-06 11:55:52', '2026-04-06 11:55:52'),
(7, 28, 3, NULL, 'Creator tip for the <Image> component: If you are loading an image from the internet (using uri), you absolutely MUST give it a fixed width and height in your styles. If you don\'t, the image will just silently fail to render and take up 0 pixels of space. It\'s the #1 most common bug for beginners!', 0, '2026-04-06 12:05:51', '2026-04-06 12:05:51'),
(8, 29, 3, NULL, 'Creator Note: Another quick styling tip I learned today—there is no px, em, or rem in React Native! You just pass naked numbers (like width: 50). React Native automatically calculates these as density-independent pixels, so your boxes will look proportionally the same size on a cheap Android phone and a flagship iPhone. Super cool!', 0, '2026-04-06 12:16:59', '2026-04-06 12:16:59'),
(9, 30, 3, NULL, 'Creator Note: One thing that tripped me up today React Native\'s onPress is the equivalent of the web\'s onClick. But if you accidentally write onPress={handleTap()} with the parentheses, the function will fire instantly the moment the app loads, resulting in an infinite loop crash! Always pass the reference: onPress={handleTap}.', 0, '2026-04-06 12:22:15', '2026-04-06 12:22:15'),
(10, 31, 3, NULL, 'Creator Note: The biggest mistake beginners make with React Navigation is forgetting to wrap their entire app inside the <NavigationContainer>! It must be at the very top level of your app (usually in App.js). If you put it inside a deeply nested component, you\'ll get bizarre error messages that are almost impossible to debug.', 0, '2026-04-06 12:27:50', '2026-04-06 12:27:50'),
(11, 32, 3, NULL, 'Creator Note: A massive warning for anyone testing this code the Expo Camera does NOT work on the iOS Simulator! You will just get a blank screen or a crash because Macs don\'t pass their webcams through to the iPhone simulator. You absolutely MUST test this on a real physical device using the Expo Go app.', 0, '2026-04-06 12:33:30', '2026-04-06 12:33:30'),
(12, 38, 4, NULL, 'The biggest takeaway here is that containers solve the \'It works on my machine!\' problem. Because the container packages its own file system, libraries, and dependencies, it will run exactly the same way on your laptop as it does on an AWS cloud server. No more dependency nightmares.', 0, '2026-04-06 12:40:37', '2026-04-06 12:40:37'),
(13, 39, 4, NULL, 'Notice how I copied package.json and ran npm install BEFORE copying the rest of the code? Docker caches each layer. If you copy all your files at once, any tiny change to a single .js file will bust the cache, and Docker will reinstall every single npm package from scratch next time you build. Splitting it up makes your builds 10x faster!', 0, '2026-04-06 12:46:58', '2026-04-06 12:46:58'),
(14, 40, 4, NULL, 'Make sure you understand the difference between \'Named Volumes\' and \'Bind Mounts\'. My snippet above uses a Named Volume (managed entirely by Docker), which is best for databases. A Bind Mount is when you link a specific folder on your desktop (like C:\\Users\\Vane\\code) to the container. Bind mounts are better for local development so your code updates in real-time!', 0, '2026-04-06 12:50:43', '2026-04-06 12:50:43'),
(15, 41, 4, NULL, 'The absolute best part about Docker Compose is that it automatically creates a custom internal network for your services. Notice how my API connects to the database using DB_HOST=database? It just uses the exact name of the service from the YAML file. Docker handles all the DNS resolution behind the scenes!', 0, '2026-04-06 13:04:28', '2026-04-06 13:04:28');

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

--
-- Dumping data for table `streaks`
--

INSERT INTO `streaks` (`id`, `user_id`, `streak_date`, `created_at`) VALUES
(1, 2, '2026-04-06', '2026-04-06 10:46:06'),
(2, 3, '2026-04-06', '2026-04-06 11:51:22'),
(3, 4, '2026-04-06', '2026-04-06 12:39:41');

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
(1, '@admin', 'admin@sericsoft.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Admin User', 'default-avatar.png', NULL, 'admin', 1, 0, 0, '2026-04-06', '2026-04-05 06:20:59', '2026-04-06 11:30:36'),
(2, '@Gideon', 'gideon@sericsoft.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Gideon Mwiti', 'default-avatar.png', NULL, 'user', 1, 1, 1, '2026-04-06', '2026-04-05 11:36:56', '2026-04-06 10:46:06'),
(3, '@Max', 'max@gmail.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Ezekiel Ayuoyi', 'default-avatar.png', NULL, 'user', 1, 1, 1, '2026-04-06', '2026-04-05 11:36:56', '2026-04-06 11:51:22'),
(4, '@Moraa', 'vane@gmail.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Vane Moraa', 'default-avatar.png', NULL, 'user', 1, 1, 1, '2026-04-06', '2026-04-05 11:36:56', '2026-04-06 12:39:41'),
(5, '@Sam', 'samuel@gmail.com', '$2y$10$MlEwM26rpDLDqhlCtYCpjOBnDV7Rhyp.taS7ohTNxxFlXARZe9/Si', 'Samuel Cheche', 'default-avatar.png', NULL, 'user', 1, 0, 0, '2026-04-06', '2026-04-05 11:36:56', '2026-04-06 13:43:30');

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
-- Dumping data for table `user_badges`
--

INSERT INTO `user_badges` (`id`, `user_id`, `badge_id`, `earned_at`) VALUES
(1, 2, 1, '2026-04-06 10:46:06'),
(2, 2, 4, '2026-04-06 10:46:06'),
(3, 2, 14, '2026-04-06 10:46:06'),
(4, 2, 18, '2026-04-06 10:46:06'),
(5, 3, 1, '2026-04-06 11:51:22'),
(6, 3, 4, '2026-04-06 11:51:22'),
(7, 3, 14, '2026-04-06 11:51:22'),
(8, 3, 18, '2026-04-06 11:51:22'),
(9, 4, 1, '2026-04-06 12:39:41'),
(10, 4, 4, '2026-04-06 12:39:41'),
(11, 4, 14, '2026-04-06 12:39:41'),
(12, 4, 18, '2026-04-06 13:03:43');

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `aha_votes`
--
ALTER TABLE `aha_votes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cloned_journeys`
--
ALTER TABLE `cloned_journeys`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_logs`
--
ALTER TABLE `daily_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `journeys`
--
ALTER TABLE `journeys`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `private_notes`
--
ALTER TABLE `private_notes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `step_comments`
--
ALTER TABLE `step_comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `streaks`
--
ALTER TABLE `streaks`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
