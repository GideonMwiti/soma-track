<?php
/**
 * SomaTrack - Visual Badge Share Page
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$badgeId = isset($_GET['b']) ? (int)$_GET['b'] : 0;
$userId  = isset($_GET['u']) ? (int)$_GET['u'] : 0;

if (!$badgeId || !$userId) {
    die("Achievement not found.");
}

$db = getDB();

// Verify badge exists and user has earned it
$stmt = $db->prepare("SELECT b.*, ub.earned_at, u.full_name FROM badges b 
                      JOIN user_badges ub ON b.id = ub.badge_id 
                      JOIN users u ON ub.user_id = u.id
                      WHERE b.id = ? AND u.id = ?");
$stmt->execute([$badgeId, $userId]);
$achievement = $stmt->fetch();

if (!$achievement) {
    die("Achievement not found or not yet earned.");
}

// Fetch most recent completed journey for context
$journeyTitle = "";
$jStmt = $db->prepare("SELECT title FROM journeys WHERE user_id = ? AND status = 'completed' ORDER BY updated_at DESC LIMIT 1");
$jStmt->execute([$userId]);
$journey = $jStmt->fetch();
if ($journey) {
    $journeyTitle = $achievement['journey_title'] ?? null;

// Map criteria to human-friendly achievement names
$criteriaMap = [
    'streak' => 'Consistency',
    'journeys_completed' => 'Completion',
    'steps_completed' => 'Impact',
    'clones' => 'Impact',
    'aha_votes' => 'Community',
    'consistent' => 'Consistency',
    'community_helper' => 'Community',
    'committed' => 'Commitment',
    'diligent' => 'Diligence',
    'aha_votes_received' => 'Impact'
];
$achievementLabel = $criteriaMap[$achievement['criteria_type']] ?? 'Excellence';
}

$pageTitle = $achievement['name'] . " - SomaTrack Achievement";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?></title>
    
    <!-- OpenGraph Tags for Social Sharing -->
    <meta property="og:title" content="<?= sanitize($achievement['full_name']) ?> earned a badge on SomaTrack!">
    <meta property="og:description" content="Achievement: <?= sanitize($achievement['name']) ?> - <?= sanitize($achievement['description']) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SomaTrack">
    
    <!-- External Assets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;700;800;900&display=swap');

        body {
            background-color: #05070a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            font-family: 'Inter', sans-serif;
            color: #fff;
        }
        .st-share-container {
            max-width: 650px;
            width: 100%;
        }
        .st-share-card {
            aspect-ratio: 1 / 1;
            width: 100%;
            background: #0d1117;
            background-image: 
                radial-gradient(circle at 50% 50%, #1a2333 0%, #0d1117 100%),
                url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            border: 4px solid #6610f2;
            border-radius: 40px;
            padding: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(102, 16, 242, 0.3), inset 0 0 100px rgba(0,0,0,0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        
        .st-corner-glow {
            position: absolute;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(102, 16, 242, 0.2) 0%, transparent 70%);
            z-index: 1;
        }
        .top-left { top: -50px; left: -50px; }
        .top-right { top: -50px; right: -50px; }
        .bottom-left { bottom: -50px; left: -50px; }
        .bottom-right { bottom: -50px; right: -50px; }

        .st-share-content {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        /* Official SomaTrack Logo Style */
        .st-official-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        .st-logo-box {
            width: 65px;
            height: 65px;
            background: #6610f2;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(102, 16, 242, 0.4);
        }
        .st-logo-box i {
            font-size: 35px;
            color: #fff;
        }
        .st-logo-text-wrapper {
            text-align: left;
        }
        .st-logo-main-text {
            font-size: 38px;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            margin-bottom: 4px;
        }
        .st-logo-tagline {
            font-size: 14px;
            font-weight: 800;
            color: #00e5ff;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .st-cert-header {
            text-transform: uppercase;
            letter-spacing: 6px;
            font-size: 16px;
            color: #fff;
            opacity: 0.8;
            font-weight: 400;
        }

        .st-badge-container {
            position: relative;
            margin: 10px 0;
        }
        .st-badge-shield {
            width: 200px;
            height: 200px;
            filter: drop-shadow(0 0 20px rgba(102, 16, 242, 0.4));
        }
        
        .st-badge-name {
            font-family: 'Cinzel', serif;
            font-size: 28px;
            color: #ffcc33;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(0,0,0,1);
            margin-top: -10px;
        }

        .st-learner-name {
            font-size: 52px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
            margin: 10px 0;
            text-shadow: 0 0 30px rgba(255,255,255,0.2);
        }

        .st-detail-panel {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 15px 30px;
            width: 100%;
            max-width: 500px;
        }
        .st-detail-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #00e5ff;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .st-detail-value {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
        }

        .st-actions-bottom {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            width: 100%;
        }

        @media print {
            .st-actions-bottom, .st-back-link { display: none; }
            body { background: #000; padding: 0; }
            .st-share-card { border: none; box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>

    <div class="st-share-container">
        <div class="st-share-card" id="achievementCard">
            <!-- Decorative Glows -->
            <div class="st-corner-glow top-left"></div>
            <div class="st-corner-glow top-right"></div>
            <div class="st-corner-glow bottom-left"></div>
            <div class="st-corner-glow bottom-right"></div>

            <div class="st-share-content">
                <!-- Official Branding -->
                <div class="st-official-logo">
                    <div class="st-logo-box">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="st-logo-text-wrapper">
                        <div class="st-logo-main-text">Soma Track</div>
                        <div class="st-logo-tagline">Built by Learners for Learners</div>
                    </div>
                </div>

                <div class="st-cert-header">Certificate of Achievement</div>
                
                <!-- Badge Visual -->
                <div class="st-badge-container">
                    <svg class="st-badge-shield" viewBox="0 0 100 100">
                        <defs>
                            <linearGradient id="shieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#6610f2;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#00e5ff;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <path d="M50 5 L85 20 V50 C85 70 50 95 50 95 C50 95 15 70 15 50 V20 L50 5Z" fill="none" stroke="url(#shieldGrad)" stroke-width="2" />
                        <path d="M50 10 L80 23 V50 C80 65 50 85 50 85 C50 85 20 65 20 50 V23 L50 10Z" fill="rgba(102, 16, 242,0.1)" />
                    </svg>
                    <i class="bi <?= $achievement['icon'] ?>" style="position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); font-size: 80px; color: #ffcc33; filter: drop-shadow(0 0 15px #ffcc33);"></i>
                </div>

                <div class="st-badge-name"><?= sanitize($achievement['name']) ?></div>

                <div class="st-learner-name"><?= sanitize($achievement['full_name']) ?></div>

                <div class="st-detail-panel">
                    <div class="row">
                        <div class="col-6" style="border-right: 1px solid rgba(255,255,255,0.1);">
                            <div class="st-detail-label">Achievement</div>
                            <div class="st-detail-value"><?= sanitize($achievementLabel) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="st-detail-label">Journey</div>
                            <div class="st-detail-value text-truncate"><?= $journeyTitle ? sanitize($journeyTitle) : 'N/A' ?></div>
                        </div>
                    </div>
                </div>

                <div class="mt-3" style="font-size:11px; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:2px;">
                    Verified by SomaTrack Platform
                </div>
            </div>
        </div>

        <div class="st-actions-bottom no-print">
            <button class="btn btn-warning py-3 rounded-pill flex-fill fw-bold shadow" id="shareBtn">
                <i class="bi bi-share me-2"></i>Share Achievement
            </button>
            <button class="btn btn-outline-info py-3 rounded-pill flex-fill fw-bold" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-2"></i>Save as PDF
            </button>
        </div>
        <div class="text-center mt-3 st-back-link">
            <a href="<?= SITE_URL ?>/user/badges.php" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Back to My Badges
            </a>
        </div>
    </div>

    <script>
        document.getElementById('shareBtn').addEventListener('click', function() {
            const shareData = {
                title: 'SomaTrack Achievement',
                text: '🌟 <?= sanitize($achievement["full_name"]) ?> earned the "<?= sanitize($achievement["name"]) ?>" badge on SomaTrack! 🌟',
                url: window.location.href
            };
            
            if (navigator.share) {
                navigator.share(shareData).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(shareData.text + " " + shareData.url).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        });
    </script>

</body>
</html>
