<?php
$conn = new mysqli("localhost", "root", "", "nimt_training");
$conn->set_charset("utf8");

// รับ ID จาก URL
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM bookings WHERE id = $id");
$data = $result->fetch_assoc();

if (!$data) {
    echo "ไม่พบข้อมูลที่ต้องการ";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>รายละเอียดการสมัคร - <?= htmlspecialchars($data['company_name']) ?></title>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-100 py-10 px-4">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden border">
        <div class="bg-blue-900 p-6 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold">รายละเอียดแบบคำขอใช้บริการ</h1>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="admin.php" class="text-sm bg-white text-blue-900 px-3 py-2 rounded shadow hover:bg-slate-100 transition">กลับหน้าจัดการ</a>
                <a href="edit_booking.php?id=<?= $data['id'] ?>" class="text-sm bg-yellow-100 text-yellow-800 px-3 py-2 rounded shadow hover:bg-yellow-200 transition">แก้ไขข้อมูล</a>
            </div>
        </div>

<div class="p-8 space-y-4">
            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">ชื่อหน่วยงาน</p><p class="font-semibold"><?= $data['company_name'] ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">หมวดหมู่</p><p class="font-semibold"><?= htmlspecialchars($data['category'] ?? '-') ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">เลขประจำตัวผู้เสียภาษี</p><p class="font-semibold"><?= $data['tax_id'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">สำนักงานใหญ่/สาขาเลขที่</p><p class="font-semibold"><?= $data['branch_code'] ?? '-' ?></p></div>
            </div>

            <div class="grid grid-cols-3 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">วันที่สมัครเข้าสู่ระบบ</p><p class="font-semibold"><?= date('d/m/Y', strtotime($data['created_at'])) ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">วันที่แจ้งขอใช้บริการ (จากฟอร์ม)</p><p class="font-semibold text-blue-700"><?= !empty($data['request_date']) && $data['request_date'] != '0000-00-00' ? date('d/m/Y', strtotime($data['request_date'])) : '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">เลขที่อ้างอิง (Ref No.)</p><p class="font-semibold"><?= htmlspecialchars($data['reference_no'] ?? '-') ?></p></div>
            </div>

            <div class="border-b pb-4">
                <p class="text-xs text-gray-500 uppercase">ที่อยู่ออกใบเสร็จ/ใบกำกับภาษี</p>
                <p class="font-semibold"><?= $data['billing_address'] ?? '-' ?></p>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">หลักสูตร/หัวข้อที่รับคำปรึกษา</p><p class="font-semibold text-blue-700"><?= $data['course_title'] ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">เครื่องมือ/ห้องปฏิบัติการ</p><p class="font-semibold"><?= htmlspecialchars($data['lab_tool'] ?? '-') ?></p></div>
                
                <div><p class="text-xs text-gray-500 uppercase">วิทยากร</p><p class="font-semibold"><?= $data['instructor_name'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">จำนวนผู้เข้าอบรม (คน)</p><p class="font-semibold"><?= $data['participant_count'] ?? '-' ?></p></div>
                
                <div><p class="text-xs text-gray-500 uppercase">วันที่อบรม/ให้คำปรึกษา</p><p class="font-semibold"><?= !empty($data['training_date']) && $data['training_date'] != '0000-00-00' ? date('d/m/Y', strtotime($data['training_date'])) : '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">สถานที่จัด</p><p class="font-semibold"><?= $data['location'] ?? '-' ?></p></div>
                
                <div><p class="text-xs text-gray-500 uppercase">ปีงบประมาณ</p><p class="font-semibold"><?= htmlspecialchars($data['fiscal_year'] ?? '-') ?></p></div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">ชื่อผู้ติดต่อ</p><p class="font-semibold"><?= $data['contact_name'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">ตำแหน่ง</p><p class="font-semibold"><?= $data['contact_position'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">เบอร์โทรศัพท์</p><p class="font-semibold"><?= $data['contact_phone'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">อีเมล</p><p class="font-semibold text-blue-600"><?= $data['contact_email'] ?? '-' ?></p></div>
            </div>
</body>
</html>