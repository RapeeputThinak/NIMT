<?php
header('Content-Type: application/json; charset=utf-8');
$mysqli = new mysqli('localhost','root','','nimt_training');
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connect failed']);
    exit;
}
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'invalid id']);
    exit;
}
$res = $mysqli->query("SELECT id, contact_email, email_sent FROM bookings WHERE id = " . $id . " LIMIT 1");
if ($res && $res->num_rows) {
    $row = $res->fetch_assoc();
    echo json_encode(['success' => true, 'row' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'not found']);
}
