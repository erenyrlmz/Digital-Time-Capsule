<?php
session_start();
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
    $is_public   = isset($_POST['is_public']) && $_POST['is_public'] == '1' ? 1 : 0;
    
    if (!isset($_SESSION['user_id'])) {
        die("Yetkisiz işlem! Lütfen giriş yapın.");
    }
    // Oturumdan gelen ID'yi al
    $sender_id = $_SESSION['user_id']; 

    try {
        // Veritabanı işlemlerini bir bütün olarak başlat (Herhangi bir hata olursa tüm kayıtları geri almak için)
        $db->beginTransaction();

        // 1. Ana Tablo Kaydı: Kapsülü oluştur
        $stmt_capsule = $db->prepare("INSERT INTO CAPSULES (target_date, title, status, sender_id, is_public) VALUES (?, ?, 'Locked', ?, ?)");
        $stmt_capsule->execute([$target_date, $title, $sender_id, $is_public]);
        
        // Eklenen kapsülün ID'sini al (Diğer tablolara Foreign Key olarak göndermek için)
        $new_capsule_id = $db->lastInsertId();
        
        // 2. Alt Tablo Kaydı: Kapsülün mesaj içeriğini ekle (Üsttip-Alttip mimarisi: Text)
        $stmt_content = $db->prepare("INSERT INTO CONTENTS (capsule_id, content_type, text_body) VALUES (?, 'Text', ?)");
        $stmt_content->execute([$new_capsule_id, $text_body]);

        // MEDYA DOSYASI YÜKLENMİŞSE İŞLE (İsteğe bağlı özellik)
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
            $filename = $_FILES['media_file']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed)) {
                $new_filename = uniqid('capsule_') . '.' . $file_ext;
                $upload_dir = '../frontend/uploads/';
                
                // Klasör yoksa oluştur
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Dosyayı taşı ve DB'ye Media tipiyle kaydet (Single Table Inheritance)
                if (move_uploaded_file($_FILES['media_file']['tmp_name'], $upload_dir . $new_filename)) {
                    $stmt_media = $db->prepare("INSERT INTO CONTENTS (capsule_id, content_type, file_url, file_extension) VALUES (?, 'Media', ?, ?)");
                    $stmt_media->execute([$new_capsule_id, 'uploads/' . $new_filename, $file_ext]);
                }
            }
        }

        // 3. Ara Tablo Kaydı: Kapsülü seçilen kategoriyle eşleştir (Çoka çok ilişki)
        $stmt_category = $db->prepare("INSERT INTO CAPSULE_CATEGORY (capsule_id, category_id) VALUES (?, ?)");
        $stmt_category->execute([$new_capsule_id, $category_id]);

        // 4. Alıcı Tablosu Kaydı (Eğer formda doldurulduysa)
        $recipient_name = isset($_POST['recipient_name']) ? trim($_POST['recipient_name']) : '';
        $recipient_email = isset($_POST['recipient_email']) ? trim($_POST['recipient_email']) : '';
        
        if (!empty($recipient_name) && !empty($recipient_email)) {
            $stmt_recipient = $db->prepare("INSERT INTO RECIPIENTS (full_name, email, capsule_id) VALUES (?, ?, ?)");
            $stmt_recipient->execute([htmlspecialchars($recipient_name), htmlspecialchars($recipient_email), $new_capsule_id]);
        }
        
        // Tüm işlemler sorunsuz bittiyse veritabanına kalıcı olarak kaydet
        $db->commit();

        // Kullanıcıya bilgi ver ve anasayfaya yönlendir (Roket animasyonu için success parametresiyle)
        header("Location: ../frontend/index.php?success=1");
        exit();
        
    } catch (PDOException $e) {
        // İşlemlerin herhangi birinde hata olursa, yarım kalan kayıtları iptal et (Rollback)
        $db->rollBack();
        
        // Hatayı ekrana yazdır (Canlıya alırken burası loglanmalıdır)
        die("Kayıt işlemi sırasında sistem hatası oluştu: " . $e->getMessage());
    }
}
?>