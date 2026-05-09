<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum açmadınız.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $capsule_id = intval($_POST['capsule_id']);
    $user_id = $_SESSION['user_id'];
    
    try {
        $db->beginTransaction();
        
        // Önce bağlı tabloları sil (CASCADE yoksa diye garantiye alalım)
        $db->prepare("DELETE FROM OPENING_LOGS WHERE capsule_id = ?")->execute([$capsule_id]);
        $db->prepare("DELETE FROM RECIPIENTS WHERE capsule_id = ?")->execute([$capsule_id]);
        $db->prepare("DELETE FROM CONTENTS WHERE capsule_id = ?")->execute([$capsule_id]);
        $db->prepare("DELETE FROM CAPSULE_CATEGORY WHERE capsule_id = ?")->execute([$capsule_id]);
        
        $sil = $db->prepare("DELETE FROM CAPSULES WHERE capsule_id = ? AND sender_id = ?");
        $sil->execute([$capsule_id, $user_id]);
        
        if ($sil->rowCount() > 0) {
            $db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Kapsül zaman çizelgesinden silindi.']);
        } else {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Kapsül bulunamadı veya yetkiniz yok.']);
        }
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Silme sırasında hata oluştu.']);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'open') {
    $capsule_id = intval($_POST['capsule_id']);
    $user_id = $_SESSION['user_id'];
    
    try {
        $ac = $db->prepare("UPDATE CAPSULES SET status = 'Opened' WHERE capsule_id = ? AND sender_id = ? AND status = 'Unlocked'");
        $ac->execute([$capsule_id, $user_id]);
        
        if ($ac->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Kapsül açıldı ve arşive taşındı.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Kapsül bulunamadı veya açılamaz durumda.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
    }
}
?>
