<?php
/**
 * SomaTrack - Public Header (for auth pages & landing)
 */
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?></title>
    <meta name="description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : 'Track, share, and prove your learning journey with SomaTrack.'; ?>">

    <!-- SEO & Canonical -->
    <link rel="canonical" href="<?= isset($canonicalUrl) ? sanitize($canonicalUrl) : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0] ?>">

    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="<?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?>">
    <meta property="og:description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : 'Track, share, and prove your learning journey with SomaTrack.'; ?>">
    <meta property="og:image" content="<?= isset($ogImage) ? sanitize($ogImage) : SITE_URL . '/assets/img/default-og.png' ?>">
    <meta property="og:url" content="<?= isset($canonicalUrl) ? sanitize($canonicalUrl) : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0] ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body class="<?= isset($bodyClass) ? $bodyClass : ''; ?>">
