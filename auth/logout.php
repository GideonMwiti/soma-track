<?php
/**
 * SomaTrack - Logout
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

destroySession();
header('Location: ' . SITE_URL . '/');
exit;
