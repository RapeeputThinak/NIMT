<?php
// ไฟล์ step1_session.php
session_start(); // เปิดใช้งาน session 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // นำข้อมูลจากฟอร์มหน้าแรกทั้งหมดเก็บลงใน session [cite: 14, 15]
    $_SESSION['data_page1'] = $_POST; 
    
    // ส่งผู้ใช้ไปหน้าสอง
    header("Location: index2.html");
    exit();
}
?>