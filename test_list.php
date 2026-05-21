<?php
header('Content-Type: application/json; charset=utf-8');
$mysqli = new mysqli('localhost','root','','nimt_training');
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connect failed']);
    exit;
}
$res = $mysqli->query("SELECT id, contact_email, email_sent FROM bookings LIMIT 20");
$rows = [];
if ($res) {
    while ($r = $res->fetch_assoc()) $rows[] = $r;
}
echo json_encode(['success' => true, 'rows' => $rows]);
