
<!DOCTYPE html>
<?php include 'db.php'; ?>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dijital Zaman Kapsülü</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">Zaman Kapsülü</div>
        <ul>
            <li><a href="#">Kapsüllerim</a></li>
            <li><a href="#">Yeni Kapsül Oluştur</a></li>
            <li><a href="#">Çıkış Yap</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <h1>Geleceğe Bir Mesaj Bırak</h1>
            <p>Anılarını, mesajlarını ve medyalarını kilitle; zamanı gelince açılsınlar.</p>
            <button class="btn-primary">Kapsül Oluştur</button>
        </section>

        <section class="capsule-container">
            <h2>Aktif Kapsüllerin</h2>
            <div class="capsule-grid" id="capsuleList">
                <?php
    // 1. Veritabanı sorgusunu hazırla (Örn: Giriş yapan kullanıcının kapsülleri)
    $sorgu = $db->query("SELECT * FROM CAPSULES WHERE sender_id = 1");
    $kapsuller = $sorgu->fetchAll();

    // 2. Döngüyü başlat (Her kapsül için bir tur döner)
    foreach($kapsuller as $kapsul) {
        ?>
        <div class="capsule-card">
            <h3><?php echo htmlspecialchars($kapsul['title']); ?></h3>
            <p>Açılış Tarihi: <?php echo $kapsul['target_date']; ?></p>
            
            
            <div class="timer" data-date="<?= $kapsul['target_date'] ?>">
                Geri Sayım Yükleniyor...
            </div>

            <span class="status <?php echo ($kapsul['status'] == 'Locked') ? 'locked' : 'unlocked'; ?>">
                <?php echo ($kapsul['status'] == 'Locked') ? 'Kilitli' : 'Açıldı'; ?>
            </span>
        </div>
        <?php
    } // 3. Döngüyü kapat
    ?>
</div>
           
        </section>
    </main>
    <div id="capsuleModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Yeni Zaman Kapsülü Kilitle</h2>
        <form id="capsuleForm" action="islemler.php" method="POST">
            <input type="text" id="capsuleTitle" name="title" placeholder="Kapsül Başlığı (Örn: Mezuniyet Mesajım)" required>
            <textarea id="capsuleMessage" name="text_body" placeholder="Geleceğe mesajını yaz..." required></textarea>
            <label>Gelecekteki Açılma Tarihi:</label>

<div class="cyber-date-picker">
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
            <select id="category" name="category_id">
                <option value="1">Kişisel</option>
                <option value="2">Eğitim</option>
                <option value="3">Eğlence</option>
            </select>
            <button type="submit" class="btn-primary">Kapsülü Zaman Boşluğuna Gönder</button>
        </form>
    </div>
</div>
<script src="script.js"></script>
<div id="uzayPrompt" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #020617; border: 2px solid #22d3ee; padding: 20px; border-radius: 15px; box-shadow: 0 0 20px rgba(34, 211, 238, 0.5); z-index: 9999; text-align: center; width: 300px;">
    <h3 style="color: #22d3ee; margin-top: 0; font-family: sans-serif;">Hedef Yıl Koordinatı</h3>
    <input type="number" id="uzayInput" style="background: #0f172a; color: white; border: 1px solid #334155; padding: 10px; border-radius: 5px; width: 80%; margin-bottom: 15px; text-align: center; font-size: 1.2rem; outline: none;">
    <br>
    <button id="uzayOnay" style="background: #22d3ee; color: #020617; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">🚀 Işınlan</button>
    <button id="uzayIptal" style="background: transparent; color: #94a3b8; border: 1px solid #334155; padding: 8px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">İptal</button>
</div>
</body>
</html>
