<?php
session_start();
require_once '../backend/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $sifre = $_POST['password'];
    $sifreli_sifre = hash('sha256', $sifre);

    $sorgu = $db->prepare("SELECT * FROM USERS WHERE email = ? AND password_hash = ?");
    $sorgu->execute([$email, $sifreli_sifre]);
    $kullanici = $sorgu->fetch();

    if ($kullanici) {
        $_SESSION['user_id'] = $kullanici['user_id'];
        header("Location: index.php");
        exit;
    } else {
        $hata = "E-posta veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap — Dijital Zaman Kapsülü</title>
    <meta name="description" content="Dijital Zaman Kapsülü sistemine giriş yapın. Anılarınızı kilitleyin, geleceğe mesaj gönderin.">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Auth Sayfası Özel Stilleri */
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* Animasyonlu Arka Plan Parçacıkları */
        .bg-orbs {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: orb-float 8s ease-in-out infinite;
        }
        .orb-1 {
            width: 400px; height: 400px;
            background: #00f2fe;
            top: -100px; left: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 350px; height: 350px;
            background: #b300ff;
            bottom: -80px; right: -80px;
            animation-delay: -3s;
        }
        .orb-3 {
            width: 200px; height: 200px;
            background: #4facfe;
            top: 50%; left: 60%;
            animation-delay: -5s;
            opacity: 0.08;
        }
        @keyframes orb-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -20px) scale(1.05); }
            66%       { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* Izgara Çizgileri */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(0, 242, 254, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 242, 254, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* Auth Kartı */
        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
        }

        /* Logo / Başlık Alanı */
        .auth-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .auth-logo .logo-text {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--accent), var(--accent-purple));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            text-transform: uppercase;
            letter-spacing: 3px;
            display: block;
            margin-bottom: 6px;
            animation: pulse-glow 3s infinite alternate;
        }
        .auth-logo .logo-icon {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 10px;
            filter: drop-shadow(0 0 12px rgba(0, 242, 254, 0.6));
        }

        /* Kart */
        .auth-card {
            background: linear-gradient(145deg, rgba(14, 17, 36, 0.95), rgba(5, 5, 10, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 42px 40px 36px;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(0, 242, 254, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        /* Kart üst kenarı parlama çizgisi */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent), transparent);
            opacity: 0.5;
        }

        .auth-card h2 {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 6px 0;
        }
        .auth-card .auth-subtitle {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        /* Input Grubu */
        .input-group {
            position: relative;
            margin-bottom: 18px;
        }
        .input-group .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
            z-index: 2;
            opacity: 0.5;
            transition: opacity 0.3s;
        }
        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(0, 0, 0, 0.35) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: var(--text-primary) !important;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            margin-bottom: 0;
            box-sizing: border-box;
            display: block;
            vertical-align: middle;
            line-height: normal;
        }
        .input-group input::placeholder {
            color: rgba(160, 174, 192, 0.6);
            font-size: 0.9rem;
        }
        .input-group input:focus {
            outline: none;
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(0, 242, 254, 0.12), 0 0 20px rgba(0, 242, 254, 0.1);
            background: rgba(0, 0, 0, 0.5) !important;
        }
        .input-group input:focus + .input-icon,
        .input-group:focus-within .input-icon {
            opacity: 1;
        }

        /* Submit Butonu */
        .auth-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--accent), var(--accent-secondary));
            color: #000;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 24px rgba(0, 242, 254, 0.35);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
            box-sizing: border-box;
        }
        .auth-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 242, 254, 0.5);
        }
        .auth-btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Hata Mesajı */
        .auth-error {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.3);
            text-align: center;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Ayırıcı */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 20px;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.06);
        }
        .auth-divider span {
            color: var(--text-secondary);
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Alt Link */
        .auth-link {
            text-align: center;
        }
        .auth-link a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            position: relative;
        }
        .auth-link a:hover {
            color: var(--accent);
        }
        .auth-link a::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 1px;
            background: var(--accent);
            transition: width 0.3s ease;
        }
        .auth-link a:hover::after {
            width: 100%;
        }

        /* Güvenlik Rozeti */
        .auth-footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(160, 174, 192, 0.4);
            font-size: 0.75rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>
</head>
<body>
    <!-- Arka Plan Efektleri -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="bg-grid"></div>

    <div class="auth-wrapper">
        <!-- Logo -->
        <div class="auth-logo">
            <span class="logo-icon">⏳</span>
            <span class="logo-text">Zaman Kapsülü</span>
        </div>

        <!-- Kart -->
        <div class="auth-card">
            <h2>Zaman Boşluğuna Bağlan</h2>
            <p class="auth-subtitle">Anılarına ve geleceğe erişmek için giriş yap</p>

            <?php if (isset($hata)): ?>
                <div class="auth-error">
                    <span>⚠</span> <?= htmlspecialchars($hata) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm">
                <div class="input-group">
                    <input type="email" name="email" id="email"
                           placeholder="E-posta adresi"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                           required autocomplete="email">
                    <span class="input-icon">✉</span>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="password"
                           placeholder="Şifre"
                           required autocomplete="current-password">
                    <span class="input-icon">🔑</span>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">
                    🚀 Sisteme Giriş Yap
                </button>
            </form>

            <div class="auth-divider">
                <span>veya</span>
            </div>

            <div class="auth-link">
                <a href="register.php">Hesabın yok mu? <strong style="color: var(--accent);">Yeni bir boyut aç →</strong></a>
            </div>
        </div>

        <div class="auth-footer">
            <span>🔒</span> SHA-256 Şifrelenmiş Güvenli Bağlantı
        </div>
    </div>

    <script>
        // Submit animasyonu
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.textContent = '⏳ Bağlanıyor...';
            btn.style.opacity = '0.7';
            btn.disabled = true;
        });
    </script>
</body>
</html>
