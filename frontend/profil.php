<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require_once '../backend/db.php';
require_once '../backend/rank_helper.php';

$current_user_id = $_SESSION['user_id'];
$is_own_profile  = true;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $target_user_id = (int)$_GET['id'];
    if ($target_user_id !== $current_user_id) $is_own_profile = false;
} else {
    $target_user_id = $current_user_id;
}

$user_stmt = $db->prepare("SELECT first_name, last_name, email FROM USERS WHERE user_id = ?");
$user_stmt->execute([$target_user_id]);
$user = $user_stmt->fetch();
if (!$user) die("Kullanıcı bulunamadı.");

$stats_stmt = $db->prepare("SELECT
    COUNT(*) as total_capsules,
    SUM(CASE WHEN status='Locked' THEN 1 ELSE 0 END) as locked_capsules,
    SUM(CASE WHEN status IN ('Unlocked', 'Opened') THEN 1 ELSE 0 END) as unlocked_capsules
    FROM CAPSULES WHERE sender_id = ?");
$stats_stmt->execute([$target_user_id]);
$stats = $stats_stmt->fetch();

$total      = (int)$stats['total_capsules'];
$rank       = getRankInfo($total);
$allTiers   = getAllTiers();
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($user['first_name'].' '.$user['last_name']) . "&background=00f2fe&color=05050a&size=150&bold=true";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil — <?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?></title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .profile-page { max-width: 860px; margin: 40px auto; padding: 0 20px 60px; }

        /* --- HERO KART --- */
        .profile-hero {
            background: linear-gradient(145deg, rgba(14,17,36,0.95), rgba(5,5,10,0.98));
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 28px;
            padding: 50px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .profile-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent), transparent);
            opacity: 0.4;
        }
        .profile-avatar {
            width: 120px; height: 120px;
            border-radius: 50%;
            border: 3px solid var(--accent);
            box-shadow: 0 0 30px rgba(0,242,254,0.4);
            margin-bottom: 20px;
            transition: transform 0.3s;
            cursor: default;
        }
        .profile-avatar:hover { transform: scale(1.08) rotate(4deg); }
        .profile-name {
            font-size: 2.4rem; font-weight: 800; margin: 0 0 4px;
            background: linear-gradient(to right, var(--accent), var(--accent-purple));
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .rank-badge-large {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 7px 20px; border-radius: 50px;
            font-size: 1rem; font-weight: 700; letter-spacing: 1px;
            margin: 12px 0 20px; border: 1px solid;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }
        .profile-email {
            color: var(--text-secondary);
            font-size: 1rem; margin-bottom: 0;
            letter-spacing: 1px;
        }

        /* --- İLERLEME ÇUBUĞU --- */
        .rank-progress-card {
            background: rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 30px;
        }
        .rank-progress-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 16px;
        }
        .rank-current-small { display: flex; align-items: center; gap: 8px; }
        .rank-current-small .icon { font-size: 1.8rem; }
        .rank-current-small .name {
            font-size: 1rem; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
        }
        .rank-next-small {
            display: flex; align-items: center; gap: 8px;
            opacity: 0.6; font-size: 0.85rem;
            text-align: right;
        }
        .rank-next-small .icon { font-size: 1.4rem; }
        .progress-bar-bg {
            height: 8px; background: rgba(255,255,255,0.06);
            border-radius: 10px; overflow: hidden; position: relative;
        }
        .progress-bar-fill {
            height: 100%; border-radius: 10px;
            transition: width 1s cubic-bezier(0.25,1,0.5,1);
            position: relative;
        }
        .progress-bar-fill::after {
            content: '';
            position: absolute; right: 0; top: -3px;
            width: 14px; height: 14px; border-radius: 50%;
            background: inherit;
            box-shadow: 0 0 10px currentColor;
        }
        .progress-text {
            display: flex; justify-content: space-between;
            font-size: 0.8rem; color: var(--text-secondary);
            margin-top: 10px;
        }

        /* --- İSTATİSTİKLER --- */
        .profile-stats {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 16px; margin-bottom: 30px;
        }
        .stat-box {
            background: rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px; padding: 28px 20px;
            text-align: center; transition: all 0.3s ease;
        }
        .stat-box:hover {
            border-color: rgba(0,242,254,0.2);
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .stat-number {
            font-family: 'Courier New', monospace;
            font-size: 2.8rem; font-weight: 900;
            color: var(--accent); margin-bottom: 6px;
            text-shadow: 0 0 15px rgba(0,242,254,0.4);
        }
        .stat-label {
            color: var(--text-secondary); font-size: 0.8rem;
            text-transform: uppercase; letter-spacing: 2px;
        }

        /* --- ROZET YOL HARİTASI --- */
        .rank-roadmap {
            background: rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px; padding: 28px;
        }
        .roadmap-title {
            font-size: 1rem; font-weight: 700; color: var(--text-secondary);
            letter-spacing: 2px; text-transform: uppercase;
            margin-bottom: 24px;
        }
        .roadmap-tiers {
            display: flex; flex-direction: column; gap: 0;
        }
        .tier-row {
            display: flex; align-items: center; gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            opacity: 0.4;
            transition: all 0.3s;
        }
        .tier-row:last-child { border-bottom: none; }
        .tier-row.achieved { opacity: 1; }
        .tier-row.current {
            opacity: 1;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 14px 16px;
            margin: 0 -16px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .tier-icon { font-size: 1.8rem; width: 36px; text-align: center; }
        .tier-info { flex: 1; }
        .tier-name { font-weight: 700; font-size: 0.95rem; }
        .tier-req { font-size: 0.78rem; color: var(--text-secondary); margin-top: 2px; }
        .tier-check { font-size: 1.2rem; }
        .current-tag {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 2px;
            padding: 3px 10px; border-radius: 20px;
            background: rgba(0,0,0,0.5); border: 1px solid;
            text-transform: uppercase;
        }

        @media (max-width: 600px) {
            .profile-stats { grid-template-columns: 1fr 1fr; }
            .profile-hero { padding: 35px 20px 30px; }
            .profile-name { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo" style="text-decoration:none">Zaman Kapsülü</a>
        <ul>
            <li><a href="index.php">Kapsüllerim</a></li>
            <li><a href="galaksi.php">Galaksi</a></li>
            <li><a href="istatistikler.php">İstatistikler</a></li>
            <li><a href="profil.php" style="color:var(--accent);text-shadow:0 0 8px var(--accent-glow)">Profilim</a></li>
            <li><a href="../backend/logout.php">Çıkış Yap</a></li>
        </ul>
    </nav>

    <div class="profile-page">

        <!-- Hero Kart -->
        <div class="profile-hero">
            <img src="<?= $avatar_url ?>" alt="Avatar" class="profile-avatar">
            <h1 class="profile-name"><?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?></h1>

            <div class="rank-badge-large" style="color:<?= $rank['color'] ?>;border-color:<?= $rank['color'] ?>;background:rgba(0,0,0,0.4);text-shadow:0 0 12px <?= $rank['color'] ?>">
                <span><?= $rank['icon'] ?></span>
                <span><?= $rank['title'] ?></span>
                <span style="font-size:0.7rem;opacity:0.6;letter-spacing:3px"><?= $rank['label'] ?></span>
            </div>

            <?php if ($is_own_profile): ?>
                <p class="profile-email">🔑 <?= htmlspecialchars($user['email']) ?></p>
            <?php else: ?>
                <p class="profile-email">🌌 Zaman Yolcusu Profili</p>
            <?php endif; ?>
        </div>

        <!-- İlerleme Çubuğu -->
        <?php if ($rank['next_title']): ?>
        <div class="rank-progress-card">
            <div class="rank-progress-header">
                <div class="rank-current-small">
                    <span class="icon"><?= $rank['icon'] ?></span>
                    <div>
                        <div class="name" style="color:<?= $rank['color'] ?>"><?= $rank['title'] ?></div>
                        <div style="font-size:0.75rem;color:var(--text-secondary)"><?= $total ?> kapsül</div>
                    </div>
                </div>
                <div style="flex:1;margin:0 20px">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" id="progressFill"
                             style="width:<?= $rank['progress_pct'] ?>%;background:linear-gradient(to right,<?= $rank['color'] ?>,<?= $rank['next_color'] ?>)"></div>
                    </div>
                    <div class="progress-text">
                        <span><?= $rank['current_min'] ?> kapsül</span>
                        <span style="color:<?= $rank['next_color'] ?>;font-weight:700">%<?= $rank['progress_pct'] ?></span>
                        <span><?= $rank['next_min'] ?> kapsül</span>
                    </div>
                </div>
                <div class="rank-next-small">
                    <div style="text-align:right">
                        <div style="color:<?= $rank['next_color'] ?>;font-weight:700;font-size:0.9rem"><?= $rank['next_title'] ?></div>
                        <div style="font-size:0.75rem"><?= $rank['progress_needed'] ?> kapsül daha</div>
                    </div>
                    <span class="icon" style="font-size:1.8rem"><?= $rank['next_icon'] ?></span>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="rank-progress-card" style="text-align:center;padding:24px">
            <div style="font-size:2.5rem;margin-bottom:8px">👑</div>
            <div style="font-size:1.1rem;font-weight:700;color:#ffd700;text-shadow:0 0 15px #ffd700">Maksimum Rozete Ulaştın!</div>
            <div style="color:var(--text-secondary);font-size:0.9rem;margin-top:4px">Sen gerçek anlamda Ölümsüz bir Efsanesin.</div>
        </div>
        <?php endif; ?>

        <!-- İstatistikler -->
        <div class="profile-stats">
            <div class="stat-box">
                <div class="stat-number"><?= $total ?></div>
                <div class="stat-label">Toplam Kapsül</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" style="color:#ff6b6b;text-shadow:0 0 15px rgba(239,68,68,0.4)"><?= (int)$stats['locked_capsules'] ?></div>
                <div class="stat-label">🔒 Kilitli</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" style="color:#34d399;text-shadow:0 0 15px rgba(16,185,129,0.4)"><?= (int)$stats['unlocked_capsules'] ?></div>
                <div class="stat-label">🔓 Açıldı</div>
            </div>
        </div>

        <!-- Rozet Yol Haritası -->
        <div class="rank-roadmap">
            <div class="roadmap-title">⚔ Rozet Yol Haritası</div>
            <div class="roadmap-tiers">
                <?php foreach ($allTiers as $i => $tier): ?>
                    <?php
                        $isAchieved = $total >= $tier['min'];
                        $isCurrent  = ($rank['current_min'] === $tier['min']);
                        $cls = $isAchieved ? 'achieved' : '';
                        if ($isCurrent) $cls .= ' current';
                        $nextMin = isset($allTiers[$i+1]) ? $allTiers[$i+1]['min'] : null;
                        $req = ($i === 0) ? '0 kapsül' : $tier['min'].' kapsül';
                        if ($nextMin) $req .= ' – '.($nextMin-1).' kapsül';
                        else $req .= '+';
                    ?>
                    <div class="tier-row <?= $cls ?>">
                        <span class="tier-icon"><?= $tier['icon'] ?></span>
                        <div class="tier-info">
                            <div class="tier-name" style="color:<?= $isAchieved ? $tier['color'] : 'var(--text-secondary)' ?>"><?= $tier['title'] ?></div>
                            <div class="tier-req"><?= $req ?></div>
                        </div>
                        <?php if ($isCurrent): ?>
                            <span class="current-tag" style="color:<?= $tier['color'] ?>;border-color:<?= $tier['color'] ?>">MEVCUT</span>
                        <?php elseif ($isAchieved): ?>
                            <span class="tier-check">✓</span>
                        <?php else: ?>
                            <span style="font-size:0.75rem;color:var(--text-secondary)"><?= $tier['min'] - $total ?> kapsül</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        </div>

        <!-- ===================== ARŞİV BÖLÜMÜ ===================== -->
        <div id="arsiv" style="margin:60px auto 0;max-width:900px">
            <h2 style="border-left:4px solid #34d399;padding-left:15px;font-size:1.8rem;margin-bottom:24px;color:#fff;text-align:left">
                🔓 Açılmış Kapsüller
                <?php if (!$is_own_profile): ?>
                    <span style="font-size:0.85rem;color:var(--text-secondary);font-weight:400;margin-left:8px">— Herkese Açık</span>
                <?php endif; ?>
            </h2>

            <?php
            // Kendi profilinde: tüm açık kapsüller
            // Başkasının profilinde: sadece is_public=1 olanlar
            if ($is_own_profile) {
                $arch_stmt = $db->prepare("
                    SELECT c.*, 
                           (SELECT text_body FROM CONTENTS WHERE capsule_id = c.capsule_id AND content_type='Text' LIMIT 1) as preview_text
                    FROM CAPSULES c
                    WHERE c.sender_id = ? AND c.status = 'Opened'
                    ORDER BY c.target_date DESC
                ");
                $arch_stmt->execute([$target_user_id]);
            } else {
                $arch_stmt = $db->prepare("
                    SELECT c.*,
                           (SELECT text_body FROM CONTENTS WHERE capsule_id = c.capsule_id AND content_type='Text' LIMIT 1) as preview_text
                    FROM CAPSULES c
                    WHERE c.sender_id = ? AND c.status = 'Opened' AND c.is_public = 1
                    ORDER BY c.target_date DESC
                ");
                $arch_stmt->execute([$target_user_id]);
            }
            $archived = $arch_stmt->fetchAll();
            ?>

            <?php if (count($archived) === 0): ?>
                <div style="text-align:center;padding:50px 20px;background:rgba(52,211,153,0.03);border:1px dashed rgba(52,211,153,0.2);border-radius:16px;max-width:600px;margin:0 auto">
                    <div style="font-size:3rem;margin-bottom:12px;opacity:0.4">📭</div>
                    <p style="color:var(--text-secondary);font-size:1rem">
                        <?php echo $is_own_profile ? 'Henüz açılmış kapsülün yok.' : 'Bu kullanıcının herkese açık kapsülü yok.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;justify-content:center">
                    <?php foreach ($archived as $arc): ?>
                        <?php
                        $isPublic = isset($arc['is_public']) ? (int)$arc['is_public'] : 0;
                        $preview  = $arc['preview_text'] ? htmlspecialchars(mb_substr($arc['preview_text'], 0, 100)) : '(Mesaj yok)';
                        ?>
                        <div class="archive-card" onclick="openCapsulePopup(<?= $arc['capsule_id'] ?>)"
                             style="background:linear-gradient(145deg,rgba(14,17,36,0.9),rgba(5,5,10,0.95));border:1px solid rgba(52,211,153,0.2);border-radius:18px;padding:20px;cursor:pointer;transition:all 0.3s;position:relative;overflow:hidden;display:flex;flex-direction:column;height:200px;box-sizing:border-box"
                             onmouseover="this.style.transform='translateY(-4px)';this.style.borderColor='rgba(52,211,153,0.5)';this.style.boxShadow='0 12px 30px rgba(52,211,153,0.15)'"
                             onmouseout="this.style.transform='';this.style.borderColor='rgba(52,211,153,0.2)';this.style.boxShadow=''">
                            <!-- Gizlilik rozeti -->
                            <div style="position:absolute;top:14px;right:14px;font-size:0.65rem;font-weight:700;letter-spacing:1px;padding:3px 9px;border-radius:50px;<?php echo $isPublic ? 'background:rgba(0,242,254,0.1);color:#00f2fe;border:1px solid rgba(0,242,254,0.3)' : 'background:rgba(148,163,184,0.1);color:#94a3b8;border:1px solid rgba(148,163,184,0.2)'; ?>">
                                <?php echo $isPublic ? '🌐 AÇIK' : '🔐 GİZLİ'; ?>
                            </div>

                            <div style="font-size:0.72rem;color:#34d399;letter-spacing:1px;font-weight:700;margin-bottom:8px;text-transform:uppercase">
                                🔓 <?= date('d.m.Y', strtotime($arc['target_date'])) ?>
                            </div>
                            <h3 style="margin:0 0 8px;font-size:1.05rem;color:#fff;font-weight:700;padding-right:60px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex-shrink:0"><?= htmlspecialchars($arc['title']) ?></h3>
                            <p style="margin:0;font-size:0.85rem;color:var(--text-secondary);line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;flex:1"><?= $preview ?></p>
                            
                            <div style="margin-top:auto;font-size:0.78rem;color:rgba(52,211,153,0.6);padding-top:10px;border-top:1px solid rgba(255,255,255,0.05);flex-shrink:0">Mesajı okumak için tıkla →</div>
                        </div>

                        <!-- Gizli içerik verisi (JS için) -->
                        <div id="capsule-data-<?= $arc['capsule_id'] ?>" style="display:none" data-title="<?= htmlspecialchars($arc['title']) ?>" data-date="<?= date('d.m.Y', strtotime($arc['target_date'])) ?>" data-public="<?= $isPublic ?>"></div>

                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== POPUP MESAJ MODALİ ===== -->
    <div id="capsulePopupOverlay" onclick="closeCapsulePopup()" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,0.88);z-index:9990;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)"></div>
    <div id="capsulePopupModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) scale(0.9);width:min(700px,94vw);max-height:85vh;overflow-y:auto;background:linear-gradient(145deg,rgba(10,14,30,0.98),rgba(5,5,10,0.99));border:1px solid rgba(52,211,153,0.25);border-radius:24px;z-index:9991;padding:36px 40px;box-shadow:0 30px 80px rgba(0,0,0,0.7),0 0 0 1px rgba(52,211,153,0.08);transition:transform 0.35s cubic-bezier(0.25,1,0.5,1),opacity 0.35s;opacity:0">
        <button onclick="closeCapsulePopup()" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:var(--text-secondary);width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;transition:all 0.3s" onmouseover="this.style.background='rgba(239,68,68,0.15)';this.style.color='#f87171'" onmouseout="this.style.background='rgba(255,255,255,0.06)';this.style.color='var(--text-secondary)'">✕</button>

        <div id="popupContent" style="min-height:100px"></div>
    </div>

    <script>
    function openCapsulePopup(id) {
        const meta = document.getElementById('capsule-data-' + id);
        if (!meta) return;
        const title   = meta.dataset.title;
        const date    = meta.dataset.date;
        const isPublic = meta.dataset.public === '1';

        const overlay = document.getElementById('capsulePopupOverlay');
        const modal   = document.getElementById('capsulePopupModal');
        const content = document.getElementById('popupContent');

        // Yükle göstergesi
        content.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-secondary)">⏳ Yükleniyor...</div>';
        overlay.style.display = 'block';
        modal.style.display   = 'block';
        requestAnimationFrame(() => {
            modal.style.transform = 'translate(-50%,-50%) scale(1)';
            modal.style.opacity   = '1';
        });

        // İçeriği AJAX ile çek
        fetch('get_capsule_content.php?id=' + id)
            .then(r => r.json())
            .then(data => {
                let html = `
                    <div style="text-align:center;margin-bottom:28px">
                        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,rgba(52,211,153,0.15),rgba(0,242,254,0.15));border:2px solid rgba(52,211,153,0.4);display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 16px;box-shadow:0 0 25px rgba(52,211,153,0.2)">🔓</div>
                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:6px">
                            <h2 style="margin:0;font-size:1.4rem;color:#fff">${escHtml(title)}</h2>
                            <span style="font-size:0.65rem;font-weight:700;padding:3px 8px;border-radius:50px;${isPublic ? 'background:rgba(0,242,254,0.1);color:#00f2fe;border:1px solid rgba(0,242,254,0.3)' : 'background:rgba(148,163,184,0.1);color:#94a3b8;border:1px solid rgba(148,163,184,0.2)'}">${isPublic ? '🌐 AÇIK' : '🔐 GİZLİ'}</span>
                        </div>
                        <div style="font-size:0.82rem;color:rgba(52,211,153,0.7)">${date} tarihinde açıldı</div>
                    </div>`;

                if (data.recipients && data.recipients.length > 0) {
                    html += `<div style="margin-bottom:16px;font-size:0.85rem;color:var(--text-secondary)"><span style="color:#00f2fe;font-weight:600">Kime:</span> ${escHtml(data.recipients.join(', '))}</div>`;
                }

                html += '<div style="background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:24px">';
                if (data.contents && data.contents.length > 0) {
                    data.contents.forEach(c => {
                        if (c.type === 'Text') {
                            html += `<div style="color:var(--text-primary);line-height:1.8;font-size:0.96rem;white-space:pre-wrap">${escHtml(c.text)}</div>`;
                        } else if (c.type === 'Media' && c.url) {
                            const ext = c.ext ? c.ext.toLowerCase() : '';
                            if (['mp4','webm'].includes(ext)) {
                                html += `<div style="text-align:center;margin-top:16px"><video controls style="max-width:100%;border-radius:12px;border:1px solid rgba(0,242,254,0.3)"><source src="${escHtml(c.url)}" type="video/${ext}"></video></div>`;
                            } else {
                                html += `<div style="text-align:center;margin-top:16px"><img src="${escHtml(c.url)}" style="max-width:100%;border-radius:12px;border:1px solid rgba(0,242,254,0.3)"></div>`;
                            }
                        }
                    });
                } else {
                    html += '<p style="color:var(--text-secondary);text-align:center">(İçerik bulunamadı)</p>';
                }
                html += '</div>';

                content.innerHTML = html;
            })
            .catch(() => {
                content.innerHTML = '<p style="color:#f87171;text-align:center">İçerik yüklenirken hata oluştu.</p>';
            });
    }

    function closeCapsulePopup() {
        const modal   = document.getElementById('capsulePopupModal');
        const overlay = document.getElementById('capsulePopupOverlay');
        modal.style.transform = 'translate(-50%,-50%) scale(0.9)';
        modal.style.opacity   = '0';
        setTimeout(() => {
            modal.style.display   = 'none';
            overlay.style.display = 'none';
        }, 350);
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ESC tuşu ile kapat
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCapsulePopup(); });
    </script>
</body>
</html>

