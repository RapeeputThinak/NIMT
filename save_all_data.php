<?php
session_start();

// นำเข้าไลบรารี PHPMailer สำหรับส่งอีเมล
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "nimt_training");

if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. ดึงข้อมูลจาก Session (ข้อมูลหน้าที่ 1 ที่ส่งมาจาก step1_session.php)
    $p1 = $_SESSION['data_page1'] ?? [];

    if (empty($p1)) {
        echo "<script>alert('ไม่พบข้อมูลจากหน้าแรก กรุณาเริ่มกรอกข้อมูลใหม่'); window.location.href='metrology_form.html';</script>";
        exit();
    }

    // ข้อมูลพื้นฐานที่ใช้ร่วมกันทุกหมวดหมู่
    $company_name      = $conn->real_escape_string($p1['company_name'] ?? '');
    $billing_address   = $conn->real_escape_string($p1['billing_address'] ?? '');
    $tax_id            = $conn->real_escape_string($p1['tax_id'] ?? '');
    $branch_code       = $conn->real_escape_string($p1['branch_code'] ?? '');
    $course_title      = $conn->real_escape_string($p1['course_title'] ?? '');
    
    // จัดการชื่อวิทยากร (รองรับทั้งชื่อตัวแปรจากฟอร์ม Inhouse และ Metrology ทั้งไทย/อังกฤษ)
    $instructor_name   = $conn->real_escape_string($p1['instructor_name'] ?? $p1['speaker_name'] ?? $p1['lecturer'] ?? '');
    
    $participant_count = intval($p1['participant_count'] ?? 0);
    $training_date     = $conn->real_escape_string($p1['training_date'] ?? '');
    $location          = $conn->real_escape_string($p1['location'] ?? '');
    $contact_name      = $conn->real_escape_string($p1['contact_name'] ?? '');
    $contact_position  = $conn->real_escape_string($p1['contact_position'] ?? '');
    $contact_phone     = $conn->real_escape_string($p1['contact_phone'] ?? '');
    $contact_email     = $conn->real_escape_string($p1['contact_email'] ?? '');

    // ข้อมูลเพิ่มเติมเฉพาะของ Metrology (ถ้ามี)
    $lab_tool          = $conn->real_escape_string($p1['lab_tool'] ?? '');
    $request_date      = $conn->real_escape_string($p1['request_date'] ?? '');
    $reference_no      = $conn->real_escape_string($p1['reference_no'] ?? $p1['ref_no'] ?? '');

    // 2. ข้อมูลจาก POST (ข้อมูลหน้าที่ 2 ที่เพิ่งส่งมาจากหน้าล่าสุด)
    $arrange_service   = $conn->real_escape_string($_POST['arrange_service'] ?? '');
    $site_vehicle      = isset($_POST['site_vehicle']) ? 'ใช่' : 'ไม่';
    $site_hotel        = isset($_POST['site_hotel']) ? 'ใช่' : 'ไม่';
    $nimt_room         = isset($_POST['nimt_room']) ? 'ใช่' : 'ไม่';
    $nimt_food         = isset($_POST['nimt_food']) ? 'ใช่' : 'ไม่';
    
    // จัดการการยินยอม (Privacy Consent)
    $consent           = (isset($_POST['privacy_consent']) || isset($_POST['consent'])) ? 'ยินยอม' : 'ไม่ยินยอม';
    
    // หมวดหมู่ (ดึงจาก POST หรือ Session)
    $category          = $conn->real_escape_string($_POST['category'] ?? $p1['category'] ?? 'Metrology-TH');

    // 3. คำนวณปีงบประมาณ (Fiscal Year) อัตโนมัติจากวันที่อบรม
    $fiscal_year = '';
    if (!empty($training_date) && $training_date !== '0000-00-00') {
        $date_parts = explode('-', $training_date);
        if(count($date_parts) == 3) {
            $year = (int)$date_parts[0];
            $month = (int)$date_parts[1];
            // ปีงบประมาณไทยเริ่มเดือนตุลาคม (10)
            $fiscal_year = ($month >= 10) ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
        }
    }

    // 4. บันทึกข้อมูลลงตาราง bookings
    $sql = "INSERT INTO bookings (
                company_name, billing_address, tax_id, branch_code,
                course_title, instructor_name, lab_tool, participant_count, training_date,
                location, contact_name, contact_position, contact_phone,
                contact_email, request_date, reference_no,
                arrange_service, site_vehicle, site_hotel,
                nimt_room, nimt_food, consent, category, fiscal_year
            ) VALUES (
                '$company_name', '$billing_address', '$tax_id', '$branch_code',
                '$course_title', '$instructor_name', '$lab_tool', $participant_count, '$training_date',
                '$location', '$contact_name', '$contact_position', '$contact_phone',
                '$contact_email', '$request_date', '$reference_no',
                '$arrange_service', '$site_vehicle', '$site_hotel',
                '$nimt_room', '$nimt_food', '$consent', '$category', '$fiscal_year'
            )";

    if ($conn->query($sql) === TRUE) {
        // --- 5. ส่วนการส่ง Email ยืนยันไปยังผู้สมัคร ---
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ponrapeeput@gmail.com';
            $mail->Password   = 'dbiu lkpt syli nxxs'; // App Password ของ Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('ponrapeeput@gmail.com', 'NIMT Training Service');
            $mail->addAddress($contact_email, $contact_name);

            $mail->isHTML(true);
            $mail->Subject = 'ยืนยันการรับเรื่องคำขอใช้บริการ - ' . $company_name;
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                    <h2 style='color: #1e3a8a;'>ได้รับข้อมูลคำขอใช้บริการเรียบร้อยแล้ว</h2>
                    <p>เรียนคุณ <b>$contact_name</b>,</p>
                    <p>สถาบันมาตรวิทยาแห่งชาติได้รับข้อมูลการสมัครขอใช้บริการ <b>$course_title</b> ของท่านเรียบร้อยแล้ว</p>
                    <hr>
                    <p>ขณะนี้ข้อมูลของท่านอยู่ระหว่างการตรวจสอบโดยเจ้าหน้าที่ หากดำเนินการเรียบร้อยแล้วจะมีการแจ้งรายละเอียดเพิ่มเติมกลับไปยังอีเมลนี้อีกครั้ง</p>
                    <br>
                    <p>ขอบคุณที่ใช้บริการสถาบันมาตรวิทยาแห่งชาติ</p>
                </div>
            "; 

            $mail->send();
            
            // ล้าง Session ทั้งหมดหลังจากบันทึกและส่งเมลสำเร็จ
            session_destroy();
            echo "<script>alert('ส่งคำขอเรียบร้อยแล้ว! เจ้าหน้าที่จะติดต่อกลับทางอีเมล'); window.location.href='admin.php';</script>";
        } catch (Exception $e) {
            // กรณีบันทึกสำเร็จแต่ส่งเมลล้มเหลว
            session_destroy();
            echo "<script>alert('บันทึกข้อมูลสำเร็จ แต่เกิดข้อผิดพลาดในการส่งอีเมลยืนยัน'); window.location.href='admin.php';</script>";
        }
    } else {
        // แจ้งข้อผิดพลาดให้ชัดเจนขึ้น
        echo "<h3>เกิดข้อผิดพลาดในการบันทึกข้อมูล:</h3>" . $conn->error;
        echo "<p><strong>วิธีแก้:</strong> คุณต้องเพิ่มคอลัมน์ที่ขาดหายไปในฐานข้อมูล โดยรันคำสั่ง SQL นี้ใน phpMyAdmin:</p>";
        echo "<pre style='background:#eee; padding:10px;'>
ALTER TABLE bookings 
ADD COLUMN lab_tool VARCHAR(255) AFTER instructor_name,
ADD COLUMN request_date DATE AFTER contact_email,
ADD COLUMN reference_no VARCHAR(100) AFTER request_date,
ADD COLUMN fiscal_year VARCHAR(20) AFTER category;</pre>";
    }
}
$conn->close();
?>