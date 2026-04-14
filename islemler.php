<?php
/**
 * Zaman Kapsülü İşlem Dosyası
 * Formdan gelen verileri yakalayıp ilişkisel tablolara güvenli bir şekilde kaydeder.
 */

// Veritabanı bağlantı dosyasını çağır
require_once 'db.php';

// Sadece POST isteği geldiğinde çalıştır
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Kullanıcıdan gelen verileri XSS (Cross-Site Scripting) saldırılarına karşı temizle
    $title       = htmlspecialchars($_POST['title']);
    $text_body   = htmlspecialchars($_POST['text_body']);
    $target_date = $_POST['target_date'];
    $category_id = $_POST['category_id'];
    
    // Oturum (Session) sistemi yapılana kadar varsayılan bir kullanıcı ID'si ata
    $sender_id = 1; 

    try {
        // Veritabanı işlemlerini bir bütün olarak başlat (Herhangi bir hata olursa tüm kayıtları geri almak için)
        $db->beginTransaction();

        // 1. Ana Tablo Kaydı: Kapsülü oluştur
        $stmt_capsule = $db->prepare("INSERT INTO CAPSULES (target_date, title, status, sender_id) VALUES (?, ?, 'Locked', ?)");
        $stmt_capsule->execute([$target_date, $title, $sender_id]);
        
        // Eklenen kapsülün ID'sini al (Diğer tablolara Foreign Key olarak göndermek için)
        $new_capsule_id = $db->lastInsertId();
        
        // 2. Alt Tablo Kaydı: Kapsülün mesaj içeriğini ekle (Üsttip-Alttip mimarisi: Text)
        $stmt_content = $db->prepare("INSERT INTO CONTENTS (capsule_id, content_type, text_body) VALUES (?, 'Text', ?)");
        $stmt_content->execute([$new_capsule_id, $text_body]);

        // 3. Ara Tablo Kaydı: Kapsülü seçilen kategoriyle eşleştir (Çoka çok ilişki)
        $stmt_category = $db->prepare("INSERT INTO CAPSULE_CATEGORY (capsule_id, category_id) VALUES (?, ?)");
        $stmt_category->execute([$new_capsule_id, $category_id]);
        
        // Tüm işlemler sorunsuz bittiyse veritabanına kalıcı olarak kaydet
        $db->commit();

        // Kullanıcıya bilgi ver ve anasayfaya yönlendir
        echo "<script>
            alert('Zaman kapsülü başarıyla mühürlendi ve sisteme kaydedildi!');
            window.location.href = 'index.php';
        </script>";
        
    } catch (PDOException $e) {
        // İşlemlerin herhangi birinde hata olursa, yarım kalan kayıtları iptal et (Rollback)
        $db->rollBack();
        
        // Hatayı ekrana yazdır (Canlıya alırken burası loglanmalıdır)
        die("Kayıt işlemi sırasında sistem hatası oluştu: " . $e->getMessage());
    }
}
?>