<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "nimt_training");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่าภาษาไทยให้แสดงผลถูกต้อง
$conn->set_charset("utf8");

// รับค่าตัวกรองปีงบประมาณและหมวดหมู่
$fiscalYear = $_GET['fiscal_year'] ?? '';
$category = $_GET['category'] ?? ''; // ใช้จาก Sidebar แทน Dropdown

$whereClauses = [];
if (!empty($fiscalYear)) {
    $whereClauses[] = "fiscal_year = '" . $conn->real_escape_string($fiscalYear) . "'";
}
if (!empty($category)) {
    $whereClauses[] = "category = '" . $conn->real_escape_string($category) . "'";
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

$result = $conn->query("SELECT * FROM bookings $whereSql ORDER BY created_at DESC");

// จัดการชื่อหมวดหมู่ที่กำลังแสดงอยู่
$categories = [
    '' => 'รายการทั้งหมด',
    'Inhouse-TH' => 'Inhouse (ไทย)',
    'Inhouse-EN' => 'Inhouse (อังกฤษ)',
    'Academic-TH' => 'Academic (ไทย)',
    'Academic-EN' => 'Academic (อังกฤษ)',
    'Metrology-TH' => 'Metrology (ไทย)',
    'Metrology-EN' => 'Metrology (อังกฤษ)'
];
$currentCategoryName = $categories[$category] ?? 'รายการทั้งหมด';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>ระบบจัดการข้อมูลการอบรม - NIMT Admin</title>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <!-- Sidebar ด้านข้างสำหรับแยกหมวดหมู่ชัดเจน -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col shadow-lg z-10 hidden md:flex">
        <div class="p-6 border-b border-blue-800">
            <h2 class="text-2xl font-bold">NIMT Admin</h2>
            <p class="text-blue-300 text-sm mt-1">ระบบจัดการข้อมูลการอบรม</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider mb-2 mt-4 px-3">เมนูหลัก</p>
            <a href="?category=&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === '' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                📋 รายการทั้งหมด
            </a>

            <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider mb-2 mt-6 px-3">Inhouse Training</p>
            <a href="?category=Inhouse-TH&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === 'Inhouse-TH' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                🇹🇭 Inhouse (ไทย)
            </a>
            <a href="?category=Inhouse-EN&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === 'Inhouse-EN' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                🇬🇧 Inhouse (อังกฤษ)
            </a>

            <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider mb-2 mt-6 px-3">Academic</p>
            <a href="?category=Academic-TH&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === 'Academic-TH' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                🇹🇭 Academic (ไทย)
            </a>
            <a href="?category=Academic-EN&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === 'Academic-EN' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                🇬🇧 Academic (อังกฤษ)
            </a>

            <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider mb-2 mt-6 px-3">Metrology</p>
            <a href="?category=Metrology-TH&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === 'Metrology-TH' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                🇹🇭 Metrology (ไทย)
            </a>
            <a href="?category=Metrology-EN&fiscal_year=<?= $fiscalYear ?>" class="block px-3 py-2 rounded-lg transition <?= $category === 'Metrology-EN' ? 'bg-blue-700 text-white font-semibold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                🇬🇧 Metrology (อังกฤษ)
            </a>
        </nav>
    </aside>

    <!-- พื้นที่เนื้อหาหลัก (Main Content) -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800"><?= $currentCategoryName ?></h1>
                <span class="text-sm text-gray-500">พบข้อมูลทั้งหมด <?= $result->num_rows ?> รายการ</span>
            </div>
            
            <!-- ตัวกรองปีงบประมาณ -->
            <form method="get" class="flex items-center gap-3">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <label class="text-sm text-gray-600 font-medium">ปีงบประมาณ:</label>
                <select name="fiscal_year" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">ทั้งหมด</option>
                    <?php foreach (['2023-2024','2024-2025','2025-2026','2026-2027','2027-2028','2028-2029','2029-2030'] as $fy): ?>
                        <option value="<?= $fy ?>" <?= $fiscalYear === $fy ? 'selected' : '' ?>><?= $fy ?></option>
                    <?php endforeach; ?>
                </select>
                <a href="?category=<?= htmlspecialchars($category) ?>" class="text-sm text-gray-500 hover:text-blue-600 underline">ล้างค่า</a>
            </form>
        </header>

        <!-- ตารางแสดงผล -->
        <div class="flex-1 overflow-auto p-8">
            <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 text-sm border-b">
                                <th class="p-4 font-semibold">วันที่สมัคร</th>
                                <th class="p-4 font-semibold">หน่วยงาน</th>
                                <th class="p-4 font-semibold">หลักสูตร</th>
                                <th class="p-4 font-semibold">วันที่อบรม</th>
                                <th class="p-4 font-semibold text-center">ปีงบประมาณ</th>
                                <th class="p-4 font-semibold text-center">สถานะ</th>
                                <th class="p-4 font-semibold text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-4 text-gray-600">
                                        <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                        <div class="text-xs text-gray-400"><?= date('H:i', strtotime($row['created_at'])) ?> น.</div>
                                    </td>
                                    <td class="p-4 font-medium text-gray-800"><?= htmlspecialchars($row['company_name'] ?? 'ไม่ระบุ') ?></td>
                                    <td class="p-4 text-gray-600">
                                        <?= htmlspecialchars($row['course_title'] ?? 'ไม่ระบุ') ?>
                                        <?php if($category === ''): // แสดง tag หมวดหมู่เฉพาะตอนอยู่หน้า "ทั้งหมด" ?>
                                            <div class="text-xs text-blue-500 mt-1"><?= htmlspecialchars($row['category'] ?? '-') ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-blue-600 font-medium">
                                        <?php 
                                            if (!empty($row['training_date']) && $row['training_date'] != '0000-00-00') {
                                                echo date('d/m/Y', strtotime($row['training_date']));
                                            } else {
                                                echo "<span class='text-gray-400 font-normal'>ไม่ได้ระบุ</span>";
                                            }
                                        ?>
                                    </td>
                                    <td class="p-4 text-center font-semibold text-gray-600"><?= htmlspecialchars($row['fiscal_year'] ?? '-') ?></td>
                                    <td class="p-4 text-center">
                                        <?php if(($row['payment_status'] ?? '') == 'paid'): ?>
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold inline-block">ชำระเงินแล้ว</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold inline-block">รอชำระเงิน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-center space-x-1">
                                        <a href="detail.php?id=<?= $row['id'] ?>" class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded text-xs hover:bg-blue-600 hover:text-white transition inline-block">
                                            รายละเอียด
                                        </a>
                                        <a href="edit_booking.php?id=<?= $row['id'] ?>" class="bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1.5 rounded text-xs hover:bg-yellow-500 hover:text-white transition inline-block">
                                            แก้ไข
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-500">ไม่พบข้อมูลในหมวดหมู่นี้</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>
</html>