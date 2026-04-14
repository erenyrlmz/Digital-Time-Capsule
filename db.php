<?php
/**
 * Veritabanı Bağlantı Dosyası
 * PDO (PHP Data Objects) kullanılarak hazırlanmış güvenli bağlantı altyapısı.
 */

// 1. Sunucu ve Veritabanı Ayarları
$host     = 'localhost';
$db_name  = 'zaman_kapsulu';
$username = 'root';
$password = '';
$charset  = 'utf8mb4';

// 2. Data Source Name (DSN) Hazırlığı
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// 3. PDO Güvenlik ve Hata Yönetimi Ayarları
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Hataları Exception olarak fırlatır
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Verileri varsayılan olarak ilişkisel dizi (array) formatında getirir
    PDO::ATTR_EMULATE_PREPARES   => false,                  // SQL Injection saldırılarına karşı koruma sağlar
];

// 4. Bağlantı İşlemi
try {
    $db = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Bağlantı başarısız olursa çalışmayı durdur ve hatayı ekrana bas
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>