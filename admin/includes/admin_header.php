<?php
/**
 * SomaTrack - Admin Dashboard Header
 */
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
requireAdmin();
$currentUser = getCurrentUser();
$notifCount = getUnreadNotificationCount($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | Admin' : 'Admin | ' . SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/dashboard.css" rel="stylesheet">
</head>
<body class="dashboard-body">

<nav class="navbar navbar-expand-lg navbar-dark st-topbar fixed-top">
    <div class="container-fluid px-4">
        <button class="btn btn-link text-light me-3 d-lg-none st-sidebar-toggle" type="button" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <a class="navbar-brand py-0" href="<?= SITE_URL ?>/admin/dashboard.php">
            <div class="st-logo-container">
                <div class="st-logo-icon" style="width:32px;height:32px;font-size:1.1rem;background:var(--st-danger);box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);"><i class="bi bi-shield-lock"></i></div>
                <div class="st-logo-text">
                    <span class="st-logo-name" style="font-size:1.1rem;"><?= SITE_NAME ?></span>
                    <span class="st-logo-tagline" style="font-size:0.6rem;">Admin Panel</span>
                </div>
            </div>
        </a>
        <div class="d-flex align-items-center ms-auto gap-3">
            <a href="<?= SITE_URL ?>" class="btn btn-sm btn-st-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Website</a>
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none dropdown-toggle st-user-dropdown d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="st-avatar-initial" style="width:30px;height:30px;font-size:0.8rem;">
                        <?= substr(sanitize($currentUser['username']), 0, 1) ?>
                    </div>
                    <span class="d-none d-md-inline text-light"><?= sanitize($currentUser['username']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end st-dropdown">
                    <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="st-wrapper">
    <nav class="st-sidebar" id="sidebar">
        <div class="st-sidebar-inner">
            <ul class="st-sidebar-nav">
                <li class="st-sidebar-heading">Admin</li>
                <li><a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-shield"></i><span>Admin Panel</span></a></li>
                <li><a href="<?= SITE_URL ?>/admin/users.php" class="<?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>"><i class="bi bi-people"></i><span>Users</span></a></li>
                <li><a href="<?= SITE_URL ?>/admin/journeys.php" class="<?= basename($_SERVER['PHP_SELF']) === 'journeys.php' ? 'active' : '' ?>"><i class="bi bi-map"></i><span>Journeys</span></a></li>
                <li><a href="<?= SITE_URL ?>/admin/comments.php" class="<?= basename($_SERVER['PHP_SELF']) === 'comments.php' ? 'active' : '' ?>"><i class="bi bi-chat-dots"></i><span>Comments</span></a></li>
                <li><a href="<?= SITE_URL ?>/admin/featured.php" class="<?= basename($_SERVER['PHP_SELF']) === 'featured.php' ? 'active' : '' ?>"><i class="bi bi-star"></i><span>Featured</span></a></li>
                <li><a href="<?= SITE_URL ?>/admin/contacts.php" class="<?= basename($_SERVER['PHP_SELF']) === 'contacts.php' ? 'active' : '' ?>"><i class="bi bi-envelope"></i><span>Contacts</span></a></li>
                <li class="st-sidebar-heading">System</li>
                <li><a href="<?= SITE_URL ?>/admin/logs.php" class="<?= basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : '' ?>"><i class="bi bi-journal-code"></i><span>Admin Logs</span></a></li>
            </ul>
        </div>
    </nav>
    <main class="st-content">
        <div class="container-fluid p-4">
            <?= displayFlash(); ?>
