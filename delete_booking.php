<?php
require_once __DIR__ . '/admin_auth.php';
require_admin();

// Expect JSON POST with { ids: [n,...], reason: '...' }
$input = file_get_contents('php://input');
$data = json_decode($input, true);
header('Content-Type: application/json; charset=utf-8');
if (!$data || empty($data['ids']) || !is_array($data['ids'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing ids']);
    exit;
}
$ids = array_map('intval', $data['ids']);
$ids = array_filter($ids, function($v){ return $v > 0; });
if (empty($ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid ids']);
    exit;
}
$reason = isset($data['reason']) ? trim($data['reason']) : '';

// connect to DB
$conn = new mysqli("localhost", "root", "", "nimt_training");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}
$conn->set_charset('utf8');

// Build safe IN clause
$inList = implode(',', array_map('intval', $ids));
$sql = "DELETE FROM bookings WHERE id IN ($inList)";
if (!$conn->query($sql)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Delete failed']);
    $conn->close();
    exit;
}

$affected = $conn->affected_rows;

// Log deletions to file
$logLine = sprintf("%s\t%s\t%s\t%s\n", date('c'), session_id(), implode(',', $ids), str_replace(["\r", "\n", "\t"], [' ', ' ', ' '], $reason));
file_put_contents(__DIR__ . '/deletion_log.tsv', $logLine, FILE_APPEND | LOCK_EX);

if ($affected > 0) {
    echo json_encode(['success' => true, 'deleted' => $affected]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Not found']);
}

$conn->close();
