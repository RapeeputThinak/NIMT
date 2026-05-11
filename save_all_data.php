<?php
session_start();

// นำเข้า PHPMailer (จากโฟลเดอร์ PHPMailer)
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// เชื่อมต่อฐานข้อมูลและตั้งค่าภาษาไทย (สำคัญมากเพื่อให้ชื่อหน่วยงานภาษาไทยขึ้นถูกต้อง)
$conn = new mysqli("localhost", "root", "", "nimt_training");
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ตรวจสอบว่ามีข้อมูลจากหน้าแรกใน Session จริงไหม
    if (!isset($_SESSION['data_page1'])) {
        echo "<script>alert('ไม่พบข้อมูลจากหน้าแรก กรุณาเริ่มกรอกใหม่'); window.location.href='index.html';</script>";
        exit();
    }

    // 1. ดึงข้อมูลหน้าแรกจาก Session
    $p1 = $_SESSION['data_page1'];

    // 2. เตรียมข้อมูลจากหน้าแรก
    $company_name = $conn->real_escape_string($p1['company_name'] ?? '');
    $billing_address = $conn->real_escape_string($p1['billing_address'] ?? '');
    $tax_id = $conn->real_escape_string($p1['tax_id'] ?? '');
    $branch_code = $conn->real_escape_string($p1['branch_code'] ?? '');
    $course_title = $conn->real_escape_string($p1['course_title'] ?? '');
    $instructor_name = $conn->real_escape_string($p1['instructor_name'] ?? '');
    $participant_count = intval($p1['participant_count'] ?? 0);
    $training_date = $conn->real_escape_string($p1['training_date'] ?? '');
    $location = $conn->real_escape_string($p1['location'] ?? '');
    $contact_name = $conn->real_escape_string($p1['contact_name'] ?? '');
    $contact_position = $conn->real_escape_string($p1['contact_position'] ?? '');
    $contact_phone = $conn->real_escape_string($p1['contact_phone'] ?? '');
    $contact_email = $conn->real_escape_string($p1['contact_email'] ?? '');

    // 3. ดึงข้อมูลหน้าสองจาก $_POST (หน้าปัจจุบัน)
    $arrange_service = $conn->real_escape_string($_POST['arrange'] ?? '');
    $site_vehicle = isset($_POST['site_vehicle']) ? 'ใช่' : 'ไม่';
    $site_hotel = isset($_POST['site_hotel']) ? 'ใช่' : 'ไม่';
    $nimt_room = isset($_POST['nimt_room']) ? 'ใช่' : 'ไม่';
    $nimt_food = isset($_POST['nimt_food']) ? 'ใช่' : 'ไม่';
    $consent = isset($_POST['consent']) ? 'ยินยอม' : 'ไม่ยินยอม';

    // 4. คำนวณปีงบประมาณจากวันที่อบรม
    if (!empty($training_date) && $training_date !== '0000-00-00') {
        try {
            $training_date_obj = new DateTime($training_date);
            $year = (int)$training_date_obj->format('Y');
            $month = (int)$training_date_obj->format('n');
            if ($month >= 10) {
                $fiscal_year = $year . '-' . ($year + 1);
            } else {
                $fiscal_year = ($year - 1) . '-' . $year;
            }
        } catch (Exception $e) {
            $fiscal_year = '';
        }
    } else {
        $fiscal_year = '';
    }

    // 5. บันทึกครั้งเดียวรวบยอด
    $sql = "INSERT INTO bookings (
                company_name,
                billing_address,
                tax_id,
                branch_code,
                course_title,
                instructor_name,
                participant_count,
                training_date,
                location,
                contact_name,
                contact_position,
                contact_phone,
                contact_email,
                arrange_service,
                site_vehicle,
                site_hotel,
                nimt_room,
                nimt_food,
                consent,
                fiscal_year
            ) VALUES (
                '$company_name',
                '$billing_address',
                '$tax_id',
                '$branch_code',
                '$course_title',
                '$instructor_name',
                $participant_count,
                '$training_date',
                '$location',
                '$contact_name',
                '$contact_position',
                '$contact_phone',
                '$contact_email',
                '$arrange_service',
                '$site_vehicle',
                '$site_hotel',
                '$nimt_room',
                '$nimt_food',
                '$consent',
                '$fiscal_year'
            )";

    if ($conn->query($sql) === TRUE) {
        // ส่งอีเมลด้วย PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // ตั้งค่า SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ponrapeeput@gmail.com';
            $mail->Password   = 'dbiu lkpt syli nxxs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // ตั้งผู้ส่งและผู้รับ
            $mail->setFrom('ponrapeeput@gmail.com', 'NIMT Training Service');
            $mail->addAddress($contact_email, $contact_name);

            // เนื้อหาอีเมล
            $mail->isHTML(true);
            $mail->Subject = 'ยืนยันการส่งคำขอใช้บริการฝึกอบรมนอกสถานที่ - ' . $company_name;
            
            $services_text = "<ul>";
            $services_text .= "<li><strong>ความต้องการขอใช้บริการ:</strong> $arrange_service</li>";
            if ($arrange_service === 'ต้องการขอใช้บริการ') {
                $services_text .= "<li><strong>พาหนะ:</strong> $site_vehicle</li>";
                $services_text .= "<li><strong>ที่พัก:</strong> $site_hotel</li>";
                $services_text .= "<li><strong>ห้องอบรม และอุปกรณ์:</strong> $nimt_room</li>";
                $services_text .= "<li><strong>อาหารและเครื่องดื่ม:</strong> $nimt_food</li>";
            }
            $services_text .= "</ul>";

            $mail->Body = "
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>
                        body { font-family: 'Sarabun', Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; }
                        .header { color: #003d7a; border-bottom: 2px solid #003d7a; padding-bottom: 10px; }
                        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; }
                        .footer { font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2 class='header'>เรียน คุณ $contact_name</h2>
                        <p>สถาบันมาตรวิทยาแห่งชาติ (NIMT) ได้รับข้อมูลคำขอใช้บริการฝึกอบรมนอกสถานที่ของท่านแล้ว</p>
                        
                        <div class='content'>
                            <h3>รายละเอียดการสมัคร:</h3>
                            <ul>
                                <li><strong>หน่วยงาน:</strong> $company_name</li>
                                <li><strong>หลักสูตร:</strong> $course_title</li>
                                <li><strong>วิทยากร:</strong> $instructor_name</li>
                                <li><strong>จำนวนผู้เข้าอบรม:</strong> $participant_count คน</li>
                                <li><strong>วันที่ต้องการอบรม:</strong> " . date('d/m/Y', strtotime($training_date)) . "</li>
                                <li><strong>สถานที่อบรม:</strong> $location</li>
                                <li><strong>ชื่อผู้ติดต่อ:</strong> $contact_name</li>
                                <li><strong>เบอร์โทรศัพท์:</strong> $contact_phone</li>
                            </ul>
                            <h3>บริการที่ขอใช้:</h3>
                            $services_text
                        </div>

                        <p style='color: #d32f2f;'><strong>* เจ้าหน้าที่จะทำการตรวจสอบข้อมูลและติดต่อกลับท่านเพื่อยืนยันอีกครั้ง</strong></p>

                        <div class='footer'>
                            <p>สถาบันมาตรวิทยาแห่งชาติ (National Institute of Metrology Thailand)</p>
                            <p>โทรศัพท์: 0 2026 5400 ต่อ 8301-3 | อีเมล: training@nimt.or.th</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            // ส่งอีเมล
            $mail->send();
            
            session_unset();
            echo "<script>alert('บันทึกข้อมูลสำเร็จ! อีเมลยืนยันได้ส่งไปยัง $contact_email เรียบร้อยแล้ว'); window.location.href='admin.php';</script>";
            
        } catch (Exception $e) {
            session_unset();
            echo "<script>alert('บันทึกข้อมูลสำเร็จ แต่เกิดข้อผิดพลาดในการส่งอีเมล: " . addslashes($mail->ErrorInfo) . "'); window.location.href='admin.php';</script>";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>