<?php
/**
 * Kullanıcı Kayıt Dosyası
 * Yeni kullanıcıları SHA-256 şifreleme ile veritabanına ekler.
 */
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once '../backend/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad    = htmlspecialchars($_POST['first_name']);
    $soyad = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $sifre = $_POST['password'];
    $sifreli_sifre = hash('sha256', $sifre);

    try {
        $kontrol = $db->prepare("SELECT * FROM USERS WHERE email = ?");
        $kontrol->execute([$email]);

        if ($kontrol->rowCount() > 0) {
            $hata = "Bu e-posta adresi zaten sisteme kayıtlı!";
        } else {
            $sorgu = $db->prepare("INSERT INTO USERS (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
            $sorgu->execute([$ad, $soyad, $email, $sifreli_sifre]);
            $basari = true;
        }
    } catch (PDOException $e) {
        $hata = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol — Dijital Zaman Kapsülü</title>
    <meta name="description" content="Dijital Zaman Kapsülü sistemine kayıt olun. Anılarınızı kilitleyin, geleceğe mesaj gönderin.">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

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
            background: #b300ff;
            top: -100px; right: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 350px; height: 350px;
            background: #00f2fe;
            bottom: -80px; left: -80px;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 200px; height: 200px;
            background: #4facfe;
            top: 40%; left: 10%;
            animation-delay: -2s;
            opacity: 0.08;
        }
        @keyframes orb-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(-30px, 20px) scale(1.05); }
            66%       { transform: translate(20px, -30px) scale(0.95); }
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(179, 0, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(179, 0, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
        }

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
            filter: drop-shadow(0 0 12px rgba(179, 0, 255, 0.6));
        }

        .auth-card {
            background: linear-gradient(145deg, rgba(14, 17, 36, 0.95), rgba(5, 5, 10, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 42px 40px 36px;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(179, 0, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent-purple), transparent);
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

        /* Ad / Soyad yan yana */
        .input-row {
            display: flex;
            gap: 12px;
        }
        .input-row .input-group {
            flex: 1;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }
        .input-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.95rem;
            pointer-events: none;
            z-index: 2;
            opacity: 0.45;
            transition: opacity 0.3s;
        }
        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 42px;
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
            font-size: 0.88rem;
        }
        .input-group input:focus {
            outline: none;
            border-color: var(--accent-purple) !important;
            box-shadow: 0 0 0 3px rgba(179, 0, 255, 0.12), 0 0 20px rgba(179, 0, 255, 0.1);
            background: rgba(0, 0, 0, 0.5) !important;
        }
        .input-group:focus-within .input-icon {
            opacity: 1;
        }

        .auth-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--accent-purple), #7c3aed);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 24px rgba(179, 0, 255, 0.35);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
            box-sizing: border-box;
        }
        .auth-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(179, 0, 255, 0.5);
            background: linear-gradient(135deg, #c026d3, var(--accent-purple));
        }
        .auth-btn:active {
            transform: translateY(0) scale(0.98);
        }

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

        .auth-success {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            text-align: center;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

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

        /* Şifre gücü göstergesi */
        .password-strength {
            height: 3px;
            border-radius: 2px;
            margin-top: 6px;
            background: rgba(255,255,255,0.06);
            overflow: hidden;
            transition: all 0.3s;
        }
        .password-strength-bar {
            height: 100%;
            border-radius: 2px;
            width: 0%;
            transition: all 0.4s ease;
        }
    </style>
</head>
<body>
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="bg-grid"></div>

    <div class="auth-wrapper">
        <div class="auth-logo">
            <span class="logo-icon">🌌</span>
            <span class="logo-text">Zaman Kapsülü</span>
        </div>

        <div class="auth-card">
            <h2>Sisteme Kayıt Ol</h2>
            <p class="auth-subtitle">Zaman boşluğuna ilk adımını at</p>

            <?php if (isset($hata)): ?>
                <div class="auth-error">
                    <span>⚠</span> <?= htmlspecialchars($hata) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($basari) && $basari): ?>
                <div class="auth-success">
                    <span>✓</span> Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...
                </div>
                <script>setTimeout(() => window.location.href = 'login.php', 1500);</script>
            <?php else: ?>

            <form action="register.php" method="POST" id="registerForm">
                <div class="input-row">
                    <div class="input-group">
                        <input type="text" name="first_name" id="firstName"
                               placeholder="Adın"
                               value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>"
                               required autocomplete="given-name">
                        <span class="input-icon">👤</span>
                    </div>
                    <div class="input-group">
                        <input type="text" name="last_name" id="lastName"
                               placeholder="Soyadın"
                               value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>"
                               required autocomplete="family-name">
                        <span class="input-icon">👤</span>
                    </div>
                </div>

                <div class="input-group">
                    <input type="email" name="email" id="email"
                           placeholder="E-posta adresi"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                           required autocomplete="email">
                    <span class="input-icon">✉</span>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="password"
                           placeholder="Şifre (en az 6 karakter)"
                           required autocomplete="new-password">
                    <span class="input-icon">🔐</span>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">
                    🌌 Kaydı Tamamla
                </button>
            </form>

            <?php endif; ?>

            <div class="auth-divider">
                <span>veya</span>
            </div>

            <div class="auth-link">
                <a href="login.php">Zaten kayıtlı mısın? <strong style="color: var(--accent);">Buradan giriş yap →</strong></a>
            </div>
        </div>

        <div class="auth-footer">
            <span>🔒</span> SHA-256 Şifrelenmiş Güvenli Kayıt
        </div>
    </div>

    <script>
        // Şifre gücü göstergesi
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        if (passwordInput && strengthBar) {
            passwordInput.addEventListener('input', function() {
                const val = this.value;
                let strength = 0;
                if (val.length >= 6)  strength += 25;
                if (val.length >= 10) strength += 25;
                if (/[A-Z]/.test(val)) strength += 25;
                if (/[0-9!@#$%^&*]/.test(val)) strength += 25;

                strengthBar.style.width = strength + '%';
                if (strength <= 25)      { strengthBar.style.background = '#ef4444'; }
                else if (strength <= 50) { strengthBar.style.background = '#f59e0b'; }
                else if (strength <= 75) { strengthBar.style.background = '#00f2fe'; }
                else                     { strengthBar.style.background = '#34d399'; }
            });
        }

        // Submit animasyonu
        const form = document.getElementById('registerForm');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.textContent = '⏳ Kayıt oluşturuluyor...';
                btn.style.opacity = '0.7';
                btn.disabled = true;
            });
        }
    </script>
</body>
</html>