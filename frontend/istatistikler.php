<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once '../backend/db.php';

$PER = 10; // Tablo başına satır

// Sayfa parametreleri
$p2 = max(1, intval($_GET['p2'] ?? 1));
$p4 = max(1, intval($_GET['p4'] ?? 1));
$p5 = max(1, intval($_GET['p5'] ?? 1));

// Arama parametreleri
$q2 = trim($_GET['q2'] ?? '');
$q4 = trim($_GET['q4'] ?? '');
$q5 = trim($_GET['q5'] ?? '');

/* ---- 1. Alt Sorgu: Hiç kapsül oluşturmayan kullanıcılar ---- */
$q1 = $db->query("SELECT first_name, last_name, email FROM USERS WHERE user_id NOT IN (SELECT DISTINCT sender_id FROM CAPSULES)");
$res1 = $q1->fetchAll();

/* ---- 2. Join: Kapsül-Kullanıcı-Kategori ---- */
$where2 = '';
$params2 = [];
if ($q2 !== '') {
    $where2 = "HAVING C.title LIKE :q OR U.first_name LIKE :q OR CAT.category_name LIKE :q";
    $params2[':q'] = '%' . $q2 . '%';
}

$cnt2_stmt = $db->prepare("SELECT COUNT(*) FROM (
    SELECT C.capsule_id FROM CAPSULES C
    JOIN USERS U ON C.sender_id = U.user_id
    JOIN CAPSULE_CATEGORY CC ON C.capsule_id = CC.capsule_id
    JOIN CATEGORIES CAT ON CC.category_id = CAT.category_id
    GROUP BY C.capsule_id, U.user_id, CAT.category_id
    $where2
) t");
$cnt2_stmt->execute($params2);
$total2 = (int)$cnt2_stmt->fetchColumn();
$pages2 = max(1, (int)ceil($total2 / $PER));
if ($p2 > $pages2) $p2 = $pages2;

$stmt2 = $db->prepare("SELECT U.first_name, U.last_name, C.title, CAT.category_name
    FROM CAPSULES C
    JOIN USERS U ON C.sender_id = U.user_id
    JOIN CAPSULE_CATEGORY CC ON C.capsule_id = CC.capsule_id
    JOIN CATEGORIES CAT ON CC.category_id = CAT.category_id
    $where2
    ORDER BY C.capsule_id DESC
    LIMIT :lim OFFSET :off");
foreach ($params2 as $k => $v) $stmt2->bindValue($k, $v);
$stmt2->bindValue(':lim', $PER, PDO::PARAM_INT);
$stmt2->bindValue(':off', ($p2-1)*$PER, PDO::PARAM_INT);
$stmt2->execute();
$res2 = $stmt2->fetchAll();

/* ---- 3. Group By: Kategori yoğunluğu ---- */
$q3 = $db->query("SELECT CAT.category_name, COUNT(CC.capsule_id) AS toplam_kapsul
    FROM CATEGORIES CAT
    LEFT JOIN CAPSULE_CATEGORY CC ON CAT.category_id = CC.category_id
    GROUP BY CAT.category_name ORDER BY toplam_kapsul DESC");
$res3 = $q3->fetchAll();
$chart_labels = array_column($res3, 'category_name');
$chart_data   = array_column($res3, 'toplam_kapsul');

/* ---- 4. Tarih Fonksiyonu: Kilitli kapsüller ---- */
$where4 = "WHERE status = 'Locked'";
$params4 = [];
if ($q4 !== '') {
    $where4 .= " AND title LIKE :q";
    $params4[':q'] = '%' . $q4 . '%';
}

$cnt4 = $db->prepare("SELECT COUNT(*) FROM CAPSULES $where4");
$cnt4->execute($params4);
$total4 = (int)$cnt4->fetchColumn();
$pages4 = max(1, (int)ceil($total4 / $PER));
if ($p4 > $pages4) $p4 = $pages4;

$stmt4 = $db->prepare("SELECT title, target_date, DATEDIFF(target_date, NOW()) AS kalan_gun
    FROM CAPSULES $where4
    ORDER BY kalan_gun ASC
    LIMIT :lim OFFSET :off");
foreach ($params4 as $k => $v) $stmt4->bindValue($k, $v);
$stmt4->bindValue(':lim', $PER, PDO::PARAM_INT);
$stmt4->bindValue(':off', ($p4-1)*$PER, PDO::PARAM_INT);
$stmt4->execute();
$res4 = $stmt4->fetchAll();

/* ---- 5. Karakter Fonksiyonu: Referans kodları ---- */
$where5 = '';
$params5 = [];
if ($q5 !== '') {
    $where5 = "HAVING buyuk_baslik LIKE :q OR referans_kodu LIKE :q";
    $params5[':q'] = '%' . $q5 . '%';
}

$cnt5 = $db->prepare("SELECT COUNT(*) FROM (
    SELECT UPPER(C.title) AS buyuk_baslik,
           CONCAT(SUBSTRING(U.first_name,1,1), SUBSTRING(U.last_name,1,1), '-', C.capsule_id) AS referans_kodu
    FROM CAPSULES C JOIN USERS U ON C.sender_id = U.user_id
    $where5
) t");
$cnt5->execute($params5);
$total5 = (int)$cnt5->fetchColumn();
$pages5 = max(1, (int)ceil($total5 / $PER));
if ($p5 > $pages5) $p5 = $pages5;

$stmt5 = $db->prepare("SELECT UPPER(C.title) AS buyuk_baslik,
    CONCAT(SUBSTRING(U.first_name,1,1), SUBSTRING(U.last_name,1,1), '-', C.capsule_id) AS referans_kodu
    FROM CAPSULES C JOIN USERS U ON C.sender_id = U.user_id
    $where5
    ORDER BY C.capsule_id DESC
    LIMIT :lim OFFSET :off");
foreach ($params5 as $k => $v) $stmt5->bindValue($k, $v);
$stmt5->bindValue(':lim', $PER, PDO::PARAM_INT);
$stmt5->bindValue(':off', ($p5-1)*$PER, PDO::PARAM_INT);
$stmt5->execute();
$res5 = $stmt5->fetchAll();

/* ---- Yardımcı: Sayfalama HTML ---- */
function paginator($cur, $total, $param, $others = []) {
    if ($total <= 1) return '';
    $q = array_merge($others, [$param => $cur]);
    $base = '?' . http_build_query($q);

    $html = '<div style="display:flex;gap:5px;margin-top:14px;flex-wrap:wrap;align-items:center;">';
    if ($cur > 1) {
        $q[$param] = $cur-1;
        $html .= '<a href="?'.http_build_query($q).'" style="'.pBtn(false).'">← Önceki</a>';
    }

    $start = max(1, $cur-2); $end = min($total, $cur+2);
    if ($start > 1) {
        $q[$param] = 1; $html .= '<a href="?'.http_build_query($q).'" style="'.pBtn(false).'">1</a>';
        if ($start > 2) $html .= '<span style="color:var(--text-secondary);padding:0 4px;">…</span>';
    }
    for ($i = $start; $i <= $end; $i++) {
        $q[$param] = $i;
        $html .= '<a href="?'.http_build_query($q).'" style="'.pBtn($i===$cur).'">'. $i .'</a>';
    }
    if ($end < $total) {
        if ($end < $total-1) $html .= '<span style="color:var(--text-secondary);padding:0 4px;">…</span>';
        $q[$param] = $total; $html .= '<a href="?'.http_build_query($q).'" style="'.pBtn(false).'">'.$total.'</a>';
    }
    if ($cur < $total) {
        $q[$param] = $cur+1;
        $html .= '<a href="?'.http_build_query($q).'" style="'.pBtn(false).'">Sonraki →</a>';
    }
    $html .= '<span style="color:var(--text-secondary);font-size:0.78rem;margin-left:8px;">Sayfa '.$cur.'/'.$total.'</span>';
    $html .= '</div>';
    return $html;
}

function pBtn($active) {
    $base = 'padding:6px 12px;border-radius:6px;text-decoration:none;font-size:0.8rem;border:1px solid;transition:all 0.2s;';
    return $active
        ? $base . 'background:rgba(0,242,254,0.15);border-color:rgba(0,242,254,0.5);color:#00f2fe;font-weight:700;'
        : $base . 'background:rgba(255,255,255,0.03);border-color:rgba(255,255,255,0.08);color:var(--text-secondary);';
}

function searchBar($name, $val, $others = []) {
    $hidden = '';
    foreach($others as $k => $v) $hidden .= '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'">';
    $clear = $val !== '' ? '<a href="?'.http_build_query($others).'" style="padding:7px 10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:6px;margin-left:5px;color:#f87171;font-size:0.82rem;text-decoration:none;">✕</a>' : '';
    return '<form method="GET" style="display:flex;gap:0;margin-bottom:12px;">'
        . $hidden
        . '<input type="text" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($val).'" placeholder="🔍 Ara..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-right:none;border-radius:6px 0 0 6px;padding:7px 12px;color:#fff;font-size:0.85rem;outline:none;">'
        . '<button type="submit" style="background:rgba(0,242,254,0.1);border:1px solid rgba(0,242,254,0.25);border-radius:0 6px 6px 0;padding:7px 14px;color:#00f2fe;cursor:pointer;font-size:0.82rem;">Ara</button>'
        . $clear
        . '</form>';
}

// Diğer sayfa param'larını koru
function otherParams($exclude) {
    $allowed = ['p2','p4','p5','q2','q4','q5'];
    $res = [];
    foreach ($allowed as $k) {
        if ($k !== $exclude && isset($_GET[$k]) && $_GET[$k] !== '') {
            $res[$k] = $_GET[$k];
        }
    }
    return $res;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sistem Analitikleri - Zaman Kapsülü</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .table-container {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
            box-shadow: 0 0 15px rgba(34, 211, 238, 0.05);
        }
        table { width: 100%; border-collapse: collapse; color: var(--text-primary); }
        th, td { padding: 11px 12px; text-align: left; border-bottom: 1px solid var(--border); font-size:0.9rem; }
        th { color: var(--accent); border-bottom: 2px solid var(--accent); font-size:0.82rem; letter-spacing:1px; text-transform:uppercase; }
        tr:hover td { background: rgba(0,242,254,0.03); }
        .query-title { color: var(--accent); margin-top: 0; border-left: 4px solid var(--accent); padding-left: 10px; }
        .sql-code { background: #020617; color: #10b981; padding: 10px; border-radius: 5px; font-family: monospace; margin-bottom: 15px; font-size: 0.88rem; border: 1px solid #334155; white-space:pre-wrap; word-break:break-all; }
        .stat-row { display:flex; gap:10px; align-items:center; margin-bottom:10px; flex-wrap:wrap; }
        .stat-badge { background:rgba(0,242,254,0.08); border:1px solid rgba(0,242,254,0.2); color:#00f2fe; padding:3px 10px; border-radius:50px; font-size:0.78rem; white-space:nowrap; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo" style="text-decoration: none;">Zaman Kapsülü</a>
        <ul>
            <li><a href="index.php">Kapsüllerim</a></li>
            <li><a href="galaksi.php">Galaksi</a></li>
            <li><a href="istatistikler.php" style="color: var(--accent); text-shadow: 0 0 8px var(--accent-glow);">Sistem İstatistikleri</a></li>
            <li><a href="profil.php">Profilim</a></li>
            <li><a href="../backend/logout.php">Çıkış Yap</a></li>
        </ul>
    </nav>

    <div class="capsule-container">
        <h2>Sistem Analitikleri &amp; Veri Akışı</h2>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">
            Platform genelindeki zaman kapsüllerinin anlık istatistikleri, pasif kullanıcı verileri ve kategorik yoğunluk metrikleri aşağıda listelenmektedir.
        </p>

        <!-- ===== 1. ALT SORGU ===== -->
        <div class="table-container">
            <h3 class="query-title">Gözlemci Kayıtları (Henüz Kapsül Oluşturmayan Kullanıcılar)</h3>
            <details style="margin-bottom:15px;cursor:pointer;">
                <summary style="color:var(--text-secondary);font-size:0.85rem;outline:none;user-select:none;">[+] Sistem Logları / Çekirdek Sorguyu İncele</summary>
                <div class="sql-code" style="margin-top:10px;">SELECT first_name, last_name, email FROM USERS
WHERE user_id NOT IN (SELECT DISTINCT sender_id FROM CAPSULES);</div>
            </details>
            <div class="stat-row">
                <span class="stat-badge"><?= count($res1) ?> kullanıcı</span>
            </div>
            <table>
                <tr><th>Ad</th><th>Soyad</th><th>E-Posta</th></tr>
                <?php foreach($res1 as $row): ?>
                <tr><td><?= htmlspecialchars($row['first_name']) ?></td><td><?= htmlspecialchars($row['last_name']) ?></td><td><?= htmlspecialchars($row['email']) ?></td></tr>
                <?php endforeach; ?>
                <?php if(count($res1)==0) echo "<tr><td colspan='3' style='color:var(--text-secondary);text-align:center;'>Herkes kapsül oluşturmuş.</td></tr>"; ?>
            </table>
        </div>

        <!-- ===== 2. JOIN (sayfalı + arama) ===== -->
        <div class="table-container">
            <h3 class="query-title">Kapsül Ağı ve Kategori Matrisi</h3>
            <details style="margin-bottom:15px;cursor:pointer;">
                <summary style="color:var(--text-secondary);font-size:0.85rem;outline:none;user-select:none;">[+] Sistem Logları / Çekirdek Sorguyu İncele</summary>
                <div class="sql-code" style="margin-top:10px;">SELECT U.first_name, U.last_name, C.title, CAT.category_name
FROM CAPSULES C
JOIN USERS U ON C.sender_id = U.user_id
JOIN CAPSULE_CATEGORY CC ON C.capsule_id = CC.capsule_id
JOIN CATEGORIES CAT ON CC.category_id = CAT.category_id;</div>
            </details>
            <div class="stat-row">
                <span class="stat-badge"><?= $total2 ?> kayıt</span>
                <span class="stat-badge">Sayfa <?= $p2 ?>/<?= $pages2 ?></span>
            </div>
            <?= searchBar('q2', $q2, otherParams('q2')) ?>
            <table>
                <tr><th>Kullanıcı Adı</th><th>Soyad</th><th>Kapsül Başlığı</th><th>Kategori</th></tr>
                <?php foreach($res2 as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($res2)==0) echo "<tr><td colspan='4' style='color:var(--text-secondary);text-align:center;'>Veri bulunamadı.</td></tr>"; ?>
            </table>
            <?= paginator($p2, $pages2, 'p2', otherParams('p2')) ?>
        </div>

        <!-- ===== 3. GROUP BY ===== -->
        <div class="table-container">
            <h3 class="query-title">Kategori Yoğunluk Analizi</h3>
            <details style="margin-bottom:15px;cursor:pointer;">
                <summary style="color:var(--text-secondary);font-size:0.85rem;outline:none;user-select:none;">[+] Sistem Logları / Çekirdek Sorguyu İncele</summary>
                <div class="sql-code" style="margin-top:10px;">SELECT CAT.category_name, COUNT(CC.capsule_id) AS toplam_kapsul
FROM CATEGORIES CAT
LEFT JOIN CAPSULE_CATEGORY CC ON CAT.category_id = CC.category_id
GROUP BY CAT.category_name ORDER BY toplam_kapsul DESC;</div>
            </details>
            <div style="display:flex;flex-wrap:wrap;gap:30px;align-items:flex-start;">
                <div style="flex:1;min-width:280px;">
                    <table>
                        <tr><th>Kategori Adı</th><th>Toplam Kapsül</th></tr>
                        <?php foreach($res3 as $row): ?>
                        <tr><td><?= htmlspecialchars($row['category_name']) ?></td><td><?= htmlspecialchars($row['toplam_kapsul']) ?></td></tr>
                        <?php endforeach; ?>
                        <?php if(count($res3)==0) echo "<tr><td colspan='2' style='color:var(--text-secondary);text-align:center;'>Veri bulunamadı.</td></tr>"; ?>
                    </table>
                </div>
                <div style="flex:1;min-width:280px;max-width:380px;margin:0 auto;background:rgba(0,0,0,0.3);padding:20px;border-radius:15px;border:1px solid rgba(255,255,255,0.05);">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            <script>
                const ctx = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode($chart_labels) ?>,
                        datasets: [{ data: <?= json_encode($chart_data) ?>, backgroundColor: ['#00f2fe','#b300ff','#34d399','#f59e0b','#ef4444','#a78bfa','#fb923c'], borderColor: '#05050a', borderWidth: 2, hoverOffset: 10 }]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#a0aec0', font: { family: 'Space Grotesk', size: 13 } } } }, cutout: '70%' }
                });
            </script>
        </div>

        <!-- ===== 4. TARİH FONKSİYONU (sayfalı + arama) ===== -->
        <div class="table-container">
            <h3 class="query-title">Zaman Çizelgesi İzleyicisi (Yaklaşan Kilitli Kapsüller)</h3>
            <details style="margin-bottom:15px;cursor:pointer;">
                <summary style="color:var(--text-secondary);font-size:0.85rem;outline:none;user-select:none;">[+] Sistem Logları / Çekirdek Sorguyu İncele</summary>
                <div class="sql-code" style="margin-top:10px;">SELECT title, target_date, DATEDIFF(target_date, NOW()) AS kalan_gun
FROM CAPSULES WHERE status = 'Locked'
ORDER BY kalan_gun ASC;</div>
            </details>
            <div class="stat-row">
                <span class="stat-badge"><?= $total4 ?> kilitli kapsül</span>
                <span class="stat-badge">Sayfa <?= $p4 ?>/<?= $pages4 ?></span>
            </div>
            <?= searchBar('q4', $q4, otherParams('q4')) ?>
            <table>
                <tr><th>Kapsül Başlığı</th><th>Hedef Tarih</th><th>Kalan Gün</th></tr>
                <?php foreach($res4 as $row): 
                    $gun = (int)$row['kalan_gun'];
                    $color = $gun <= 7 ? '#f59e0b' : ($gun <= 30 ? '#34d399' : 'inherit');
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['target_date']) ?></td>
                    <td style="color:<?= $color ?>;font-weight:<?= $gun<=30?'600':'400' ?>;"><?= $gun ?> gün</td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($res4)==0) echo "<tr><td colspan='3' style='color:var(--text-secondary);text-align:center;'>Şu an kilitli kapsül yok.</td></tr>"; ?>
            </table>
            <?= paginator($p4, $pages4, 'p4', otherParams('p4')) ?>
        </div>

        <!-- ===== 5. KARAKTER FONKSİYONU (sayfalı + arama) ===== -->
        <div class="table-container">
            <h3 class="query-title">Şifrelenmiş Referans Kayıtları Sistemi</h3>
            <details style="margin-bottom:15px;cursor:pointer;">
                <summary style="color:var(--text-secondary);font-size:0.85rem;outline:none;user-select:none;">[+] Sistem Logları / Çekirdek Sorguyu İncele</summary>
                <div class="sql-code" style="margin-top:10px;">SELECT UPPER(C.title) AS buyuk_baslik,
       CONCAT(SUBSTRING(U.first_name,1,1), SUBSTRING(U.last_name,1,1), '-', C.capsule_id) AS referans_kodu
FROM CAPSULES C JOIN USERS U ON C.sender_id = U.user_id;</div>
            </details>
            <div class="stat-row">
                <span class="stat-badge"><?= $total5 ?> kayıt</span>
                <span class="stat-badge">Sayfa <?= $p5 ?>/<?= $pages5 ?></span>
            </div>
            <?= searchBar('q5', $q5, otherParams('q5')) ?>
            <table>
                <tr><th>Sistem Başlığı</th><th>Üretilen Referans Kodu</th></tr>
                <?php foreach($res5 as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['buyuk_baslik']) ?></td>
                    <td style="font-family:monospace;color:#34d399;"><?= htmlspecialchars($row['referans_kodu']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($res5)==0) echo "<tr><td colspan='2' style='color:var(--text-secondary);text-align:center;'>Veri bulunamadı.</td></tr>"; ?>
            </table>
            <?= paginator($p5, $pages5, 'p5', otherParams('p5')) ?>
        </div>

    </div>
</body>
</html>
