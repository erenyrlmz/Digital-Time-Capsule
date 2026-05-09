<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once '../backend/db.php';

$capsule_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Kapsülü getir
$sorgu = $db->prepare("SELECT * FROM CAPSULES WHERE capsule_id = ? AND sender_id = ?");
$sorgu->execute([$capsule_id, $user_id]);
$kapsul = $sorgu->fetch();

if (!$kapsul) {
    die("Kapsül bulunamadı veya yetkiniz yok.");
}

// Tarih geçtiyse otomatik olarak Unlocked yap (index.php ile aynı mantık)
if ($kapsul['status'] == 'Locked' && strtotime($kapsul['target_date']) <= time()) {
    $upd = $db->prepare("UPDATE CAPSULES SET status = 'Unlocked' WHERE capsule_id = ?");
    $upd->execute([$capsule_id]);
    $kapsul['status'] = 'Unlocked';
}

// Güvenlik Kontrolü: Kapsül hâlâ kilitliyse ve açılma vakti gelmediyse erişimi engelle
if ($kapsul['status'] == 'Locked') {
    die("Zaman paradoksu! Bu kapsülün açılış tarihi henüz gelmedi: " . htmlspecialchars($kapsul['target_date']));
}

// İçerikleri getir (Hem Metin Hem Medya)
$icerik_sorgu = $db->prepare("SELECT * FROM CONTENTS WHERE capsule_id = ? ORDER BY content_id ASC");
$icerik_sorgu->execute([$capsule_id]);
$icerikler = $icerik_sorgu->fetchAll();

// Alıcıları getir
$alici_sorgu = $db->prepare("SELECT * FROM RECIPIENTS WHERE capsule_id = ?");
$alici_sorgu->execute([$capsule_id]);
$alicilar = $alici_sorgu->fetchAll();

// Log tablosuna ekleme yap (Sadece ilk kez veya her açılışta loglanabilir. Burada her görüntülemeyi logluyoruz)
$ip_address = $_SERVER['REMOTE_ADDR'];
$log_insert = $db->prepare("INSERT INTO OPENING_LOGS (opening_time, ip_address, capsule_id) VALUES (NOW(), ?, ?)");
$log_insert->execute([$ip_address, $capsule_id]);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($kapsul['title']) ?> - Zaman Kapsülü</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* YENİ: KAPSÜL DETAY ÖZEL STİLLERİ (HAP KONSEPTİ) */
        .hologram-wrapper {
            padding: 4px;
            background: linear-gradient(135deg, #00f2fe 0%, #b300ff 100%);
            border-radius: 40px;
            box-shadow: 0 0 50px rgba(0, 242, 254, 0.2), 0 0 100px rgba(179, 0, 255, 0.1);
            margin-top: 20px;
            display: none; /* JS ile açılacak */
            animation: hologramFade 1.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .hologram-container {
            background: radial-gradient(circle at top, #0f172a 0%, #020617 100%);
            border-radius: 36px;
            padding: 50px;
            position: relative;
            overflow: hidden;
        }
        
        /* Arka plan tarama çizgileri */
        .hologram-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: repeating-linear-gradient(
                transparent,
                transparent 2px,
                rgba(0, 242, 254, 0.03) 3px,
                rgba(0, 242, 254, 0.03) 4px
            );
            pointer-events: none;
            z-index: 0;
        }

        .hologram-container > * {
            position: relative;
            z-index: 1;
        }

        @keyframes hologramFade {
            0% { opacity: 0; transform: translateY(50px) scale(0.95) rotateX(10deg); filter: blur(10px); }
            100% { opacity: 1; transform: translateY(0) scale(1) rotateX(0deg); filter: blur(0); }
        }

        .capsule-message {
            font-size: 1.15rem;
            line-height: 1.9;
            color: #e2e8f0;
            white-space: pre-wrap;
            margin-bottom: 40px;
            font-family: 'Inter', sans-serif;
            background: rgba(0,0,0,0.4);
            padding: 35px;
            border-radius: 20px;
            border-left: 5px solid #00f2fe;
            box-shadow: inset 0 0 30px rgba(0,0,0,0.8);
        }
        
        .message-title {
            text-align: center;
            background: linear-gradient(to right, #00f2fe, #b300ff);
            -webkit-background-clip: text;
            color: transparent;
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }
        
        .message-meta {
            text-align: center;
            color: #94a3b8;
            font-size: 1rem;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 30px;
        }

        /* YENİ: KAPSÜL PARÇALANMA ANİMASYONLARI */
        .interactive-scene {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 50vh;
            position: relative;
            z-index: 100;
        }

        .scene-title {
            color: var(--text-secondary);
            font-family: 'Courier New', monospace;
            margin-bottom: 40px;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: pulse-text 1.5s infinite alternate;
        }

        @keyframes pulse-text {
            from { opacity: 0.5; }
            to { opacity: 1; text-shadow: 0 0 10px #fff; }
        }

        .physical-capsule {
            width: 320px;
            height: 120px;
            position: relative;
            cursor: pointer;
            transition: all 0.5s ease;
        }

        .physical-capsule:hover {
            transform: scale(1.05);
            filter: brightness(1.2);
        }

        .cap-left, .cap-right {
            position: absolute;
            top: 0; 
            width: 160px; 
            height: 120px;
            transition: transform 1.5s cubic-bezier(0.25, 1, 0.5, 1);
            z-index: 5;
            box-shadow: inset 0 30px 40px rgba(0,0,0,0.6), 0 15px 30px rgba(0,0,0,0.5);
            background-size: cover;
        }

        .cap-left {
            left: 0;
            background: linear-gradient(90deg, #00f2fe 0%, #0284c7 100%);
            border-radius: 200px 0 0 200px;
            border-right: 4px solid #fff;
        }

        .cap-right {
            right: 0;
            background: linear-gradient(90deg, #6b21a8 0%, #b300ff 100%);
            border-radius: 0 200px 200px 0;
            border-left: 4px solid #fff;
        }

        /* Cam parlaması */
        .cap-left::after, .cap-right::after {
            content: '';
            position: absolute;
            top: 10%; left: 10%;
            width: 80%; height: 30%;
            background: linear-gradient(to bottom, rgba(255,255,255,0.3), transparent);
            border-radius: 100px;
            pointer-events: none;
        }

        .physical-capsule.split .cap-left {
            transform: translateX(-180px);
        }
        
        .physical-capsule.split .cap-right {
            transform: translateX(180px);
        }

        .secret-paper {
            position: absolute;
            top: 20px; 
            left: 110px; 
            width: 100px; 
            height: 80px;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            box-shadow: 0 0 20px rgba(255,255,255,0.8), inset 0 0 10px rgba(0,0,0,0.1);
            z-index: 2;
            border-radius: 5px;
            opacity: 0;
            transform: translateY(0) scale(0.8);
            transition: all 1.2s cubic-bezier(0.25, 1, 0.5, 1);
            display: flex; 
            justify-content: center; 
            align-items: center;
            cursor: pointer;
            border: 1px solid #ccc;
        }

        .secret-paper::before {
            content: '✉️';
            font-size: 2.5rem;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
        }

        .physical-capsule.split .secret-paper {
            opacity: 1;
            transform: translateY(-80px) scale(1.4) rotate(10deg);
            animation: float-paper 2s ease-in-out infinite 1.2s;
        }

        @keyframes float-paper {
            0%, 100% { transform: translateY(-80px) scale(1.4) rotate(10deg); }
            50% { transform: translateY(-90px) scale(1.4) rotate(5deg); }
        }

        .secret-paper:hover {
            box-shadow: 0 0 50px #fff, 0 0 20px #00f2fe;
            transform: translateY(-100px) scale(1.6) rotate(0deg) !important;
            animation: none;
        }

        /* Holo container gizli başlasın - Artık wrapper gizli başlıyor */
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo" style="text-decoration: none;">Zaman Kapsülü</a>
        <ul>
            <li><a href="index.php">Kapsüllerim</a></li>
            <li><a href="galaksi.php">Galaksi</a></li>
            <li><a href="istatistikler.php">İstatistikler</a></li>
            <li><a href="profil.php">Profilim</a></li>
            <li><a href="../backend/logout.php">Çıkış Yap</a></li>
        </ul>
    </nav>

    <div class="capsule-container" style="max-width: 900px; margin: 0 auto;">
        
        <!-- YENİ: Etkileşimli Parçalanma Sahnesi -->
        <div class="interactive-scene" id="interactiveScene">
            <h3 class="scene-title">Mühürlü Kapsülü Açmak İçin Dokun</h3>
            <div class="physical-capsule" id="phyCapsule">
                <div class="cap-left"></div>
                <div class="cap-right"></div>
                <div class="secret-paper" id="secretPaper" title="Gizli Mesajı Oku"></div>
            </div>
        </div>

        <!-- Gerçek İçerik (Kağıda tıklanınca açılır) -->
        <div class="hologram-wrapper" id="holoContent">
            <div class="hologram-container">
                <h1 class="message-title"><?= htmlspecialchars($kapsul['title']) ?></h1>
            <div class="message-meta">
                Açılış Hedef Tarihi: <?= date('d.m.Y', strtotime($kapsul['target_date'])) ?> | Durum: Başarıyla Çözüldü 🔓
                <br><br>
                <span style="color: var(--accent);">Kime:</span> 
                <?php if (count($alicilar) > 0): ?>
                    <?php 
                    $alici_listesi = [];
                    foreach($alicilar as $alici) {
                        $alici_listesi[] = htmlspecialchars($alici['full_name']) . " (" . htmlspecialchars($alici['email']) . ")";
                    }
                    echo implode(", ", $alici_listesi);
                    ?>
                <?php else: ?>
                    Kendime Not
                <?php endif; ?>
            </div>
            
            <div class="capsule-message">
                <?php 
                foreach ($icerikler as $icerik) {
                    if ($icerik['content_type'] == 'Text') {
                        echo "<div>" . nl2br(htmlspecialchars($icerik['text_body'])) . "</div><br>";
                    } elseif ($icerik['content_type'] == 'Media' && $icerik['file_url']) {
                        $ext = strtolower($icerik['file_extension']);
                        if (in_array($ext, ['mp4', 'webm'])) {
                            echo '<div style="text-align: center;"><video controls style="max-width:100%; max-height:400px; border-radius:10px; border:2px solid var(--accent); margin-bottom: 20px; box-shadow: 0 0 15px var(--accent-glow);">
                                    <source src="' . htmlspecialchars($icerik['file_url']) . '" type="video/' . $ext . '">
                                  </video></div>';
                        } else {
                            echo '<div style="text-align: center;"><img src="' . htmlspecialchars($icerik['file_url']) . '" style="max-width:100%; max-height:400px; border-radius:10px; border:2px solid var(--accent); margin-bottom: 20px; box-shadow: 0 0 15px var(--accent-glow);"></div>';
                        }
                    }
                }
                ?>
            </div>
            
            <div style="text-align: center;">
                <a href="index.php" class="btn-primary" style="text-decoration: none; padding: 15px 40px; font-size: 1.1rem; border-radius: 30px; box-shadow: 0 0 20px rgba(0, 242, 254, 0.4);">Zaman Çizelgesine Dön</a>
            </div>
            </div> <!-- End of hologram-container -->
        </div> <!-- End of hologram-wrapper -->
    </div>

    <!-- Ses Efektleri İçin -->
    <script>
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        function playSciFiBeep(type) {
            if(audioCtx.state === 'suspended') audioCtx.resume();
            const osc = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            if(type === 'split') { // Yumuşatılmış, düşük seste pnömatik açılma efekti
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(300, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(100, audioCtx.currentTime + 0.6);
                gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                gainNode.gain.linearRampToValueAtTime(0.08, audioCtx.currentTime + 0.1);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.6);
                osc.start(); osc.stop(audioCtx.currentTime + 0.6);
            } else if(type === 'paper') { // Kulak tırmalamayan çok hafif sihirli bir vınlama
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.8);
                gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                gainNode.gain.linearRampToValueAtTime(0.05, audioCtx.currentTime + 0.1);
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
                osc.start(); osc.stop(audioCtx.currentTime + 0.8);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const phyCapsule = document.getElementById('phyCapsule');
            const secretPaper = document.getElementById('secretPaper');
            const holoContent = document.getElementById('holoContent');
            const interactiveScene = document.getElementById('interactiveScene');
            const sceneTitle = document.querySelector('.scene-title');

            // 1. Aşama: Kapsülü İkiye Böl
            phyCapsule.addEventListener('click', function(e) {
                if(e.target === secretPaper) return; // Kağıda tıklanırsa bölme animasyonu tetiklenmesin
                
                if (!this.classList.contains('split')) {
                    this.classList.add('split');
                    sceneTitle.innerText = "Mesajı Çıkarmak İçin Zarfa Dokun";
                    playSciFiBeep('split');
                }
            });

            // 2. Aşama: Kağıdı Çıkar ve Hologramı Aç
            secretPaper.addEventListener('click', function(e) {
                e.stopPropagation();
                playSciFiBeep('paper');

                // Sahneyi beyaz ışıkla patlat ve büyüt
                interactiveScene.style.transition = "opacity 0.8s ease, transform 0.8s ease";
                interactiveScene.style.transform = "scale(3)";
                interactiveScene.style.opacity = "0";

                // Kısa süre sonra gerçek mesaj ekranını (Hologram) göster
                setTimeout(() => {
                    interactiveScene.style.display = "none";
                    holoContent.style.display = "block"; // Wrapper'ı görünür yap (Animasyon otomatik başlar)
                }, 800);
            });
        });
    </script>
</body>
</html>
