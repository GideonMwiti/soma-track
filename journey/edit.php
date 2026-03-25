<?php
/**
 * SomaTrack - Edit Journey (redirect wrapper)
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: ' . SITE_URL . '/user/journeys.php');
    exit;
}
// Reuse create.php with ?id= param
$_GET['id'] = (int)$_GET['id'];
require __DIR__ . '/create.php';
