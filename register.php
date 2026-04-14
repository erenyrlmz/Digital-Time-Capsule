<?php
/**
 * Kullanıcı Kayıt Dosyası
 * Yeni kullanıcıları SHA-256 şifreleme ile veritabanına ekler.
 */

// Veritabanı bağlantısını dahil et
require_once 'db.php';

// Form gönderildiğinde çalışır
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Formdan gelen verileri değişkenlere ata
    $ad    = htmlspecialchars($_POST['first_name']);
    $soyad = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $sifre = $_POST['password'];

    // 1. GÜVENLİK ADIMI: Şifreyi SHA-256 ile özetle (Hocanın istediği kural)
    // password_hash fonksiyonu daha moderndir ama ödevde özellikle SHA istendiği için bu şekilde kullanıyoruz.
    $sifreli_sifre = hash('sha256', $sifre);

    try {
        // E-posta adresinin daha önce alınıp alınmadığını kontrol et
        $kontrol = $db->prepare("SELECT * FROM USERS WHERE email = ?");
        $kontrol->execute([$email]);

        if ($kontrol->rowCount() > 0) {
            die("Bu e-posta adresi zaten sisteme kayıtlı!");
        }

        // 2. ADIM: Kullanıcıyı USERS tablosuna ekle
        $sorgu = $db->prepare("INSERT INTO USERS (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
        $sorgu->execute([$ad, $soyad, $email, $sifreli_sifre]);

        echo "<script>alert('Kaydınız başarıyla oluşturuldu! Giriş yapabilirsiniz.'); window.location.href='index.php';</script>";

    } catch (PDOException $e) {
        die("Kayıt sırasında bir hata oluştu: " . $e->getMessage());
    }
}
?>