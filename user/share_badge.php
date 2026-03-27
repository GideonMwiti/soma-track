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
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Cinzel:wght@600&family=Inter:wght@400;600;700&display=swap');

        body {
            background-color: #020408;
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
        
        /* SomaTrack Neon-Glass Achievement Card */
        .st-share-card {
            aspect-ratio: 1 / 1;
            width: 100%;
            background: #0d1117;
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(102, 16, 242, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(0, 229, 255, 0.15) 0%, transparent 50%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 40px;
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        /* Neon Border Glow */
        .st-share-card::after {
            content: '';
            position: absolute;
            inset: 0;
            padding: 2px;
            border-radius: 40px;
            background: linear-gradient(135deg, #6610f2, #00e5ff);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Branding Top */
        .st-card-header {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .st-brand-mini {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .st-brand-logo-box {
            width: 40px;
            height: 40px;
            background: #6610f2;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .st-brand-logo-box i { font-size: 20px; }
        .st-brand-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        .st-brand-tagline {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #00e5ff;
            font-weight: 700;
        }

        /* Achievement Body */
        .st-card-main {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        
        .st-badge-crown {
            position: relative;
            margin-bottom: 20px;
        }
        .st-glow-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(102, 16, 242, 0.4) 0%, transparent 70%);
            animation: pulse 4s infinite ease-in-out;
        }
        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
        }

        .st-badge-icon-lg {
            font-size: 100px;
            color: #ffcc33;
            filter: drop-shadow(0 0 20px rgba(255, 204, 51, 0.4));
            position: relative;
            z-index: 2;
        }

        .st-achievement-title {
            font-family: 'Cinzel', serif;
            font-size: 32px;
            color: #ffcc33;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .st-recipient-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            margin: 10px 0;
            background: linear-gradient(135deg, #fff 0%, #a0a0a0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Modern Detail Strip */
        .st-detail-strip {
            margin-top: 30px;
            display: flex;
            gap: 40px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }
        .st-detail-item { text-align: left; }
        .st-label { font-size: 10px; text-transform: uppercase; color: #00e5ff; letter-spacing: 1.5px; margin-bottom: 4px; }
        .st-value { font-size: 15px; font-weight: 700; color: #fff; }

        .st-actions-bottom {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            width: 100%;
        }

        @media print {
            .st-actions-bottom, .st-back-link { display: none; }
            body { background: #000; padding: 0; }
            .achievement-card { border: none; box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>

    <div class="st-share-container">
        <div class="achievement-card" id="achievementCard">
            <!-- Header Branding -->
            <div class="st-card-header">
                <div class="st-brand-mini">
                    <div class="st-brand-logo-box">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="st-brand-name">Soma Track</div>
                </div>
                <div class="st-brand-tagline">Built by Learners for Learners</div>
            </div>

            <!-- Main Content -->
            <div class="st-card-main">
                <div class="st-badge-crown">
                    <div class="st-glow-ring"></div>
                    <i class="bi <?= $achievement['icon'] ?> st-badge-icon-lg"></i>
                </div>
                
                <div style="font-size: 14px; opacity: 0.7; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 10px;">Presented To</div>
                <h1 class="st-recipient-name"><?= sanitize($achievement['full_name']) ?></h1>
                
                <div style="margin-top: 15px;">
                    <div style="font-size: 22px; font-weight: 600; color: #fff; margin-bottom: 5px;">
                        for <span id="badgeTitleText" style="color: #ffcc33; font-family: 'Cinzel', serif;"><?= sanitize($achievement['name']) ?></span>
                    </div>
                    <?php if ($journeyTitle): ?>
                    <div style="font-size: 16px; color: #00e5ff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                        in <span id="journeyTitleText" style="color: #fff;"><?= sanitize($journeyTitle) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); width: 200px; padding-top: 15px;">
                    <span style="font-size: 11px; color: #00e5ff; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">Verified Achievement</span>
                </div>
            </div>

            <!-- Footer Small -->
            <div style="margin-top: 30px; font-size: 10px; opacity: 0.4; text-transform: uppercase; letter-spacing: 1px; text-align: center; width: 100%;">
                © <?= date('Y') ?> SomaTrack Global Learning Community
            </div>
        </div>

        <div class="st-actions-bottom no-print">
            <button class="btn btn-warning py-3 rounded-pill flex-fill fw-bold shadow" id="shareBtn">
                <i class="bi bi-share me-2"></i>Share Achievement
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
    <script src="<?= SITE_URL ?>/assets/js/app.js"></script>

</body>
</html>
