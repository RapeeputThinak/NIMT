<?php
require_once __DIR__ . '/admin_auth.php';
require_admin();
// รับคำขอ POST แบบ JSON หรือ form-data เพื่อบันทึกค่า defaults ของเทมเพลต
header('Content-Type: application/json; charset=utf-8');

$data = [];
// พยายามอ่าน JSON raw body
$raw = file_get_contents('php://input');
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}

// หากไม่มี JSON ให้อ่านจาก POST form fields
if (empty($data) && !empty($_POST)) {
    foreach ($_POST as $k => $v) {
        $data[$k] = $v;
    }
}

if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$file = __DIR__ . '/email_defaults.json';
if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to write file']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Saved', 'file' => basename($file)]);
