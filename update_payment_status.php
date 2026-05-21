<?php
// ตรวจสอบสิทธิ์ admin
require_once __DIR__ . '/admin_auth.php';
require_admin();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "nimt_training");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}
$conn->set_charset("utf8");

// รับข้อมูลจาก request
$input = json_decode(file_get_contents('php://input'), true);
$bookingId = intval($input['id'] ?? 0);
$newStatus = $input['status'] ?? '';

// ตรวจสอบค่า
if (!$bookingId || !in_array($newStatus, ['paid', 'pending'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// อัปเดตสถานะ
$query = "UPDATE bookings SET payment_status = '" . $conn->real_escape_string($newStatus) . "' WHERE id = $bookingId";
if ($conn->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Payment status updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>
