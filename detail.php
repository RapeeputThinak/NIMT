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
                <div><p class="text-xs text-gray-500 uppercase">เลขประจำตัวผู้เสียภาษี</p><p class="font-semibold"><?= $data['tax_id'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">สำนักงานใหญ่/สาขาเลขที่</p><p class="font-semibold"><?= $data['branch_code'] ?? '-' ?></p></div>
            </div>

            <div class="grid grid-cols-3 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">วันที่สมัคร</p><p class="font-semibold"><?= date('d/m/Y', strtotime($data['created_at'])) ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">เวลาที่สมัคร</p><p class="font-semibold"><?= date('H:i', strtotime($data['created_at'])) ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">ปีงบประมาณ</p><p class="font-semibold"><?= htmlspecialchars($data['fiscal_year'] ?? '-') ?></p></div>
            </div>

            <div class="border-b pb-4">
                <p class="text-xs text-gray-500 uppercase">ที่อยู่ออกใบเสร็จ/ใบกำกับภาษี</p>
                <p class="font-semibold"><?= $data['billing_address'] ?? '-' ?></p>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">หลักสูตรที่สมัคร</p><p class="font-semibold text-blue-700"><?= $data['course_title'] ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">วิทยากร</p><p class="font-semibold"><?= $data['instructor_name'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">จำนวนผู้เข้าอบรม (คน)</p><p class="font-semibold"><?= $data['participant_count'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">วันที่อบรม</p><p class="font-semibold"><?= date('d/m/Y', strtotime($data['training_date'])) ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">สถานที่จัด</p><p class="font-semibold"><?= $data['location'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">ปีงบประมาณ</p><p class="font-semibold"><?= htmlspecialchars($data['fiscal_year'] ?? '-') ?></p></div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4">
                <div><p class="text-xs text-gray-500 uppercase">ชื่อผู้ติดต่อ</p><p class="font-semibold"><?= $data['contact_name'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">ตำแหน่ง</p><p class="font-semibold"><?= $data['contact_position'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">เบอร์โทรศัพท์</p><p class="font-semibold"><?= $data['contact_phone'] ?? '-' ?></p></div>
                <div><p class="text-xs text-gray-500 uppercase">อีเมล</p><p class="font-semibold text-blue-600"><?= $data['contact_email'] ?? '-' ?></p></div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <h3 class="text-sm font-bold text-blue-900 mb-2 border-b border-blue-200 pb-1 italic">บริการเพิ่มเติมที่ขอใช้:</h3>
                <ul class="list-disc ml-5 text-sm space-y-1">
                    <li>ความต้องการบริการ: <strong><?= $data['arrange_service'] ?? 'ไม่ระบุ' ?></strong></li>
                    <li>พาหนะ: <?= $data['site_vehicle'] ?? 'ไม่ระบุ' ?></li>
                    <li>ที่พัก: <?= $data['site_hotel'] ?? 'ไม่ระบุ' ?></li>
                    <li>ห้องอบรม: <?= $data['nimt_room'] ?? 'ไม่ระบุ' ?></li>
                    <li>อาหาร/เครื่องดื่ม: <?= $data['nimt_food'] ?? 'ไม่ระบุ' ?></li>
                    <li>การยินยอมเปิดเผยข้อมูล: <?= $data['consent'] ?? 'ไม่ระบุ' ?></li>
                </ul>
            </div>
        </div>
        
        <div class="p-6 bg-gray-50 text-center no-print">
            <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded hover:bg-black transition font-bold">พิมพ์เอกสารชุดนี้</button>
        </div>
    </div>
</body>
</html>