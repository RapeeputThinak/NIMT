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
$res = $mysqli->query("UPDATE bookings SET email_sent = 1 WHERE id = " . $id);
if ($res) {
    echo json_encode(['success' => true, 'id' => $id]);
} else {
    echo json_encode(['success' => false, 'error' => $mysqli->error]);
}
