<?php
/**
 * SomaTrack - Dashboard Header (for authenticated pages)
 */
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
requireLogin();
$currentUser = getCurrentUser();
$notifCount = getUnreadNotificationCount($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : 'Track, share, and prove your learning journey with SomaTrack.'; ?>">

    <!-- SEO & Canonical -->
    <link rel="canonical" href="<?= isset($canonicalUrl) ? sanitize($canonicalUrl) : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0] ?>">

    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="<?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : 'Track, share, and prove your learning journey with SomaTrack.'; ?>">
    <meta property="og:image" content="<?= isset($ogImage) ? sanitize($ogImage) : SITE_URL . '/assets/img/default-og.png' ?>">
    <meta property="og:url" content="<?= isset($canonicalUrl) ? sanitize($canonicalUrl) : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0] ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/dashboard.css" rel="stylesheet">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body class="dashboard-body">

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark st-topbar fixed-top">
    <div class="container-fluid px-4">
        <button class="btn btn-link text-light me-3 d-lg-none st-sidebar-toggle" type="button" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <a class="navbar-brand py-0" href="<?= SITE_URL ?>/user/dashboard.php">
            <div class="st-logo-container">
                <div class="st-logo-icon" style="width:32px;height:32px;font-size:1.1rem;"><i class="bi bi-mortarboard-fill"></i></div>
                <div class="st-logo-text">
                    <span class="st-logo-name" style="font-size:1.1rem;"><?= SITE_NAME ?></span>
                </div>
            </div>
        </a>
        <div class="d-flex align-items-center ms-auto gap-3">
            <a href="<?= SITE_URL ?>" class="btn btn-sm btn-st-secondary d-none d-md-inline"><i class="bi bi-arrow-left me-1"></i>Back to Website</a>
            <!-- Search -->
            <form class="d-none d-md-flex" action="<?= SITE_URL ?>/explore.php" method="GET">
                <div class="input-group input-group-sm st-search-bar">
                    <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control bg-transparent border-0" placeholder="Search journeys...">
                </div>
            </form>
            <!-- Notifications -->
            <a href="<?= SITE_URL ?>/user/notifications.php" class="btn btn-link text-light position-relative st-nav-icon">
                <i class="bi bi-bell fs-5"></i>
                <?php if ($notifCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger st-notif-badge">
                        <?= $notifCount > 99 ? '99+' : $notifCount ?>
                    </span>
                <?php endif; ?>
            </a>
            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none dropdown-toggle st-user-dropdown d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="st-avatar-initial" style="width:30px;height:30px;font-size:0.8rem;">
                        <?= strtoupper(substr(sanitize(ltrim(!empty($currentUser['full_name']) ? $currentUser['full_name'] : $currentUser['username'], '@')), 0, 1)) ?>
                    </div>
                    <span class="d-none d-md-inline text-light"><?= sanitize(!empty($currentUser['full_name']) ? $currentUser['full_name'] : ltrim($currentUser['username'], '@')) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end st-dropdown">
                    <li><a class="dropdown-item" href="<?= SITE_URL ?>/user/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="<?= SITE_URL ?>/user/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <?php if (isAdmin()): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/admin/dashboard.php"><i class="bi bi-shield me-2"></i>Admin Panel</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar + Content Wrapper -->
<div class="st-wrapper">
    <!-- Sidebar -->
    <nav class="st-sidebar" id="sidebar">
        <div class="st-sidebar-inner">
            <ul class="st-sidebar-nav">
                <li class="st-sidebar-heading">Main</li>
                <li><a href="<?= SITE_URL ?>/user/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>
                <?php if ($currentUser['role'] !== 'admin'): ?>
                    <li><a href="<?= SITE_URL ?>/user/journeys.php" class="<?= basename($_SERVER['PHP_SELF']) === 'journeys.php' ? 'active' : '' ?>"><i class="bi bi-map"></i><span>My Journeys</span></a></li>
                    <li><a href="<?= SITE_URL ?>/user/cloned.php" class="<?= basename($_SERVER['PHP_SELF']) === 'cloned.php' ? 'active' : '' ?>"><i class="bi bi-copy"></i><span>Cloned Journeys</span></a></li>
                <?php endif; ?>
                <li><a href="<?= SITE_URL ?>/explore.php" class="<?= basename($_SERVER['PHP_SELF']) === 'explore.php' ? 'active' : '' ?>"><i class="bi bi-compass"></i><span>Explore</span></a></li>

                <li class="st-sidebar-heading">Activity</li>
                <?php if ($currentUser['role'] !== 'admin'): ?>
                    <li><a href="<?= SITE_URL ?>/user/logs.php" class="<?= basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i><span>Daily Logs</span></a></li>
                    <li><a href="<?= SITE_URL ?>/user/streaks.php" class="<?= basename($_SERVER['PHP_SELF']) === 'streaks.php' ? 'active' : '' ?>"><i class="bi bi-fire"></i><span>Streaks</span></a></li>
                    <li><a href="<?= SITE_URL ?>/user/badges.php" class="<?= basename($_SERVER['PHP_SELF']) === 'badges.php' ? 'active' : '' ?>"><i class="bi bi-award"></i><span>Badges</span></a></li>
                <?php endif; ?>

                <li class="st-sidebar-heading">Account</li>
                <li><a href="<?= SITE_URL ?>/user/profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>"><i class="bi bi-person"></i><span>Profile</span></a></li>
                <li><a href="<?= SITE_URL ?>/user/notifications.php" class="<?= basename($_SERVER['PHP_SELF']) === 'notifications.php' ? 'active' : '' ?>"><i class="bi bi-bell"></i><span>Notifications</span>
                    <?php if ($notifCount > 0): ?><span class="badge bg-danger ms-auto"><?= $notifCount ?></span><?php endif; ?>
                </a></li>
                <li><a href="<?= SITE_URL ?>/user/settings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>"><i class="bi bi-gear"></i><span>Settings</span></a></li>
            </ul>

            <!-- Streak Widget -->
            <?php if ($currentUser['role'] !== 'admin'): ?>
                <?php $userStats = getUserStats($currentUser['id']); ?>
                <div class="st-streak-widget">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-fire text-warning fs-4 me-2"></i>
                        <div>
                            <div class="fw-semibold"><?= sanitize($currentUser['full_name']) ?></div>
                            <small class="text-muted"><?= $userStats['current_streak'] ?>-day streak</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="st-content">
        <div class="container-fluid p-4">
            <?= displayFlash(); ?>
