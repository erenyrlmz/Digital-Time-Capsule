<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include '../backend/db.php';

// Küresel İstatistikler
$stat1 = $db->query("SELECT COUNT(*) FROM CAPSULES")->fetchColumn();
$stat2 = $db->query("SELECT COUNT(*) FROM USERS")->fetchColumn();
$stat3 = $db->query("SELECT COUNT(*) FROM CONTENTS WHERE content_type = 'Media'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dijital Zaman Kapsülü</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>
    
    <nav>
        <a href="index.php" class="logo" style="text-decoration: none;">Zaman Kapsülü</a>
        <ul>
            <li><a href="index.php">Kapsüllerim</a></li>
            <li><a href="#" class="open-modal-btn">Yeni Kapsül Oluştur</a></li>
            <li><a href="galaksi.php">Galaksi</a></li>
            <li><a href="istatistikler.php">İstatistikler</a></li>
            <li><a href="profil.php">Profilim</a></li>
            <li><a href="../backend/logout.php">Çıkış Yap</a></li>
        </ul>
    </nav>

    <main style="position: relative; z-index: 5;">
        <section class="hero" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%2322d3ee\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            <h1>Geleceğe Bir Mesaj Bırak</h1>
            <p>Anılarını, mesajlarını ve medyalarını kilitle; zamanı gelince açılsınlar.</p>
            <button class="btn-primary open-modal-btn">Kapsül Oluştur</button>
        </section>

        <div class="system-stats" style="display: flex; justify-content: center; gap: 60px; padding: 25px 20px; background: rgba(34, 211, 238, 0.03); border-top: 1px solid rgba(34, 211, 238, 0.2); border-bottom: 1px solid rgba(34, 211, 238, 0.2); margin-bottom: 40px; flex-wrap: wrap;">
            <div style="text-align: center;">
                <h3 style="color: var(--accent); font-size: 2.5rem; margin: 0; text-shadow: 0 0 10px var(--accent-glow);"><?= $stat1 ?></h3>
                <span style="color: var(--text-secondary); font-size: 0.8rem; letter-spacing: 2px;">KİLİTLİ ANILAR</span>
            </div>
            <div style="text-align: center;">
                <h3 style="color: var(--accent); font-size: 2.5rem; margin: 0; text-shadow: 0 0 10px var(--accent-glow);"><?= $stat2 ?></h3>
                <span style="color: var(--text-secondary); font-size: 0.8rem; letter-spacing: 2px;">ZAMAN YOLCULARI</span>
            </div>
            <div style="text-align: center;">
                <h3 style="color: var(--accent); font-size: 2.5rem; margin: 0; text-shadow: 0 0 10px var(--accent-glow);"><?= $stat3 ?></h3>
                <span style="color: var(--text-secondary); font-size: 0.8rem; letter-spacing: 2px;">MEDYA DOSYASI</span>
            </div>
        </div>

        <section style="padding: 0 10%; margin-bottom: 60px; text-align: center;">
            <h2 style="margin-bottom: 50px; font-size: 2.2rem; font-weight: 700; color: #fff;">
                <span style="background: linear-gradient(to right, var(--accent), var(--accent-purple)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent;">Zaman Boşluğuna</span> Nasıl Veri Gönderilir?
            </h2>
            <div style="display: flex; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px; padding: 30px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05); background: rgba(15,23,42,0.4); box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📦</div>
                    <h3 style="color: var(--text-primary); font-size: 1.2rem;">1. Kapsülünü Hazırla</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Metin, fotoğraf veya videolarını ekle. Mesajının kime ulaşacağına karar ver.</p>
                </div>
                <div style="flex: 1; min-width: 250px; padding: 30px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05); background: rgba(15,23,42,0.4); box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🔒</div>
                    <h3 style="color: var(--text-primary); font-size: 1.2rem;">2. Mühürle ve Kilitle</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Açılmasını istediğin tarihi seç ve fırlat. O tarihe kadar kimse içeriğe ulaşamaz.</p>
                </div>
                <div style="flex: 1; min-width: 250px; padding: 30px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05); background: rgba(15,23,42,0.4); box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🔓</div>
                    <h3 style="color: var(--text-primary); font-size: 1.2rem;">3. Gelecekte Keşfet</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Tarih geldiğinde kilit açılır. Geçmişteki kendinden veya sevdiklerinden gelen mesajı oku.</p>
                </div>
            </div>
        </section>

        <section class="capsule-container">
            <!-- ======= BAŞLIK + KONTROLLER ======= -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
                <h2 style="border-left:4px solid var(--accent);padding-left:15px;font-size:2rem;margin:0">Kapsüllerim</h2>
                <a href="profil.php#arsiv" style="color:var(--text-secondary);text-decoration:none;font-size:0.9rem;display:flex;align-items:center;gap:6px;border:1px solid rgba(255,255,255,0.1);padding:8px 16px;border-radius:50px;transition:all 0.3s" onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.color='var(--text-secondary)'">
                    🔓 Açılmış Arşiv →
                </a>
            </div>

            <!-- ======= FİLTRE ÇUBUĞU ======= -->
            <?php
                $per_page     = 9;
                $cur_page     = max(1, intval($_GET['page'] ?? 1));
                $filter_stat  = $_GET['filter'] ?? 'all';
                $search_q     = trim(htmlspecialchars($_GET['q'] ?? ''));
                $uid          = $_SESSION['user_id'];

                // Tarihi gelen kapsüllerin durumunu Unlocked yap
                $update = $db->prepare("UPDATE CAPSULES SET status='Unlocked' WHERE sender_id=:uid AND target_date <= CURRENT_DATE AND status='Locked'");
                $update->execute(['uid' => $uid]);

                // WHERE oluştur
                $where  = "sender_id = :uid AND status != 'Opened'";
                $params = ['uid' => $uid];
                if ($filter_stat === 'locked')  { $where .= " AND status='Locked'"; }
                if ($filter_stat === 'ready')   { $where .= " AND status='Unlocked'"; }
                if ($search_q !== '') {
                    $where .= " AND title LIKE :q";
                    $params['q'] = '%' . $search_q . '%';
                }

                // Toplam kayıt sayısı
                $cnt_stmt = $db->prepare("SELECT COUNT(*) FROM CAPSULES WHERE $where");
                $cnt_stmt->execute($params);
                $total_rows  = (int)$cnt_stmt->fetchColumn();
                $total_pages = max(1, (int)ceil($total_rows / $per_page));
                if ($cur_page > $total_pages) $cur_page = $total_pages;
                $offset = ($cur_page - 1) * $per_page;

                // Sayfalı sorgu
                $sorgu = $db->prepare("SELECT * FROM CAPSULES WHERE $where ORDER BY FIELD(status,'Unlocked','Locked'), target_date ASC LIMIT :lim OFFSET :off");
                foreach ($params as $k => $v) $sorgu->bindValue(":$k", $v);
                $sorgu->bindValue(':lim', $per_page, PDO::PARAM_INT);
                $sorgu->bindValue(':off', $offset, PDO::PARAM_INT);
                $sorgu->execute();
                $kapsuller = $sorgu->fetchAll();

                // URL oluşturucu
                function buildUrl($page, $filter, $q) {
                    $p = ['page' => $page];
                    if ($filter !== 'all') $p['filter'] = $filter;
                    if ($q !== '') $p['q'] = $q;
                    return 'index.php#capsule-section' . (count($p) ? '?' . http_build_query($p) : '');
                }
            ?>

            <div style="background:rgba(14,17,36,0.7);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:16px 20px;margin-bottom:24px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;">

                <!-- Arama Kutusu -->
                <form method="GET" action="index.php" style="flex:1;min-width:200px;display:flex;gap:0;">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter_stat) ?>">
                    <input type="text" name="q" value="<?= htmlspecialchars($search_q) ?>"
                        placeholder="🔍  Kapsül ara..."
                        style="flex:1;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-right:none;border-radius:8px 0 0 8px;padding:9px 14px;color:#fff;font-size:0.88rem;outline:none;transition:border 0.2s;"
                        onfocus="this.style.borderColor='rgba(0,242,254,0.4)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                    <button type="submit" style="background:rgba(0,242,254,0.1);border:1px solid rgba(0,242,254,0.25);border-radius:0 8px 8px 0;padding:9px 16px;color:#00f2fe;cursor:pointer;font-size:0.9rem;transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(0,242,254,0.2)'"
                        onmouseout="this.style.background='rgba(0,242,254,0.1)'">Ara</button>
                    <?php if($search_q !== ''): ?>
                        <a href="index.php?filter=<?= urlencode($filter_stat) ?>" style="padding:9px 12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:8px;margin-left:6px;color:#f87171;font-size:0.85rem;text-decoration:none;white-space:nowrap;"
                            onmouseover="this.style.background='rgba(239,68,68,0.2)'"
                            onmouseout="this.style.background='rgba(239,68,68,0.1)'">✕ Temizle</a>
                    <?php endif; ?>
                </form>

                <!-- Durum Filtreleri -->
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <?php
                    $filters = [
                        'all'    => ['label' => 'Tümü',            'icon' => '🗂'],
                        'locked' => ['label' => 'Kilitli',         'icon' => '🔒'],
                        'ready'  => ['label' => 'Açılmaya Hazır',  'icon' => '✨'],
                    ];
                    foreach ($filters as $key => $f):
                        $isActive = ($filter_stat === $key);
                        $href = 'index.php' . ($key !== 'all' ? '?filter='.$key : '') . ($search_q ? ($key !== 'all' ? '&' : '?') . 'q='.urlencode($search_q) : '');
                    ?>
                    <a href="<?= $href ?>"
                        style="padding:8px 14px;border-radius:8px;font-size:0.82rem;font-weight:600;letter-spacing:0.5px;text-decoration:none;transition:all 0.2s;border:1px solid;
                        <?= $isActive
                            ? 'background:rgba(0,242,254,0.15);border-color:rgba(0,242,254,0.5);color:#00f2fe;box-shadow:0 0 12px rgba(0,242,254,0.15);'
                            : 'background:rgba(255,255,255,0.03);border-color:rgba(255,255,255,0.07);color:var(--text-secondary);' ?>"
                        onmouseover="this.style.borderColor='rgba(0,242,254,0.3)';this.style.color='#00f2fe';"
                        onmouseout="this.style.borderColor='<?= $isActive ? 'rgba(0,242,254,0.5)' : 'rgba(255,255,255,0.07)' ?>';this.style.color='<?= $isActive ? '#00f2fe' : 'var(--text-secondary)' ?>';">
                        <?= $f['icon'] ?> <?= $f['label'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Sayfa Bilgisi -->
                <div style="font-size:0.8rem;color:var(--text-secondary);margin-left:auto;white-space:nowrap;">
                    <?= $total_rows ?> kapsül &bull; Sayfa <?= $cur_page ?>/<?= $total_pages ?>
                </div>
            </div>

            <div class="capsule-grid" id="capsuleList">

                <?php foreach($kapsuller as $kapsul):
                    $isReady   = $kapsul['status'] === 'Unlocked';
                    $isPublic  = isset($kapsul['is_public']) ? (int)$kapsul['is_public'] : 0;
                ?>
        <div class="capsule-card <?= $isReady ? 'is-ready' : '' ?>">
            <!-- Üst: Rozetler -->
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                <?php if ($isReady): ?>
                    <span class="status unlocked" style="animation:pulse-ready 2s ease-in-out infinite">✨ AÇILMAYA HAZIR</span>
                <?php else: ?>
                    <span class="status locked">🔒 KİLİTLİ</span>
                <?php endif; ?>
                <span style="font-size:0.62rem;font-weight:700;letter-spacing:1px;padding:2px 7px;border-radius:50px;<?= $isPublic ? 'background:rgba(0,242,254,0.08);color:#00f2fe;border:1px solid rgba(0,242,254,0.2)' : 'background:rgba(148,163,184,0.08);color:#94a3b8;border:1px solid rgba(148,163,184,0.2)' ?>">
                    <?= $isPublic ? '🌐 AÇIK' : '🔐 GİZLİ' ?>
                </span>
            </div>

            <!-- Orta: Başlık + Tarih -->
            <div class="pill-info" style="padding-right:34px">
                <h3><?= htmlspecialchars($kapsul['title']) ?></h3>
                <p class="date-info"><?= $isReady ? '🔓 ' : '📅 ' ?><?= date('d.m.Y', strtotime($kapsul['target_date'])) ?></p>
            </div>

            <!-- Alt: Timer veya Aç Butonu -->
            <div class="pill-action">
                <?php if ($isReady): ?>
                    <button
                        class="btn-primary open-ready-btn"
                        data-id="<?= $kapsul['capsule_id'] ?>"
                        style="padding:10px 20px;font-size:0.85rem;letter-spacing:1px;background:linear-gradient(135deg,#059669,#34d399);box-shadow:0 6px 20px rgba(52,211,153,0.35);width:100%">
                        🔓 Kapsülü Aç
                    </button>
                <?php else: ?>
                    <div class="timer" data-date="<?= $kapsul['target_date'] ?>" style="font-size:0.8rem;width:100%;text-align:center">
                        Yükleniyor...
                    </div>
                <?php endif; ?>
            </div>

            <!-- Silme (her zaman sağ üstte) -->
            <div class="pill-delete">
                <button class="delete-btn icon-btn" data-id="<?= $kapsul['capsule_id'] ?>" title="Kapsülü Sil">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (count($kapsuller) == 0): ?>
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;background:rgba(34,211,238,0.03);border:1px dashed rgba(34,211,238,0.3);border-radius:15px">
            <?php if ($search_q !== '' || $filter_stat !== 'all'): ?>
                <div style="font-size:4rem;margin-bottom:15px;opacity:0.5">🔍</div>
                <h3 style="color:var(--text-primary);font-size:1.5rem;margin-bottom:10px">Sonuç Bulunamadı</h3>
                <p style="color:var(--text-secondary);margin-bottom:20px">Bu filtreyle eşleşen kapsül yok.</p>
                <a href="index.php" style="display:inline-block;background:rgba(0,242,254,0.1);border:1px solid rgba(0,242,254,0.3);color:#00f2fe;padding:10px 24px;border-radius:50px;text-decoration:none;font-size:0.9rem;">← Tüm Kapsülleri Göster</a>
            <?php else: ?>
                <div style="font-size:4rem;margin-bottom:15px;opacity:0.5">🌌</div>
                <h3 style="color:var(--text-primary);font-size:1.5rem;margin-bottom:10px">Zaman Çizelgen Boş</h3>
                <p style="color:var(--text-secondary);margin-bottom:25px">Geleceğe henüz hiçbir iz bırakmadın.</p>
                <button class="btn-primary open-modal-btn">İlk Kapsülünü Fırlat 🚀</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

            <!-- ======= PAGINATION ======= -->
            <?php if ($total_pages > 1): ?>
            <div style="display:flex;justify-content:center;align-items:center;gap:6px;margin-top:36px;flex-wrap:wrap;" id="capsule-section">

                <?php if ($cur_page > 1): ?>
                <a href="<?= buildUrl($cur_page-1, $filter_stat, $search_q) ?>"
                    style="padding:8px 16px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:var(--text-secondary);text-decoration:none;font-size:0.85rem;transition:all 0.2s;"
                    onmouseover="this.style.borderColor='rgba(0,242,254,0.4)';this.style.color='#00f2fe';"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.08)';this.style.color='var(--text-secondary)';">← Önceki</a>
                <?php endif; ?>

                <?php
                $range = 2;
                $start = max(1, $cur_page - $range);
                $end   = min($total_pages, $cur_page + $range);
                if ($start > 1): ?>
                    <a href="<?= buildUrl(1, $filter_stat, $search_q) ?>" style="padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:var(--text-secondary);text-decoration:none;font-size:0.85rem;">1</a>
                    <?php if ($start > 2): ?><span style="color:var(--text-secondary);padding:0 4px;">…</span><?php endif; ?>
                <?php endif;

                for ($i = $start; $i <= $end; $i++):
                    $isActive = ($i === $cur_page);
                ?>
                    <a href="<?= buildUrl($i, $filter_stat, $search_q) ?>"
                        style="padding:8px 14px;border-radius:8px;text-decoration:none;font-size:0.85rem;font-weight:<?= $isActive ? '700' : '400' ?>;transition:all 0.2s;
                            <?= $isActive
                                ? 'background:rgba(0,242,254,0.15);border:1px solid rgba(0,242,254,0.5);color:#00f2fe;box-shadow:0 0 10px rgba(0,242,254,0.15);'
                                : 'background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:var(--text-secondary);' ?>"
                        onmouseover="this.style.borderColor='rgba(0,242,254,0.4)';this.style.color='#00f2fe';"
                        onmouseout="this.style.borderColor='<?= $isActive ? 'rgba(0,242,254,0.5)' : 'rgba(255,255,255,0.08)' ?>';this.style.color='<?= $isActive ? '#00f2fe' : 'var(--text-secondary)' ?>';"
                    ><?= $i ?></a>
                <?php endfor;

                if ($end < $total_pages): ?>
                    <?php if ($end < $total_pages - 1): ?><span style="color:var(--text-secondary);padding:0 4px;">…</span><?php endif; ?>
                    <a href="<?= buildUrl($total_pages, $filter_stat, $search_q) ?>" style="padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:var(--text-secondary);text-decoration:none;font-size:0.85rem;"><?= $total_pages ?></a>
                <?php endif; ?>

                <?php if ($cur_page < $total_pages): ?>
                <a href="<?= buildUrl($cur_page+1, $filter_stat, $search_q) ?>"
                    style="padding:8px 16px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:var(--text-secondary);text-decoration:none;font-size:0.85rem;transition:all 0.2s;"
                    onmouseover="this.style.borderColor='rgba(0,242,254,0.4)';this.style.color='#00f2fe';"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.08)';this.style.color='var(--text-secondary)';">Sonraki →</a>
                <?php endif; ?>

            </div>
            <?php endif; ?>

        </section>
    </main>

<!-- ============================================== -->
<!-- 🔓 KAPSÜL AÇMA ANİMASYONU OVERLAY              -->
<!-- ============================================== -->
<div id="openOverlay" style="display:none;position:fixed;inset:0;z-index:9999;background:#010714;align-items:center;justify-content:center;overflow:hidden;">
    <canvas id="openStarCanvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;"></canvas>
    <div id="openBurst" style="position:absolute;top:50%;left:50%;width:0;height:0;border-radius:50%;transform:translate(-50%,-50%);pointer-events:none;"></div>
    <div id="openCenterWrap" style="position:relative;z-index:10;display:flex;flex-direction:column;align-items:center;gap:0;">
        <div id="openCapsule" style="width:44px;height:110px;border-radius:26px;position:relative;transform:translateY(-110vh);">
            <div id="openCapsuleTop" style="width:44px;height:55px;background:linear-gradient(160deg,#00f2fe,#0ea5e9);border-radius:26px 26px 0 0;position:relative;overflow:hidden;box-shadow:0 0 30px rgba(0,242,254,0.6),0 0 60px rgba(0,242,254,0.2);">
                <div style="position:absolute;top:8%;left:18%;width:16%;height:80%;background:linear-gradient(to bottom,rgba(255,255,255,0.7),transparent);border-radius:10px;"></div>
            </div>
            <div id="openCapsuleBottom" style="width:44px;height:55px;background:linear-gradient(160deg,#34d399,#059669);border-radius:0 0 26px 26px;position:relative;overflow:hidden;box-shadow:0 0 30px rgba(52,211,153,0.6),0 0 60px rgba(52,211,153,0.2);">
                <div style="position:absolute;top:8%;left:18%;width:16%;height:80%;background:linear-gradient(to bottom,rgba(255,255,255,0.5),transparent);border-radius:10px;"></div>
            </div>
            <div style="position:absolute;top:50%;left:-4px;right:-4px;height:2px;background:rgba(255,255,255,0.5);transform:translateY(-50%);box-shadow:0 0 8px #fff;"></div>
        </div>
        <div id="openStatusText" style="margin-top:36px;font-family:'Courier New',monospace;font-size:0.95rem;letter-spacing:2px;color:#00f2fe;text-shadow:0 0 12px #00f2fe;min-height:28px;text-align:center;"></div>
        <div id="openProgressBar" style="width:220px;height:2px;background:rgba(255,255,255,0.08);border-radius:2px;margin-top:14px;overflow:hidden;">
            <div id="openProgressFill" style="width:0%;height:100%;background:linear-gradient(to right,#00f2fe,#34d399);transition:width 0.4s ease;box-shadow:0 0 8px #00f2fe;"></div>
        </div>
    </div>
</div>

<style>
@keyframes cap-drop {
    0%   { transform: translateY(-110vh) rotate(-5deg) scale(0.7); opacity:0; }
    70%  { transform: translateY(16px) rotate(1deg) scale(1.05); opacity:1; }
    85%  { transform: translateY(-8px) rotate(-0.5deg) scale(0.98); }
    100% { transform: translateY(0) rotate(0deg) scale(1); opacity:1; }
}
@keyframes cap-pulse {
    0%,100% { box-shadow: 0 0 30px rgba(0,242,254,0.6), 0 0 60px rgba(0,242,254,0.2); }
    50%      { box-shadow: 0 0 50px rgba(0,242,254,0.9), 0 0 100px rgba(0,242,254,0.4); }
}
@keyframes cap-split-top {
    0%   { transform: translateY(0); }
    100% { transform: translateY(-80px) rotate(-8deg); opacity:0; }
}
@keyframes cap-split-bot {
    0%   { transform: translateY(0); }
    100% { transform: translateY(80px) rotate(8deg); opacity:0; }
}
@keyframes ring-wave {
    0%   { transform:translate(-50%,-50%) scale(0); opacity:0.9; }
    100% { transform:translate(-50%,-50%) scale(8); opacity:0; }
}
@keyframes burst-flash {
    0%   { width:0; height:0; opacity:1; }
    40%  { width:300vw; height:300vw; opacity:0.25; }
    100% { width:300vw; height:300vw; opacity:0; }
}
@keyframes data-float {
    0%   { transform:translateY(0) rotate(0deg); opacity:0.9; }
    100% { transform:translateY(-120px) rotate(360deg); opacity:0; }
}
@keyframes shake {
    0%,100% { transform:translate(0,0); }
    20%     { transform:translate(-6px, 4px); }
    40%     { transform:translate(6px, -4px); }
    60%     { transform:translate(-4px, 6px); }
    80%     { transform:translate(4px, -2px); }
}
@keyframes glitch {
    0%,100% { clip-path:none; transform:none; }
    20%     { clip-path:inset(20% 0 60% 0); transform:translate(-4px,0); }
    40%     { clip-path:inset(60% 0 10% 0); transform:translate(4px,0); }
    60%     { clip-path:none; transform:none; }
}
</style>

<script>
/* ============ WARP STAR FIELD ============ */
function startOpenStars() {
    const canvas = document.getElementById('openStarCanvas');
    const ctx = canvas.getContext('2d');
    let W, H, raf;
    let phase = 'idle'; // idle → warp → settle
    let warpProgress = 0;

    function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
    resize(); window.addEventListener('resize', resize);

    // Warp stars
    const warpStars = Array.from({length: 250}, () => ({
        x: (Math.random()-0.5)*2, y: (Math.random()-0.5)*2,
        z: Math.random(), pz: 0
    }));
    // Idle twinkle stars
    const idleStars = Array.from({length: 200}, () => ({
        x: Math.random()*2-1, y: Math.random()*2-1,
        r: Math.random()*1.2+0.3, a: Math.random()*Math.PI*2, sp: 0.015+Math.random()*0.02
    }));

    function draw() {
        ctx.fillStyle = 'rgba(1,7,20,0.18)';
        ctx.fillRect(0,0,W,H);
        const cx = W/2, cy = H/2;

        if (phase === 'warp') {
            warpProgress = Math.min(warpProgress + 0.018, 1);
            const speed = 0.015 + warpProgress * 0.06;
            warpStars.forEach(s => {
                s.pz = s.z;
                s.z -= speed;
                if (s.z <= 0) { s.x=(Math.random()-0.5)*2; s.y=(Math.random()-0.5)*2; s.z=1; s.pz=1; }
                const sx = (s.x/s.z)*cx+cx, sy = (s.y/s.z)*cy+cy;
                const px = (s.x/s.pz)*cx+cx, py = (s.y/s.pz)*cy+cy;
                const len = Math.hypot(sx-px,sy-py);
                const bright = Math.min(1, (1-s.z)*1.4);
                ctx.strokeStyle = `rgba(${180+Math.floor(bright*75)},${230+Math.floor(bright*25)},255,${bright})`;
                ctx.lineWidth = Math.max(0.4, (1-s.z)*2.5);
                ctx.beginPath(); ctx.moveTo(px,py); ctx.lineTo(sx,sy); ctx.stroke();
            });
        } else {
            idleStars.forEach(s => {
                s.a += s.sp;
                const sx = (s.x)*cx+cx, sy = (s.y)*cy+cy;
                const brightness = (Math.sin(s.a)*0.5+0.5);
                ctx.beginPath();
                ctx.arc(sx, sy, s.r, 0, Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${brightness*0.85})`;
                ctx.fill();
            });
        }
        raf = requestAnimationFrame(draw);
    }
    draw();

    return {
        warp: () => { phase = 'warp'; warpProgress = 0; },
        settle: () => { phase = 'idle'; },
        stop: () => { cancelAnimationFrame(raf); window.removeEventListener('resize', resize); }
    };
}

/* ============ RING WAVE FACTORY ============ */
function fireRingWave(color, delay) {
    setTimeout(() => {
        const r = document.createElement('div');
        r.style.cssText = `position:fixed;top:50%;left:50%;width:80px;height:80px;
            border:2px solid ${color};border-radius:50%;pointer-events:none;
            transform:translate(-50%,-50%) scale(0);opacity:0.9;z-index:9998;
            box-shadow:0 0 20px ${color};animation:ring-wave 0.9s ease-out forwards;`;
        document.body.appendChild(r);
        setTimeout(() => r.remove(), 950);
    }, delay);
}

/* ============ DATA PARTICLES ============ */
function spawnDataParticles() {
    const chars = '01アイウエオΩΣ∞✦★◆';
    const colors = ['#00f2fe','#34d399','#a78bfa','#f59e0b'];
    for (let i = 0; i < 22; i++) {
        setTimeout(() => {
            const el = document.createElement('div');
            el.textContent = chars[Math.floor(Math.random()*chars.length)];
            const angle = Math.random()*Math.PI*2;
            const dist  = 80 + Math.random()*160;
            const tx = Math.cos(angle)*dist, ty = Math.sin(angle)*dist;
            el.style.cssText = `position:fixed;z-index:9998;pointer-events:none;
                left:calc(50% + ${tx}px);top:calc(50% + ${ty}px);
                font-family:'Courier New',monospace;font-size:${10+Math.random()*14}px;
                color:${colors[Math.floor(Math.random()*colors.length)]};
                text-shadow:0 0 8px currentColor;
                animation:data-float ${0.8+Math.random()*0.8}s ease-out forwards;`;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 1600);
        }, i * 60);
    }
}

/* ============ TYPEWRITER ============ */
function typeWrite(el, text, speed, done) {
    el.textContent = '';
    let i = 0;
    const t = setInterval(() => {
        el.textContent += text[i++];
        if (i >= text.length) { clearInterval(t); if(done) done(); }
    }, speed);
    return t;
}

/* ============ MAIN ANIMATION ============ */
function playOpenAnimation(id, callback) {
    const overlay   = document.getElementById('openOverlay');
    const capsule   = document.getElementById('openCapsule');
    const capTop    = document.getElementById('openCapsuleTop');
    const capBot    = document.getElementById('openCapsuleBottom');
    const statusTxt = document.getElementById('openStatusText');
    const progFill  = document.getElementById('openProgressFill');
    const burst     = document.getElementById('openBurst');

    // Reset
    overlay.style.display = 'flex'; overlay.style.opacity = '1';
    capsule.style.animation = 'none'; capsule.style.opacity = '1';
    capsule.style.transform = 'translateY(-110vh)';
    capTop.style.animation = capBot.style.animation = 'none';
    burst.style.animation = 'none'; burst.style.width = burst.style.height = '0';
    statusTxt.textContent = ''; progFill.style.width = '0%';
    void capsule.offsetWidth;

    const stars = startOpenStars();

    // --- PHASE 1: WARP (0-0.6s) ---
    stars.warp();

    // --- PHASE 2: CAPSULE DROPS (0.6s) ---
    setTimeout(() => {
        stars.settle();
        capsule.style.animation = 'cap-drop 1.1s cubic-bezier(0.22,1,0.36,1) forwards';
        capTop.style.animation = 'cap-pulse 1.2s ease-in-out infinite';
    }, 600);

    // --- PHASE 3: SIMPLE LOADING (1.8s) ---
    setTimeout(() => {
        statusTxt.textContent = '⏳  Yükleniyor...';
        statusTxt.style.opacity = '1';
        statusTxt.style.transition = 'opacity 0.4s';
        // Progress bar yavaşça dolar
        let pct = 0;
        const progTimer = setInterval(() => {
            pct = Math.min(pct + 2, 95);
            progFill.style.width = pct + '%';
        }, 40);
        // Patlama öncesi progress'i %100 yap
        setTimeout(() => { clearInterval(progTimer); progFill.style.width = '100%'; }, 2050);
    }, 1800);

    // --- PHASE 4: EXPLOSION (3.9s) ---
    setTimeout(() => {
        // Screen shake
        overlay.style.animation = 'shake 0.35s ease-in-out';
        setTimeout(() => overlay.style.animation = '', 360);

        // Light burst
        burst.style.cssText = `position:absolute;top:50%;left:50%;border-radius:50%;
            transform:translate(-50%,-50%);pointer-events:none;
            background:radial-gradient(circle,rgba(255,255,255,0.95) 0%,rgba(0,242,254,0.5) 40%,transparent 70%);
            animation:burst-flash 0.7s ease-out forwards;`;

        // Multi-ring shockwaves
        fireRingWave('#ffffff', 0);
        fireRingWave('#00f2fe', 80);
        fireRingWave('#34d399', 180);
        fireRingWave('#a78bfa', 300);

        // Capsule split
        capTop.style.animation = 'cap-split-top 0.55s ease-in forwards';
        capBot.style.animation = 'cap-split-bot 0.55s ease-in forwards';

        // Data particles rain
        spawnDataParticles();

        // Status message
        setTimeout(() => {
            statusTxt.style.color = '#fbbf24';
            statusTxt.style.fontSize = '1.15rem';
            statusTxt.style.letterSpacing = '3px';
            statusTxt.style.textShadow = '0 0 20px #fbbf24';
            typeWrite(statusTxt, '✨  KAPSÜL AÇILDI  ✨', 45);
            progFill.style.background = 'linear-gradient(to right,#fbbf24,#f59e0b)';
            progFill.style.boxShadow = '0 0 12px #fbbf24';
        }, 300);

    }, 3900);

    // --- PHASE 5: FADE OUT (5.2s) ---
    setTimeout(() => {
        stars.stop();
        overlay.style.transition = 'opacity 0.7s ease';
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.style.transition = '';
            if (typeof callback === 'function') callback();
        }, 720);
    }, 5200);
}

// "Kapsülü Aç" butonları → animasyon → mesaj popup → profil
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.open-ready-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const id = this.dataset.id;

                // 1. Önce veritabanında kapsülü "Açıldı" olarak işaretle
                const formData = new FormData();
                formData.append('action', 'open');
                formData.append('capsule_id', id);
                fetch('../backend/ajax_islemler.php', { method: 'POST', body: formData });

                // 2. Animasyonu oynat, bitince içeriği çek ve popup göster
                playOpenAnimation(id, () => {
                    fetch(`get_capsule_content.php?id=${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            window.location.href = 'profil.php#arsiv';
                            return;
                        }
                        showCapsuleReadPopup(data);
                    })
                    .catch(() => {
                        window.location.href = 'profil.php#arsiv';
                    });
                });
            } catch (err) {
                alert('JS Hatasi: ' + err.message);
            }
        });
    });
});

function showCapsuleReadPopup(data) {
    const popup = document.getElementById('capsuleReadPopup');
    const titleEl = document.getElementById('crp-title');
    const dateEl  = document.getElementById('crp-date');
    const bodyEl  = document.getElementById('crp-body');

    titleEl.textContent = data.title || 'Kapsül';
    if (data.target_date) {
        const d = new Date(data.target_date);
        dateEl.textContent = '📅 ' + d.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' });
    } else {
        dateEl.textContent = '';
    }

    bodyEl.innerHTML = '';
    if (data.contents && data.contents.length > 0) {
        data.contents.forEach(c => {
            if (c.type === 'Text') {
                const p = document.createElement('p');
                p.style.cssText = 'white-space:pre-wrap;line-height:1.8;color:#e2e8f0;font-size:1rem;';
                p.textContent = c.text;
                bodyEl.appendChild(p);
            } else if (c.type === 'Media') {
                const img = document.createElement('img');
                img.src = '../' + c.url;
                img.style.cssText = 'max-width:100%;border-radius:12px;margin-top:12px;';
                bodyEl.appendChild(img);
            }
        });
    } else {
        bodyEl.innerHTML = '<p style="color:var(--text-secondary);font-style:italic;">Bu kapsülde içerik bulunamadı.</p>';
    }

    popup.style.display = 'flex';
    setTimeout(() => popup.classList.add('crp-show'), 10);
}
</script>

<!-- ============================================== -->
<!-- 📨 KAPSÜL OKUMA POPUP                         -->
<!-- ============================================== -->
<div id="capsuleReadPopup" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(2,6,23,0.85);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:20px;">
    <div style="background:linear-gradient(145deg,#0e1124,#060b1a);border:1px solid rgba(0,242,254,0.25);border-radius:24px;max-width:600px;width:100%;max-height:80vh;overflow-y:auto;padding:40px;position:relative;box-shadow:0 0 60px rgba(0,242,254,0.15);">
        <!-- Üst çizgi -->
        <div style="position:absolute;top:0;left:10%;right:10%;height:1px;background:linear-gradient(to right,transparent,#00f2fe,transparent);opacity:0.5;"></div>

        <!-- Başlık -->
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
            <div>
                <div style="font-size:0.75rem;letter-spacing:3px;color:#00f2fe;text-transform:uppercase;margin-bottom:8px;">✨ Kapsül Açıldı</div>
                <h2 id="crp-title" style="font-size:1.6rem;font-weight:800;color:#fff;margin:0;"></h2>
                <div id="crp-date" style="color:var(--text-secondary);font-size:0.85rem;margin-top:6px;"></div>
            </div>
            <button onclick="document.getElementById('capsuleReadPopup').style.display='none';window.location.href='profil.php#arsiv';"
                style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:50%;width:36px;height:36px;color:#fff;font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.2s;"
                onmouseover="this.style.background='rgba(0,242,254,0.15)';this.style.borderColor='#00f2fe';"
                onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='rgba(255,255,255,0.1)';">✕</button>
        </div>

        <!-- Ayraç -->
        <div style="height:1px;background:rgba(255,255,255,0.07);margin-bottom:24px;"></div>

        <!-- İçerik -->
        <div id="crp-body" style="min-height:80px;"></div>

        <!-- Alt buton -->
        <div style="margin-top:32px;text-align:center;">
            <button onclick="document.getElementById('capsuleReadPopup').style.display='none';window.location.href='profil.php#arsiv';"
                style="background:linear-gradient(135deg,#059669,#34d399);color:#fff;border:none;padding:12px 32px;border-radius:50px;font-size:0.9rem;font-weight:700;letter-spacing:1px;cursor:pointer;box-shadow:0 6px 20px rgba(52,211,153,0.3);transition:all 0.3s;"
                onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 30px rgba(52,211,153,0.4)';"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 6px 20px rgba(52,211,153,0.3)';">
                🔓 Arşivime Git →
            </button>
        </div>
    </div>
</div>

<div id="capsuleModal" class="modal">
    <div class="modal-content" style="max-width:600px">
        <span class="close-btn">&times;</span>

        <div style="text-align:center;margin-bottom:28px">
            <div style="font-size:2.5rem;margin-bottom:8px;filter:drop-shadow(0 0 12px rgba(0,242,254,0.6))">⏳</div>
            <h2 style="margin:0 0 4px;font-size:1.5rem">Yeni Zaman Kapsülü</h2>
            <p style="color:var(--text-secondary);font-size:0.88rem;margin:0">Mesajını mühürle, geleceğe fırlat</p>
        </div>

        <form id="capsuleForm" action="../backend/islemler.php" method="POST" enctype="multipart/form-data">

            <!-- BÖLÜM 1: İÇERİK -->
            <div style="background:rgba(0,242,254,0.03);border:1px solid rgba(0,242,254,0.1);border-radius:16px;padding:20px;margin-bottom:16px">
                <div style="font-size:0.75rem;font-weight:700;letter-spacing:2px;color:var(--accent);margin-bottom:14px;text-transform:uppercase">📝 Kapsül İçeriği</div>
                <input type="text" id="capsuleTitle" name="title"
                    placeholder="Başlık — Örn: 5 Yıl Sonra Kendime"
                    style="margin-bottom:12px" required>
                <textarea id="capsuleMessage" name="text_body"
                    placeholder="Geleceğe mesajını yaz... Bu satırları bir gün okuyacaksın."
                    style="min-height:110px;margin-bottom:0" required></textarea>
            </div>

            <!-- BÖLÜM 2: KATEGORİ -->
            <div style="background:rgba(179,0,255,0.03);border:1px solid rgba(179,0,255,0.1);border-radius:16px;padding:20px;margin-bottom:16px">
                <div style="font-size:0.75rem;font-weight:700;letter-spacing:2px;color:var(--accent-purple);margin-bottom:14px;text-transform:uppercase">🏷️ Kategori</div>
                <select id="category" name="category_id" style="margin-bottom:0">
                    <option value="1">🧬 Kişisel</option>
                    <option value="2">🎓 Eğitim</option>
                    <option value="3">🎮 Eğlence</option>
                </select>
            </div>

            <!-- BÖLÜM 3: TARİH -->
            <div style="background:rgba(96,165,250,0.03);border:1px solid rgba(96,165,250,0.15);border-radius:16px;padding:20px;margin-bottom:16px">
                <div style="font-size:0.75rem;font-weight:700;letter-spacing:2px;color:#60a5fa;margin-bottom:14px;text-transform:uppercase">📅 Açılış Tarihi</div>
                <div class="cyber-date-picker" style="margin:0">
                    <div class="date-box">
                        <button type="button" id="btnDayUp" class="cyber-btn">▲</button>
                        <span id="dispDay">01</span>
                        <button type="button" id="btnDayDown" class="cyber-btn">▼</button>
                        <small>GÜN</small>
                    </div>
                    <div class="date-box">
                        <button type="button" id="btnMonthUp" class="cyber-btn">▲</button>
                        <span id="dispMonth">01</span>
                        <button type="button" id="btnMonthDown" class="cyber-btn">▼</button>
                        <small>AY</small>
                    </div>
                    <div class="date-box year-box">
                        <button type="button" id="btnYearUp" class="cyber-btn">🚀</button>
                        <span id="dispYear">2026</span>
                        <button type="button" id="btnYearDown" class="cyber-btn">▼</button>
                        <small>YIL</small>
                    </div>
                </div>
                <input type="hidden" id="unlockDate" name="target_date" value="2026-01-01">
            </div>

            <!-- BÖLÜM 4: MEDYA (opsiyonel, gizle/göster) -->
            <div style="margin-bottom:16px">
                <button type="button" id="toggleMedia"
                    style="background:none;border:1px dashed rgba(255,255,255,0.15);color:var(--text-secondary);width:100%;padding:10px;border-radius:12px;cursor:pointer;font-family:inherit;font-size:0.88rem;transition:all 0.3s"
                    onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.15)';this.style.color='var(--text-secondary)'">
                    🖼️ Görsel veya Video Ekle (Opsiyonel)
                </button>
                <div id="mediaSection" style="display:none;margin-top:10px">
                    <input type="file" name="media_file" accept="image/*,video/*"
                        style="background:var(--bg-deep);padding:10px;border:1px dashed var(--accent);color:white;border-radius:10px;width:100%;box-sizing:border-box;cursor:pointer;margin-bottom:0">
                </div>
            </div>

            <!-- BÖLÜM 5: ALICI (opsiyonel, gizle/göster) -->
            <div style="margin-bottom:20px">
                <button type="button" id="toggleRecipient"
                    style="background:none;border:1px dashed rgba(255,255,255,0.15);color:var(--text-secondary);width:100%;padding:10px;border-radius:12px;cursor:pointer;font-family:inherit;font-size:0.88rem;transition:all 0.3s"
                    onmouseover="this.style.borderColor='var(--accent-purple)';this.style.color='var(--accent-purple)'"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.15)';this.style.color='var(--text-secondary)'">
                    👤 Başkasına Gönder (Opsiyonel)
                </button>
                <div id="recipientSection" style="display:none;margin-top:10px;display:none">
                    <div style="display:flex;gap:10px">
                        <input type="text" name="recipient_name" placeholder="Alıcının Adı Soyadı" style="margin-bottom:0">
                        <input type="email" name="recipient_email" placeholder="E-posta Adresi" style="margin-bottom:0">
                    </div>
                </div>
            </div>

            <!-- BÖLÜM 6: GİZLİLİK -->
            <div style="margin-bottom:20px">
                <div style="font-size:0.75rem;font-weight:700;letter-spacing:2px;color:#94a3b8;margin-bottom:10px;text-transform:uppercase">🔒 Gizlilik Ayarı</div>
                <div style="display:flex;gap:0;border:1px solid rgba(255,255,255,0.1);border-radius:12px;overflow:hidden">
                    <label id="privLabelPrivate" style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;cursor:pointer;font-size:0.88rem;font-weight:600;background:rgba(148,163,184,0.15);color:#94a3b8;transition:all 0.3s;border-right:1px solid rgba(255,255,255,0.08)">
                        <input type="radio" name="is_public" value="0" checked style="display:none" id="radioPrivate">
                        🔐 Gizli
                    </label>
                    <label id="privLabelPublic" style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;cursor:pointer;font-size:0.88rem;font-weight:600;background:transparent;color:var(--text-secondary);transition:all 0.3s">
                        <input type="radio" name="is_public" value="1" style="display:none" id="radioPublic">
                        🌐 Herkese Açık
                    </label>
                </div>
                <p style="font-size:0.75rem;color:var(--text-secondary);margin:6px 0 0;opacity:0.7">Herkese açık kapsüller açıldıktan sonra profilinde görünür.</p>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;padding:16px;font-size:1rem;letter-spacing:2px">
                🚀 Kapsülü Zaman Boşluğuna Gönder
            </button>
        </form>
    </div>
</div>

<script>
// Opsiyonel bölümleri aç/kapat
document.getElementById('toggleMedia').addEventListener('click', function() {
    const s = document.getElementById('mediaSection');
    const open = s.style.display !== 'none';
    s.style.display = open ? 'none' : 'block';
    this.textContent = open ? '🖼️ Görsel veya Video Ekle (Opsiyonel)' : '✕ Medyayı Kaldır';
});
document.getElementById('toggleRecipient').addEventListener('click', function() {
    const s = document.getElementById('recipientSection');
    const open = s.style.display !== 'none';
    s.style.display = open ? 'none' : 'block';
    this.textContent = open ? '👤 Başkasına Gönder (Opsiyonel)' : '✕ Alıcıyı Kaldır';
});

// Gizlilik toggle görsel güncelleme
function updatePrivacyUI() {
    const isPrivate = document.getElementById('radioPrivate').checked;
    const lblPriv = document.getElementById('privLabelPrivate');
    const lblPub  = document.getElementById('privLabelPublic');
    if (isPrivate) {
        lblPriv.style.background = 'rgba(148,163,184,0.2)';
        lblPriv.style.color      = '#e2e8f0';
        lblPub.style.background  = 'transparent';
        lblPub.style.color       = 'var(--text-secondary)';
    } else {
        lblPub.style.background  = 'rgba(0,242,254,0.12)';
        lblPub.style.color       = '#00f2fe';
        lblPriv.style.background = 'transparent';
        lblPriv.style.color      = 'var(--text-secondary)';
    }
}
document.getElementById('radioPrivate').addEventListener('change', updatePrivacyUI);
document.getElementById('radioPublic').addEventListener('change', updatePrivacyUI);
updatePrivacyUI();
</script>

<script src="script.js?v=<?= time() ?>"></script>
<div id="uzayPrompt" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #020617; border: 2px solid #22d3ee; padding: 20px; border-radius: 15px; box-shadow: 0 0 20px rgba(34, 211, 238, 0.5); z-index: 9999; text-align: center; width: 300px;">
    <h3 style="color: #22d3ee; margin-top: 0; font-family: sans-serif;">Hedef Yıl Koordinatı</h3>
    <input type="number" id="uzayInput" style="background: #0f172a; color: white; border: 1px solid #334155; padding: 10px; border-radius: 5px; width: 80%; margin-bottom: 15px; text-align: center; font-size: 1.2rem; outline: none;">
    <br>
    <button id="uzayOnay" style="background: #22d3ee; color: #020617; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">🚀 Işınlan</button>
    <button id="uzayIptal" style="background: transparent; color: #94a3b8; border: 1px solid #334155; padding: 8px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">İptal</button>
</div>

<!-- Particles.js -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
    particlesJS("particles-js", {
        "particles": {
            "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
            "color": { "value": ["#00f2fe", "#b300ff", "#ffffff"] },
            "shape": { "type": "circle" },
            "opacity": { "value": 0.2, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.05, "sync": false } },
            "size": { "value": 3, "random": true, "anim": { "enable": true, "speed": 2, "size_min": 0.1, "sync": false } },
            "line_linked": { "enable": true, "distance": 150, "color": "#00f2fe", "opacity": 0.1, "width": 1 },
            "move": { "enable": true, "speed": 1, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false }
        },
        "interactivity": {
            "detect_on": "window",
            "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
            "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 0.5 } }, "push": { "particles_nb": 4 } }
        },
        "retina_detect": true
    });
</script>

<!-- Kapsül Açılış Animasyon Overlay -->
<div id="openOverlay" style="display:none;position:fixed;inset:0;z-index:9998;background:#020617;justify-content:center;align-items:center;flex-direction:column;overflow:hidden;">
    <canvas id="openStarCanvas" style="position:absolute;inset:0;width:100%;height:100%"></canvas>
    <div style="position:relative;z-index:10;display:flex;flex-direction:column;align-items:center;gap:30px">
        <!-- Kapsül iniyormuş gibi yukardan aşağı -->
        <div id="openCapsule" style="width:52px;height:130px;border-radius:100px;background:linear-gradient(180deg,#00f2fe 0%,#4facfe 45%,rgba(255,255,255,0.9) 50%,#b300ff 55%,#7c3aed 100%);border:1.5px solid rgba(255,255,255,0.25);box-shadow:inset 8px 0 18px rgba(255,255,255,0.5),inset -8px 0 18px rgba(0,0,0,0.5),0 0 30px rgba(0,242,254,0.5),0 0 60px rgba(179,0,255,0.3);position:relative;animation:open-descend 1s cubic-bezier(0.2,0,0.8,1) forwards;opacity:0;">
            <div style="position:absolute;top:6%;left:14%;width:12%;height:85%;background:linear-gradient(to bottom,rgba(255,255,255,0.8),transparent);border-radius:100px;filter:blur(1px)"></div>
        </div>
        <!-- Kırılma halkası -->
        <div id="openRing" style="position:absolute;width:80px;height:80px;border-radius:50%;border:2px solid rgba(0,242,254,0.8);box-shadow:0 0 25px rgba(0,242,254,0.6);transform:scale(0);opacity:0;transition:none"></div>
        <!-- Durum metni -->
        <div style="font-family:'Courier New',monospace;font-size:0.9rem;color:#00f2fe;text-shadow:0 0 8px rgba(0,242,254,0.6);letter-spacing:2px;display:flex;align-items:center;gap:10px">
            <div id="openDot" style="width:8px;height:8px;border-radius:50%;background:#00f2fe;box-shadow:0 0 10px #00f2fe;animation:dot-blink 0.8s ease-in-out infinite"></div>
            <span id="openText">Kapsül indiriliyor...</span>
        </div>
    </div>
</div>

<style>
@keyframes open-descend {
    0%   { transform: translateY(-80px); opacity: 0; }
    60%  { transform: translateY(10px);  opacity: 1; }
    80%  { transform: translateY(-4px);  }
    100% { transform: translateY(0);     opacity: 1; }
}
@keyframes open-crack {
    0%   { transform: scale(0) rotate(0deg);   opacity: 1; }
    50%  { transform: scale(2) rotate(45deg);  opacity: 0.8; }
    100% { transform: scale(4) rotate(90deg);  opacity: 0; }
}
@keyframes open-split-top {
    0%   { transform: translateY(0);   opacity: 1; }
    100% { transform: translateY(-60px) scale(0.5); opacity: 0; }
}
@keyframes open-split-bot {
    0%   { transform: translateY(0);   opacity: 1; }
    100% { transform: translateY(60px) scale(0.5);  opacity: 0; }
}
</style>

<script>
(function() {
    /* ---- Yıldız Canvas (açılış overlay için) ---- */
    function startOpenStars() {
        const canvas = document.getElementById('openStarCanvas');
        const ctx = canvas.getContext('2d');
        let W, H, stars = [], raf;
        function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
        function make(n) { stars = []; for(let i=0;i<n;i++) stars.push({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.5+0.3,a:Math.random(),sp:Math.random()*0.006+0.002}); }
        function draw() {
            ctx.clearRect(0,0,W,H);
            stars.forEach(s => {
                s.a += s.sp; if(s.a>1){s.a=0;s.x=Math.random()*W;s.y=Math.random()*H;}
                ctx.beginPath(); ctx.arc(s.x,s.y,s.r,0,Math.PI*2);
                ctx.fillStyle=`rgba(255,255,255,${Math.sin(s.a*Math.PI)*0.7})`; ctx.fill();
            });
            raf = requestAnimationFrame(draw);
        }
        resize(); make(160); draw();
        window.addEventListener('resize', () => { resize(); make(160); });
        return () => cancelAnimationFrame(raf);
    }

    let stopStars = null;

    /* ---- Açılış Animasyonu ---- */
    function playOpenAnimation(targetId) {
        const overlay   = document.getElementById('openOverlay');
        const capsuleEl = document.getElementById('openCapsule');
        const ringEl    = document.getElementById('openRing');
        const textEl    = document.getElementById('openText');

        // Overlay'i göster
        overlay.style.display = 'flex';
        stopStars = startOpenStars();

        // Kapsülü sıfırla & başlat
        capsuleEl.style.animation = 'none';
        capsuleEl.style.opacity   = '0';
        ringEl.style.transform    = 'scale(0)';
        ringEl.style.opacity      = '0';
        ringEl.style.transition   = 'none';

        const messages = [
            'Kapsül indiriliyor...',
            'Kriptografik kilit çözülüyor...',
            'Zaman katmanları açılıyor...',
            'Mesaj okunmaya hazır! 🔓'
        ];
        let mi = 0;
        textEl.textContent = messages[0];
        const msgTimer = setInterval(() => {
            mi = (mi + 1) % messages.length;
            textEl.style.opacity = '0';
            setTimeout(() => { textEl.textContent = messages[mi]; textEl.style.opacity = '1'; }, 250);
        }, 700);
        textEl.style.transition = 'opacity 0.25s';

        // Kapsülü indirme animasyonu
        setTimeout(() => {
            capsuleEl.style.animation = 'open-descend 0.9s cubic-bezier(0.2,0,0.8,1) forwards';
        }, 100);

        // Kırılma efekti
        setTimeout(() => {
            clearInterval(msgTimer);
            textEl.textContent = '✨ Açılıyor!';

            ringEl.style.transition = 'none';
            ringEl.style.transform  = 'scale(0)';
            ringEl.style.opacity    = '1';
            setTimeout(() => {
                ringEl.style.transition = 'transform 0.7s ease-out, opacity 0.7s ease-out';
                ringEl.style.transform  = 'scale(5)';
                ringEl.style.opacity    = '0';
            }, 50);

            // Kapsülü ikiye böl
            capsuleEl.style.clipPath   = 'inset(0 0 50% 0)';
            capsuleEl.style.transition = 'transform 0.5s ease-in, opacity 0.5s';
            capsuleEl.style.transform  = 'translateY(-50px)';
            capsuleEl.style.opacity    = '0';

        }, 2200);

        // Overlay'i kapat → callback veya içerik
        setTimeout(() => {
            if(stopStars) { stopStars(); stopStars = null; }
            overlay.style.transition = 'opacity 0.6s ease';
            overlay.style.opacity    = '0';
            setTimeout(() => {
                overlay.style.display  = 'none';
                overlay.style.opacity  = '1';
                capsuleEl.style.animation  = '';
                capsuleEl.style.transform  = '';
                capsuleEl.style.opacity    = '';
                capsuleEl.style.clipPath   = '';
                capsuleEl.style.transition = '';

                if (typeof callback === 'function') {
                    callback();
                }
            }, 650);
        }, 2900);
    }

    /* ---- Eski inline açma butonları (artık kullanılmıyor, fallback) ---- */
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.open-inline-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                playOpenAnimation(targetId, null);
            });
        });
    });
})();


<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>


<!-- YENİ: Temiz Uzay Fırlatma Animasyonu (Ses Yok) -->
<div id="launchOverlay">

    <!-- Yıldız Alanı -->
    <canvas id="starCanvas"></canvas>

    <!-- Merkez İçerik -->
    <div class="launch-center">
        <div class="launch-capsule-wrap">
            <!-- Kapsül gövdesi -->
            <div class="launch-capsule" id="launchCapsule">
                <div class="capsule-shine"></div>
            </div>
            <!-- Roket izi -->
            <div class="launch-trail" id="launchTrail"></div>
            <!-- Fırlatma halkası -->
            <div class="launch-ring" id="launchRing"></div>
        </div>

        <!-- Durum Metni -->
        <div class="launch-status" id="launchStatus">
            <div class="status-dot"></div>
            <span id="statusText">Kapsül hazırlanıyor...</span>
        </div>
    </div>

    <!-- Başarı Kartı (animasyon sonunda) -->
    <div class="launch-success-card" id="successCard">
        <div class="success-icon">✓</div>
        <h2 class="success-title">Kapsül Fırlatıldı!</h2>
        <p class="success-sub">Mesajın zaman tünelinde kilit altında.</p>
    </div>
</div>

<style>
#launchOverlay {
    position: fixed;
    inset: 0;
    background: #020617;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

/* Yıldız Canvas */
#starCanvas {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

/* Merkez sarıcı */
.launch-center {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
}

/* Kapsül sarıcı */
.launch-capsule-wrap {
    position: relative;
    width: 60px;
    height: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Kapsül */
.launch-capsule {
    width: 52px;
    height: 130px;
    border-radius: 100px;
    background: linear-gradient(180deg,
        #00f2fe 0%,
        #4facfe 45%,
        rgba(255,255,255,0.9) 50%,
        #b300ff 55%,
        #7c3aed 100%
    );
    border: 1.5px solid rgba(255,255,255,0.25);
    box-shadow:
        inset 8px 0 18px rgba(255,255,255,0.5),
        inset -8px 0 18px rgba(0,0,0,0.5),
        0 0 30px rgba(0,242,254,0.5),
        0 0 60px rgba(179,0,255,0.3);
    position: relative;
    animation: capsule-idle 2s ease-in-out infinite;
    transform-origin: center bottom;
}

.capsule-shine {
    position: absolute;
    top: 6%; left: 14%;
    width: 12%; height: 85%;
    background: linear-gradient(to bottom, rgba(255,255,255,0.8), transparent);
    border-radius: 100px;
    filter: blur(1px);
}

/* Roket izi */
.launch-trail {
    width: 4px;
    height: 0;
    background: linear-gradient(to bottom, rgba(0,242,254,0.8), transparent);
    border-radius: 2px;
    margin-top: 4px;
    filter: blur(1px);
    opacity: 0;
}

/* Fırlatma halkası */
.launch-ring {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%) scale(0);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid rgba(0,242,254,0.6);
    opacity: 0;
    box-shadow: 0 0 20px rgba(0,242,254,0.4);
}

/* Durum metni */
.launch-status {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Courier New', monospace;
    font-size: 0.95rem;
    color: #00f2fe;
    text-shadow: 0 0 8px rgba(0,242,254,0.6);
    letter-spacing: 1px;
}
.status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #00f2fe;
    box-shadow: 0 0 10px #00f2fe;
    animation: dot-blink 0.8s ease-in-out infinite;
}
@keyframes dot-blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.2; }
}

/* Başarı Kartı */
.launch-success-card {
    position: absolute;
    z-index: 20;
    text-align: center;
    opacity: 0;
    transform: translateY(30px) scale(0.9);
    transition: all 0.7s cubic-bezier(0.25, 1, 0.5, 1);
    pointer-events: none;
}
.launch-success-card.visible {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
.success-icon {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(0,242,254,0.15), rgba(52,211,153,0.15));
    border: 2px solid rgba(52,211,153,0.5);
    box-shadow: 0 0 30px rgba(52,211,153,0.3);
    display: flex; justify-content: center; align-items: center;
    font-size: 2rem; color: #34d399;
    margin: 0 auto 24px;
    animation: success-pulse 1.5s ease-in-out infinite;
}
@keyframes success-pulse {
    0%, 100% { box-shadow: 0 0 30px rgba(52,211,153,0.3); }
    50%       { box-shadow: 0 0 50px rgba(52,211,153,0.6); }
}
.success-title {
    font-size: 2.2rem; font-weight: 800; margin: 0 0 10px;
    background: linear-gradient(to right, #00f2fe, #34d399);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.success-sub {
    color: rgba(160,174,192,0.8);
    font-size: 1rem; margin: 0;
}

/* Kapsül raylama */
@keyframes capsule-idle {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-8px); }
}
@keyframes capsule-launch {
    0%   { transform: translateY(0) scale(1); opacity: 1; }
    20%  { transform: translateY(-10px) scale(1.05); }
    100% { transform: translateY(-130vh) scale(0.3); opacity: 0; }
}
@keyframes trail-grow {
    0%   { height: 0; opacity: 0; }
    30%  { height: 60px; opacity: 0.8; }
    100% { height: 180px; opacity: 0; }
}
@keyframes ring-expand {
    0%   { transform: translateX(-50%) scale(0); opacity: 0.8; }
    100% { transform: translateX(-50%) scale(4); opacity: 0; }
}
</style>

<script>
(function() {
    /* ---------- Yıldız Canvas ---------- */
    const canvas = document.getElementById('starCanvas');
    const ctx = canvas.getContext('2d');
    let W, H, stars = [], animFrame;

    function resize() {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }

    function makeStars(n) {
        stars = [];
        for (let i = 0; i < n; i++) {
            stars.push({
                x: Math.random() * W,
                y: Math.random() * H,
                r: Math.random() * 1.5 + 0.3,
                a: Math.random(),
                sp: Math.random() * 0.005 + 0.002
            });
        }
    }

    function drawStars() {
        ctx.clearRect(0, 0, W, H);
        stars.forEach(s => {
            s.a += s.sp;
            if (s.a > 1) { s.a = 0; s.x = Math.random() * W; s.y = Math.random() * H; }
            const alpha = Math.sin(s.a * Math.PI);
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${alpha * 0.7})`;
            ctx.fill();
        });
        animFrame = requestAnimationFrame(drawStars);
    }

    resize();
    makeStars(180);
    drawStars();
    window.addEventListener('resize', () => { resize(); makeStars(180); });

    /* ---------- Animasyon Zaman Çizelgesi ---------- */
    const capsule    = document.getElementById('launchCapsule');
    const trail      = document.getElementById('launchTrail');
    const ring       = document.getElementById('launchRing');
    const statusText = document.getElementById('statusText');
    const card       = document.getElementById('successCard');
    const overlay    = document.getElementById('launchOverlay');

    const messages = [
        'Kriptografik mühür uygulanıyor...',
        'Zaman koordinatları kilitleniyor...',
        'Fırlatma rampaları aktif...',
        'Kapsül zaman tüneline gönderiliyor...'
    ];
    let msgIdx = 0;

    // Mesaj döngüsü
    const msgTimer = setInterval(() => {
        msgIdx = (msgIdx + 1) % messages.length;
        statusText.style.opacity = '0';
        setTimeout(() => {
            statusText.textContent = messages[msgIdx];
            statusText.style.opacity = '1';
        }, 300);
    }, 900);

    statusText.style.transition = 'opacity 0.3s';

    // 2.8s → fırlatma
    setTimeout(() => {
        clearInterval(msgTimer);
        statusText.textContent = '🚀 Fırlatılıyor!';

        // Halkayı patlat
        ring.style.animation = 'ring-expand 0.8s ease-out forwards';

        // İzi aktif et
        trail.style.animation = 'trail-grow 1.2s ease-out forwards';

        // Kapsülü fırlat
        capsule.style.animation = 'capsule-launch 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards';

    }, 2800);

    // 4.2s → başarı kartını göster
    setTimeout(() => {
        document.getElementById('launchStatus').style.opacity = '0';
        document.querySelector('.launch-capsule-wrap').style.opacity = '0';
        card.classList.add('visible');
    }, 4200);

    // 6s → overlay'i kapat
    setTimeout(() => {
        cancelAnimationFrame(animFrame);
        overlay.style.transition = 'opacity 0.9s ease';
        overlay.style.opacity    = '0';
        setTimeout(() => {
            overlay.style.display = 'none';
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 900);
    }, 6000);
})();
</script>
<?php endif; ?>

</body>
</html>
