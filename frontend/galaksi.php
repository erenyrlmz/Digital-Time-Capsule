<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require_once '../backend/db.php';
require_once '../backend/rank_helper.php';

// Sayfalama
$per_page    = 12;
$page        = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($page - 1) * $per_page;
$search      = trim($_GET['q'] ?? '');

// Toplam kullanıcı sayısı
$count_sql  = "SELECT COUNT(*) FROM USERS";
$count_params = [];
if ($search !== '') {
    $count_sql .= " WHERE CONCAT(first_name,' ',last_name) LIKE ?";
    $count_params[] = "%$search%";
}
$total_users = (int)$db->prepare($count_sql)->execute($count_params) ? $db->query(
    $count_sql . ($search ? " -- already prepared" : "")
) : 0;

// Doğru COUNT sorgusu
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute($count_params);
$total_users = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_users / $per_page));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per_page;

// Top 3 (yalnızca sayfa 1'de gösterilir)
$top3_stmt = $db->query("SELECT U.user_id, U.first_name, U.last_name,
    COUNT(C.capsule_id) as total_capsules
    FROM USERS U LEFT JOIN CAPSULES C ON U.user_id = C.sender_id
    GROUP BY U.user_id ORDER BY total_capsules DESC LIMIT 3");
$top3 = $top3_stmt->fetchAll();

// Arama + sayfalama sorgusu
$search_cond = $search !== '' ? "WHERE CONCAT(U.first_name,' ',U.last_name) LIKE ?" : "";
$main_sql = "SELECT U.user_id, U.first_name, U.last_name,
    COUNT(C.capsule_id) as total_capsules
    FROM USERS U LEFT JOIN CAPSULES C ON U.user_id = C.sender_id
    $search_cond
    GROUP BY U.user_id
    ORDER BY total_capsules DESC, U.user_id ASC
    LIMIT $per_page OFFSET $offset";
$main_stmt = $db->prepare($main_sql);
$search !== '' ? $main_stmt->execute(["%$search%"]) : $main_stmt->execute();
$users = $main_stmt->fetchAll();

// Global sırayı bulmak için her kullanıcının rank numarası
$rank_map_stmt = $db->query("SELECT U.user_id, ROW_NUMBER() OVER (ORDER BY COUNT(C.capsule_id) DESC, U.user_id ASC) as rn
    FROM USERS U LEFT JOIN CAPSULES C ON U.user_id = C.sender_id GROUP BY U.user_id");
$rank_map = [];
foreach ($rank_map_stmt->fetchAll() as $r) $rank_map[$r['user_id']] = (int)$r['rn'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Galaktik Liderlik — Zaman Kapsülü</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .galaxy-page { padding: 50px 8%; }

        /* --- HEADER --- */
        .galaxy-header { text-align:center; margin-bottom: 50px; }
        .galaxy-header h1 {
            font-size: 2.8rem; font-weight: 800; margin-bottom: 10px;
            background: linear-gradient(to right, #00f2fe, #b300ff);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .galaxy-header p { color: var(--text-secondary); font-size: 1.1rem; }

        /* --- ARAMA --- */
        .search-bar {
            display: flex; justify-content: center; margin-bottom: 40px;
        }
        .search-bar form {
            display: flex; gap: 10px; width: 100%; max-width: 440px;
        }
        .search-bar input {
            flex: 1; padding: 12px 18px;
            background: rgba(0,0,0,0.4) !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
            color: white !important; border-radius: 50px;
            font-family: inherit; font-size: 0.95rem;
            margin-bottom: 0;
        }
        .search-bar input:focus { border-color: var(--accent) !important; outline: none; }
        .search-bar button {
            padding: 12px 22px; background: linear-gradient(135deg,var(--accent),var(--accent-secondary));
            color: #000; border: none; border-radius: 50px;
            font-weight: 700; cursor: pointer; font-size: 0.9rem;
            white-space: nowrap;
        }

        /* --- PODYUM (TOP 3) --- */
        .podium-section {
            display: flex; justify-content: center; align-items: flex-end;
            gap: 20px; margin-bottom: 60px; flex-wrap: wrap;
        }
        .podium-card {
            text-align: center; position: relative;
            display: flex; flex-direction: column; align-items: center;
        }
        .podium-card a { text-decoration: none; color: inherit; }
        .podium-avatar-wrap { position: relative; display: inline-block; margin-bottom: 14px; }
        .podium-avatar {
            border-radius: 50%; display: block;
            border: 3px solid var(--accent);
            transition: transform 0.3s;
        }
        .podium-avatar:hover { transform: scale(1.07); }
        .podium-medal {
            position: absolute; bottom: -4px; right: -4px;
            font-size: 1.4rem; line-height: 1;
        }
        .podium-name { font-size: 1.05rem; font-weight: 700; color: white; margin-bottom: 4px; }
        .podium-badge {
            font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;
            padding: 3px 12px; border-radius: 20px; border: 1px solid; margin-bottom: 10px;
            display: inline-block;
        }
        .podium-count { font-size: 0.85rem; color: var(--text-secondary); }
        .podium-base {
            width: 100%; border-radius: 14px 14px 0 0;
            margin-top: 12px; display: flex; align-items: center; justify-content: center;
            font-family: 'Courier New', monospace; font-size: 2rem; font-weight: 900;
        }
        .podium-1 { width: 180px; }
        .podium-1 .podium-avatar { width: 110px; height: 110px; box-shadow: 0 0 30px rgba(255,215,0,0.5); border-color: #ffd700; }
        .podium-1 .podium-base { height: 90px; background: linear-gradient(180deg,#ffd700,#b8860b); color:#000; }

        .podium-2 { width: 155px; }
        .podium-2 .podium-avatar { width: 90px; height: 90px; border-color: #c0c0c0; box-shadow: 0 0 20px rgba(192,192,192,0.4); }
        .podium-2 .podium-base { height: 65px; background: linear-gradient(180deg,#c0c0c0,#808080); color:#000; }

        .podium-3 { width: 140px; }
        .podium-3 .podium-avatar { width: 80px; height: 80px; border-color: #cd7f32; box-shadow: 0 0 15px rgba(205,127,50,0.4); }
        .podium-3 .podium-base { height: 50px; background: linear-gradient(180deg,#cd7f32,#8b4513); color:#fff; }

        /* --- KULLANICI GRID --- */
        .users-section-title {
            font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);
            letter-spacing: 3px; text-transform: uppercase; margin-bottom: 20px;
        }
        .travelers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .traveler-card {
            background: var(--bg-card); backdrop-filter: var(--glass-blur);
            border: 1px solid var(--border); border-radius: 20px;
            padding: 24px; text-align: center;
            transition: all 0.3s ease; text-decoration: none; display: block;
            position: relative; overflow: hidden;
        }
        .traveler-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4), 0 0 20px rgba(179,0,255,0.15);
            border-color: rgba(179,0,255,0.3);
        }
        .traveler-rank-num {
            position: absolute; top: 12px; left: 14px;
            font-size: 0.75rem; font-weight: 900;
            font-family: 'Courier New', monospace;
            color: var(--text-secondary); opacity: 0.5;
        }
        .traveler-avatar {
            width: 70px; height: 70px; border-radius: 50%;
            border: 2px solid var(--accent); margin-bottom: 12px;
        }
        .traveler-name { font-size: 1.1rem; font-weight: 700; color: white; margin-bottom: 6px; }
        .traveler-badge {
            font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;
            padding: 3px 12px; border-radius: 20px; border: 1px solid;
            display: inline-block; margin-bottom: 12px;
        }
        .traveler-stat { color: var(--text-secondary); font-size: 0.85rem; }
        .traveler-stat strong { color: var(--accent); font-size: 1.1rem; }

        /* --- SAYFALAMA --- */
        .pagination {
            display: flex; justify-content: center; align-items: center;
            gap: 8px; flex-wrap: wrap;
        }
        .page-btn {
            padding: 9px 16px; border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-secondary); text-decoration: none;
            font-size: 0.9rem; transition: all 0.2s;
            font-family: inherit; cursor: pointer;
        }
        .page-btn:hover { background: rgba(0,242,254,0.1); color: var(--accent); border-color: var(--accent); }
        .page-btn.active { background: var(--accent); color: #000; font-weight: 700; border-color: var(--accent); }
        .page-btn.disabled { opacity: 0.3; pointer-events: none; }

        .empty-state {
            text-align: center; padding: 60px 20px;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo" style="text-decoration:none">Zaman Kapsülü</a>
        <ul>
            <li><a href="index.php">Kapsüllerim</a></li>
            <li><a href="galaksi.php" style="color:var(--accent);text-shadow:0 0 8px var(--accent-glow)">Galaksi</a></li>
            <li><a href="istatistikler.php">İstatistikler</a></li>
            <li><a href="profil.php">Profilim</a></li>
            <li><a href="../backend/logout.php">Çıkış Yap</a></li>
        </ul>
    </nav>

    <main class="galaxy-page">
        <div class="galaxy-header">
            <h1>🌌 Galaktik Liderlik Tablosu</h1>
            <p>Zaman boşluğuna en çok iz bırakan yolcular</p>
        </div>

        <!-- Arama -->
        <div class="search-bar">
            <form method="GET" action="galaksi.php">
                <input type="text" name="q" placeholder="Kullanıcı ara..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">🔍 Ara</button>
                <?php if ($search): ?>
                    <a href="galaksi.php" style="padding:12px 18px;color:var(--text-secondary);text-decoration:none;display:flex;align-items:center">✕</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Podyum — yalnızca ilk sayfada ve arama yoksa -->
        <?php if ($page === 1 && $search === '' && count($top3) > 0): ?>
        <div class="podium-section">
            <?php
            // Sıra: 2. — 1. — 3. (podyum görünümü için)
            $podium_order = [1 => null, 0 => null, 2 => null];
            foreach ([1,0,2] as $idx):
                if (!isset($top3[$idx])) continue;
                $u = $top3[$idx];
                $pos = $idx + 1;
                $rank_info = getRankInfo((int)$u['total_capsules']);
                $avatar = "https://ui-avatars.com/api/?name=".urlencode($u['first_name'].' '.$u['last_name'])."&background=00f2fe&color=05050a&size=120&bold=true";
                $medals = [1=>'🥇',2=>'🥈',3=>'🥉'];
                $cls = "podium-$pos";
            ?>
            <div class="podium-card <?= $cls ?>">
                <a href="profil.php?id=<?= $u['user_id'] ?>">
                    <div class="podium-avatar-wrap">
                        <img src="<?= $avatar ?>" class="podium-avatar" alt="avatar">
                        <span class="podium-medal"><?= $medals[$pos] ?></span>
                    </div>
                    <div class="podium-name"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></div>
                    <div class="podium-badge" style="color:<?= $rank_info['color'] ?>;border-color:<?= $rank_info['color'] ?>">
                        <?= $rank_info['icon'] ?> <?= $rank_info['title'] ?>
                    </div>
                    <div class="podium-count"><?= (int)$u['total_capsules'] ?> kapsül</div>
                    <div class="podium-base">#<?= $pos ?></div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Kullanıcı Listesi -->
        <?php if ($search): ?>
            <div class="users-section-title">🔍 "<?= htmlspecialchars($search) ?>" için <?= $total_users ?> sonuç</div>
        <?php else: ?>
            <div class="users-section-title">Tüm Zaman Yolcuları — Sayfa <?= $page ?> / <?= $total_pages ?></div>
        <?php endif; ?>

        <?php if (count($users) === 0): ?>
            <div class="empty-state">
                <div style="font-size:3rem;margin-bottom:12px">🌌</div>
                <div>Aramanızla eşleşen yolcu bulunamadı.</div>
            </div>
        <?php else: ?>
        <div class="travelers-grid">
            <?php foreach ($users as $u):
                $t = (int)$u['total_capsules'];
                $ri = getRankInfo($t);
                $global_rank = $rank_map[$u['user_id']] ?? '?';
                $avatar = "https://ui-avatars.com/api/?name=".urlencode($u['first_name'].' '.$u['last_name'])."&background=00f2fe&color=05050a&size=100&bold=true";
            ?>
            <a href="profil.php?id=<?= $u['user_id'] ?>" class="traveler-card"
               style="<?= $global_rank <= 3 && $search === '' ? "border-color:rgba(255,215,0,0.2);box-shadow:0 0 20px rgba(255,215,0,0.05)" : "" ?>">
                <div class="traveler-rank-num">#<?= $global_rank ?></div>
                <img src="<?= $avatar ?>" class="traveler-avatar" alt="avatar"
                     style="border-color:<?= $ri['color'] ?>">
                <div class="traveler-name"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></div>
                <div class="traveler-badge" style="color:<?= $ri['color'] ?>;border-color:<?= $ri['color'] ?>">
                    <?= $ri['icon'] ?> <?= $ri['title'] ?>
                </div>
                <div class="traveler-stat"><strong><?= $t ?></strong> kapsül</div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Sayfalama -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <a href="?page=<?= $page-1 ?><?= $search ? "&q=".urlencode($search) : "" ?>"
               class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>">← Önceki</a>

            <?php
            $start = max(1, $page - 2);
            $end   = min($total_pages, $page + 2);
            if ($start > 1) echo '<span style="color:var(--text-secondary);padding:0 4px">...</span>';
            for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?= $i ?><?= $search ? "&q=".urlencode($search) : "" ?>"
                   class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor;
            if ($end < $total_pages) echo '<span style="color:var(--text-secondary);padding:0 4px">...</span>';
            ?>

            <a href="?page=<?= $page+1 ?><?= $search ? "&q=".urlencode($search) : "" ?>"
               class="page-btn <?= $page >= $total_pages ? 'disabled' : '' ?>">Sonraki →</a>

            <span style="color:var(--text-secondary);font-size:0.8rem;margin-left:8px">
                <?= $total_users ?> yolcu
            </span>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </main>
</body>
</html>
