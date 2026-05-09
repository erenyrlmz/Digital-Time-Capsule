<?php
/**
 * get_capsule_content.php
 * Kapsül içeriğini JSON olarak döndürür.
 * Gizlilik kontrolü: Gizli kapsüller sadece sahip tarafından görülebilir.
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Oturum açmanız gerekiyor.']);
    exit;
}

require_once '../backend/db.php';

$capsule_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$capsule_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz kapsül ID.']);
    exit;
}

// Kapsülü getir
$cap_stmt = $db->prepare("SELECT * FROM CAPSULES WHERE capsule_id = ?");
$cap_stmt->execute([$capsule_id]);
$capsule = $cap_stmt->fetch(PDO::FETCH_ASSOC);

if (!$capsule) {
    http_response_code(404);
    echo json_encode(['error' => 'Kapsül bulunamadı.']);
    exit;
}

// Gizlilik kontrolü
// - Kapsül açılmamışsa (Locked) → hiç kimse göremez
// - Kapsül gizliyse (is_public=0) → sadece sahibi görebilir
if ($capsule['status'] === 'Locked') {
    http_response_code(403);
    echo json_encode(['error' => 'Kapsül henüz açılmadı.']);
    exit;
}

$is_owner = ((int)$capsule['sender_id'] === (int)$_SESSION['user_id']);
$is_public = isset($capsule['is_public']) ? (int)$capsule['is_public'] : 0;

if (!$is_owner && !$is_public) {
    http_response_code(403);
    echo json_encode(['error' => 'Bu kapsül gizlidir. Sadece sahibi görebilir.']);
    exit;
}

// İçerikleri getir
$con_stmt = $db->prepare("SELECT content_type, text_body, file_url, file_extension FROM CONTENTS WHERE capsule_id = ?");
$con_stmt->execute([$capsule_id]);
$contents_raw = $con_stmt->fetchAll(PDO::FETCH_ASSOC);

$contents = [];
foreach ($contents_raw as $c) {
    if ($c['content_type'] === 'Text') {
        $contents[] = ['type' => 'Text', 'text' => $c['text_body']];
    } elseif ($c['content_type'] === 'Media') {
        $contents[] = [
            'type' => 'Media',
            'url'  => $c['file_url'],
            'ext'  => $c['file_extension']
        ];
    }
}

// Alıcıları getir
$rec_stmt = $db->prepare("SELECT full_name FROM RECIPIENTS WHERE capsule_id = ?");
$rec_stmt->execute([$capsule_id]);
$recipients = array_column($rec_stmt->fetchAll(PDO::FETCH_ASSOC), 'full_name');

echo json_encode([
    'capsule_id' => $capsule_id,
    'title'      => $capsule['title'],
    'target_date'=> $capsule['target_date'],
    'is_public'  => $is_public,
    'contents'   => $contents,
    'recipients' => $recipients
], JSON_UNESCAPED_UNICODE);
