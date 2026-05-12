<?php
session_start();

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "nimt_training");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เก็บข้อมูล session สำหรับ step ถัดไป
    $_SESSION['data_page1'] = $_POST;

    // รับค่าจากฟอร์ม
    $company_name      = $conn->real_escape_string($_POST['company_name'] ?? '');
    $billing_address   = $conn->real_escape_string($_POST['billing_address'] ?? '');
    $tax_id            = $conn->real_escape_string($_POST['tax_id'] ?? '');
    $branch_code       = $conn->real_escape_string($_POST['branch_code'] ?? '');
    $course_title      = $conn->real_escape_string($_POST['course_title'] ?? '');
    $instructor_name   = $conn->real_escape_string($_POST['instructor_name'] ?? '');
    $participant_count = intval($_POST['participant_count'] ?? 0);
    $location          = $conn->real_escape_string($_POST['location'] ?? '');
    $contact_name      = $conn->real_escape_string($_POST['contact_name'] ?? '');
    $contact_position  = $conn->real_escape_string($_POST['contact_position'] ?? '');
    $contact_phone     = $conn->real_escape_string($_POST['contact_phone'] ?? '');
    $contact_email     = $conn->real_escape_string($_POST['contact_email'] ?? '');
    $arrange_service   = $conn->real_escape_string($_POST['arrange_service'] ?? '');
    $site_vehicle      = isset($_POST['site_vehicle']) ? 'ใช่' : 'ไม่';
    $site_hotel        = isset($_POST['site_hotel']) ? 'ใช่' : 'ไม่';
    $nimt_room         = isset($_POST['nimt_room']) ? 'ใช่' : 'ไม่';
    $nimt_food         = isset($_POST['nimt_food']) ? 'ใช่' : 'ไม่';
    $consent           = isset($_POST['privacy_consent']) ? 'ยินยอม' : 'ไม่ยินยอม';
    $category          = $conn->real_escape_string($_POST['category'] ?? '');

    // แปลงวันที่จาก d/m/Y เป็น Y-m-d
    $training_date = $_POST['training_date'] ?? '';
    if (strpos($training_date, '/') !== false) {
        $dateObj = DateTime::createFromFormat('d/m/Y', $training_date);
        if ($dateObj) {
            $training_date = $dateObj->format('Y-m-d');
        }
    }
    $training_date = $conn->real_escape_string($training_date);

    $sql = "INSERT INTO bookings (
                company_name, billing_address, tax_id, branch_code,
                course_title, instructor_name, participant_count, training_date,
                location, contact_name, contact_position, contact_phone,
                contact_email, arrange_service, site_vehicle, site_hotel,
                nimt_room, nimt_food, consent, category
            ) VALUES (
                '$company_name', '$billing_address', '$tax_id', '$branch_code',
                '$course_title', '$instructor_name', $participant_count, '$training_date',
                '$location', '$contact_name', '$contact_position', '$contact_phone',
                '$contact_email', '$arrange_service', '$site_vehicle', '$site_hotel',
                '$nimt_room', '$nimt_food', '$consent', '$category'
            )";

    if ($conn->query($sql) === TRUE) {
        header("Location: Inhouse_services.html");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
