<?php
/**
 * SomaTrack - Dynamic XML Sitemap
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$db = getDB();

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Static Pages
$staticPages = [
    '',
    '/explore.php',
    '/contact.php',
    '/terms.php',
    '/privacy.php',
    '/auth/login.php',
    '/auth/register.php'
];

foreach ($staticPages as $page) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars(SITE_URL . $page) . "</loc>\n";
    echo "    <changefreq>daily</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
}

// Public Journeys
$stmt = $db->query("SELECT id, updated_at FROM journeys WHERE visibility = 'public' ORDER BY updated_at DESC");
while ($row = $stmt->fetch()) {
    $date = date('Y-m-d', strtotime($row['updated_at']));
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars(SITE_URL . '/journey/view.php?id=' . $row['id']) . "</loc>\n";
    echo "    <lastmod>{$date}</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n";
}

echo "</urlset>\n";
