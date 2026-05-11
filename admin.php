<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "nimt_training");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่าภาษาไทยให้แสดงผลถูกต้อง
$conn->set_charset("utf8");

// รับค่าตัวกรองปีงบประมาณ
$fiscalYear = $_GET['fiscal_year'] ?? '';
$whereSql = '';
if (!empty($fiscalYear)) {
    $whereSql = "WHERE fiscal_year = '" . $conn->real_escape_string($fiscalYear) . "'";
}

$result = $conn->query("SELECT * FROM bookings $whereSql ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>ระบบจัดการข้อมูลการอบรม - NIMT</title>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 border-b pb-4 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-blue-900">ระบบจัดการข้อมูลการอบรม</h1>
                <span class="text-sm text-gray-500">จำนวนทั้งหมด: <?= $result->num_rows ?> รายการ</span>
            </div>
            <form method="get" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">ปีงบประมาณ</label>
                    <select name="fiscal_year" class="border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">ทั้งหมด</option>
                        <?php foreach (['2023-2024','2024-2025','2025-2026','2026-2027','2027-2028','2028-2029','2029-2030'] as $fy): ?>
                            <option value="<?= $fy ?>" <?= $fiscalYear === $fy ? 'selected' : '' ?>><?= $fy ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">กรอง</button>
                <a href="admin.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">รีเซ็ต</a>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-900 text-white text-sm">
                        <th class="p-3">วันที่สมัคร</th>
                        <th class="p-3">เวลา</th>
                        <th class="p-3">หน่วยงาน</th>
                        <th class="p-3">หลักสูตร</th>
                        <th class="p-3">วันที่อบรม</th>
                        <th class="p-3">ปีงบประมาณ</th>
                        <th class="p-3 text-center">สถานะ</th>
                        <th class="p-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        <td class="p-3"><?= date('H:i', strtotime($row['created_at'])) ?></td>
                        <td class="p-3 font-semibold text-gray-800"><?= htmlspecialchars($row['company_name'] ?? 'ไม่ระบุ') ?></td>
                        <td class="p-3"><?= htmlspecialchars($row['course_title'] ?? 'ไม่ระบุ') ?></td>
                        <td class="p-3 text-blue-600 font-medium">
                            <?php 
                                if (!empty($row['training_date']) && $row['training_date'] != '0000-00-00') {
                                    echo date('d/m/Y', strtotime($row['training_date']));
                                } else {
                                    echo "ไม่ได้ระบุ";
                                }
                            ?>
                        </td>
                        <td class="p-3 text-center text-sm font-semibold"><?= htmlspecialchars($row['fiscal_year'] ?? '-') ?></td>
                        <td class="p-3 text-center">
                            <?php if(($row['payment_status'] ?? '') == 'paid'): ?>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold">ชำระเงินแล้ว</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-bold">รอชำระเงิน</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-center space-x-2">
                            <a href="detail.php?id=<?= $row['id'] ?>" class="bg-blue-100 text-blue-600 px-4 py-1 rounded hover:bg-blue-600 hover:text-white transition inline-block">
                                ดูรายละเอียด
                            </a>
                            <a href="edit_booking.php?id=<?= $row['id'] ?>" class="bg-yellow-100 text-yellow-700 px-4 py-1 rounded hover:bg-yellow-500 hover:text-white transition inline-block">
                                แก้ไข
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>