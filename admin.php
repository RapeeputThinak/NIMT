<?php
require_once __DIR__ . '/admin_auth.php';
require_admin();
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "nimt_training");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่าภาษาไทยให้แสดงผลถูกต้อง
$conn->set_charset("utf8");

// รับค่าตัวกรองปีงบประมาณ หมวดหมู่ และเดือน
$fiscalYear = $_GET['fiscal_year'] ?? '';
$category = $_GET['category'] ?? ''; // ใช้จาก Sidebar แทน Dropdown
$month = $_GET['month'] ?? ''; // เดือนที่เลือก

$whereClauses = [];
if (!empty($fiscalYear)) {
    $whereClauses[] = "fiscal_year = '" . $conn->real_escape_string($fiscalYear) . "'";
}
if (!empty($category)) {
    $whereClauses[] = "category = '" . $conn->real_escape_string($category) . "'";
}
if (!empty($month)) {
    // กรองตามเดือนของ training_date
    $whereClauses[] = "MONTH(training_date) = " . intval($month);
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$countResult = $conn->query("SELECT COUNT(*) AS total FROM bookings $whereSql");
$totalRows = 0;
if ($countResult) {
    $countData = $countResult->fetch_assoc();
    $totalRows = intval($countData['total'] ?? 0);
}
$totalPages = max(1, ceil($totalRows / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$result = $conn->query("SELECT * FROM bookings $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");

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
                <span class="text-sm text-gray-500">แสดง <?= $result ? $result->num_rows : 0 ?> จาก <?= $totalRows ?> รายการ (หน้า <?= $page ?> / <?= $totalPages ?>)</span>
            <!-- Delete confirmation modal (single & bulk, with reason) -->
            <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">ยืนยันการลบ</h3>
                    <p id="deleteModalText" class="text-sm text-gray-600 mb-4">คุณแน่ใจหรือไม่ว่าจะลบรายการนี้? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">เหตุผลการลบ (ไม่บังคับ)</label>
                    <textarea id="deleteReason" class="w-full border border-gray-200 rounded-md p-2 mb-4 text-sm" rows="3" placeholder="ระบุเหตุผลหรือบันทึกข้อมูลประกอบการลบ"></textarea>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">ยกเลิก</button>
                        <button id="confirmDeleteBtn" onclick="performDelete()" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">ลบเลย</button>
                    </div>
                </div>
            </div>

            <script>
            let bookingToDelete = null; // number or array of numbers

            function collectSelectedIds() {
                const ids = [];
                document.querySelectorAll('.rowCheckbox:checked').forEach(cb => {
                    const v = parseInt(cb.value);
                    if (!isNaN(v)) ids.push(v);
                });
                return ids;
            }

            function confirmBulkDelete() {
                const ids = collectSelectedIds();
                if (!ids.length) {
                    alert('กรุณาเลือกอย่างน้อย 1 แถวเพื่อทำการลบ');
                    return;
                }
                bookingToDelete = ids;
                const modal = document.getElementById('deleteModal');
                const txt = document.getElementById('deleteModalText');
                txt.textContent = 'คุณแน่ใจหรือไม่ว่าจะลบ ' + ids.length + ' รายการ? การกระทำนี้ไม่สามารถย้อนกลับได้';
                document.getElementById('deleteReason').value = '';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function confirmDelete(id) {
                bookingToDelete = id;
                const modal = document.getElementById('deleteModal');
                const txt = document.getElementById('deleteModalText');
                txt.textContent = 'คุณแน่ใจหรือไม่ว่าจะลบรายการ ID ' + id + '? การกระทำนี้ไม่สามารถย้อนกลับได้';
                document.getElementById('deleteReason').value = '';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteModal() {
                bookingToDelete = null;
                const modal = document.getElementById('deleteModal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }

            async function performDelete() {
                if (!bookingToDelete) return;
                const reason = document.getElementById('deleteReason').value || '';
                const ids = Array.isArray(bookingToDelete) ? bookingToDelete : [bookingToDelete];
                try {
                    const res = await fetch('delete_booking.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: ids, reason: reason })
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) throw new Error(data.error || 'ลบไม่สำเร็จ');

                    // remove rows from table
                    ids.forEach(id => {
                        const checkbox = document.querySelector('.rowCheckbox[value="' + id + '"]');
                        if (checkbox) {
                            const tr = checkbox.closest('tr');
                            if (tr) tr.remove();
                        }
                    });

                    alert('ลบรายการเรียบร้อยแล้ว');
                } catch (err) {
                    console.error(err);
                    alert(err.message || 'เกิดข้อผิดพลาดขณะลบ');
                } finally {
                    closeDeleteModal();
                }
            }
            </script>
            </div>
            
            <!-- ตัวกรองปีงบประมาณ และเดือน -->
            <form method="get" class="flex items-center gap-3 flex-wrap">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium">ปีงบประมาณ:</label>
                    <select name="fiscal_year" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        <?php foreach (['2023-2024','2024-2025','2025-2026','2026-2027','2027-2028','2028-2029','2029-2030'] as $fy): ?>
                            <option value="<?= $fy ?>" <?= $fiscalYear === $fy ? 'selected' : '' ?>><?= $fy ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium">เดือน:</label>
                    <select name="month" class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        <option value="1" <?= $month === '1' ? 'selected' : '' ?>>มกราคม</option>
                        <option value="2" <?= $month === '2' ? 'selected' : '' ?>>กุมภาพันธ์</option>
                        <option value="3" <?= $month === '3' ? 'selected' : '' ?>>มีนาคม</option>
                        <option value="4" <?= $month === '4' ? 'selected' : '' ?>>เมษายน</option>
                        <option value="5" <?= $month === '5' ? 'selected' : '' ?>>พฤษภาคม</option>
                        <option value="6" <?= $month === '6' ? 'selected' : '' ?>>มิถุนายน</option>
                        <option value="7" <?= $month === '7' ? 'selected' : '' ?>>กรกฎาคม</option>
                        <option value="8" <?= $month === '8' ? 'selected' : '' ?>>สิงหาคม</option>
                        <option value="9" <?= $month === '9' ? 'selected' : '' ?>>กันยายน</option>
                        <option value="10" <?= $month === '10' ? 'selected' : '' ?>>ตุลาคม</option>
                        <option value="11" <?= $month === '11' ? 'selected' : '' ?>>พฤศจิกายน</option>
                        <option value="12" <?= $month === '12' ? 'selected' : '' ?>>ธันวาคม</option>
                    </select>
                </div>
                <a href="?category=<?= htmlspecialchars($category) ?>" class="text-sm text-gray-500 hover:text-blue-600 underline">ล้างค่า</a>
            </form>
            <?php if(!empty(
                $_SESSION['is_admin']
            )): ?>
                <div class="ml-4 flex items-center gap-3">
                    <a href="admin_prices.php" class="text-sm bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-600 hover:text-white transition">แก้ไขราคาตาราง</a>
                    <button onclick="confirmBulkDelete()" class="text-sm bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded hover:bg-red-600 hover:text-white transition">ลบที่เลือก</button>
                    <a href="admin_logout.php" class="text-sm text-red-600 hover:underline">ออกจากระบบ</a>
                </div>
            <?php else: ?>
                <div class="ml-4">
                    <a href="admin_login.php" class="text-sm text-blue-600 hover:underline">เข้าสู่ระบบ</a>
                </div>
            <?php endif; ?>
        </header>


        <!-- ตารางแสดงผล -->
        <div class="flex-1 overflow-auto p-8">
            <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 text-sm border-b">
                                <th class="p-4 font-semibold"><input id="selectAll" type="checkbox" class="w-4 h-4"></th>
                                <th class="p-4 font-semibold">วันที่สมัคร</th>
                                <th class="p-4 font-semibold">หน่วยงาน</th>
                                <th class="p-4 font-semibold">เบอร์โทร</th>
                                <th class="p-4 font-semibold">อีเมล</th>
                                <th class="p-4 font-semibold">ส่งอีเมล</th>
                                <th class="p-4 font-semibold">หลักสูตร</th>
                                <th class="p-4 font-semibold">วันที่อบรม</th>
                                <th class="p-4 font-semibold text-center">ปีงบประมาณ</th>
                                <th class="p-4 font-semibold text-center">สถานะชำระเงิน</th>
                                <th class="p-4 font-semibold text-center">สถานะส่งอีเมล</th>
                                <th class="p-4 font-semibold text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-4 text-gray-600">
                                        <input type="checkbox" class="rowCheckbox" value="<?= $row['id'] ?>">
                                    </td>
                                    <td class="p-4 text-gray-600">
                                        <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                        <div class="text-xs text-gray-400"><?= date('H:i', strtotime($row['created_at'])) ?> น.</div>
                                    </td>
                                    <td class="p-4 font-medium text-gray-800"><?= htmlspecialchars($row['company_name'] ?? 'ไม่ระบุ') ?></td>
                                    <td class="p-4 text-blue-600 font-medium"><?= htmlspecialchars($row['contact_phone'] ?? 'ไม่ระบุ') ?></td>
                                    <td class="p-4 text-gray-600 font-medium recipient-email"><?= htmlspecialchars($row['contact_email'] ?? 'ไม่ระบุ') ?></td>
                                    <td class="p-4 text-center">
                                        <?php if (!empty($row['contact_email']) && filter_var($row['contact_email'], FILTER_VALIDATE_EMAIL)): ?>
                                            <?php
                                                $recipientNameParam = urlencode($row['company_name'] ?? '');
                                                $speakerNameParam = urlencode($row['instructor_name'] ?? '');
                                                $trainingDateParam = '';
                                                if (!empty($row['training_date']) && $row['training_date'] != '0000-00-00') {
                                                    $trainingDateParam = urlencode(date('d/m/Y', strtotime($row['training_date'])));
                                                }
                                                $attendeeCountParam = urlencode($row['participant_count'] ?? '');
                                            ?>
                                            <a href="EmailConfirmationForm.html?recipient_emails=<?= urlencode($row['contact_email']) ?>&recipientName=<?= $recipientNameParam ?>&speakerName=<?= $speakerNameParam ?>&trainingDate=<?= $trainingDateParam ?>&attendeeCount=<?= $attendeeCountParam ?>&booking_id=<?= $row['id'] ?>" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-xs font-semibold hover:bg-blue-600 hover:text-white transition">ส่งอีเมล</a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">ไม่มีอีเมล</span>
                                        <?php endif; ?>
                                    </td>
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
                                    <?php
                                        $isPaid = ($row['payment_status'] ?? '') === 'paid';
                                        $isEmailSent = !empty($row['email_sent']);
                                    ?>
                                    <td class="p-4 text-center">
                                        <select class="payment-status-select px-3 py-1 rounded text-xs font-semibold border-2 cursor-pointer" onchange="updatePaymentStatus(<?= $row['id'] ?>, this.value)" data-booking-id="<?= $row['id'] ?>" style="border-color: <?= $isPaid ? '#10b981' : '#ef4444' ?>; color: <?= $isPaid ? '#10b981' : '#ef4444' ?>; background-color: <?= $isPaid ? '#ecfdf5' : '#fef2f2' ?>;">
                                            <option value="paid" <?= ($row['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>ชำระแล้ว</option>
                                            <option value="pending" <?= ($row['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>ยังไม่ชำระ</option>
                                        </select>
                                    </td>
                                    <td class="p-4 text-center">
                                        <?php if ($isEmailSent): ?>
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold inline-block">ส่งเมลแล้ว</span>
                                        <?php else: ?>
                                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold inline-block">ยังไม่ส่ง</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-center space-x-1">
                                        <a href="detail.php?id=<?= $row['id'] ?>" class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded text-xs hover:bg-blue-600 hover:text-white transition inline-block">
                                            รายละเอียด
                                        </a>
                                        <a href="edit_booking.php?id=<?= $row['id'] ?>" class="bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1.5 rounded text-xs hover:bg-yellow-500 hover:text-white transition inline-block">
                                            แก้ไข
                                        </a>
                                        <button onclick="confirmDelete(<?= $row['id'] ?>)" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded text-xs hover:bg-red-600 hover:text-white transition inline-block">ลบ</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="p-8 text-center text-gray-500">ไม่พบข้อมูลในหมวดหมู่นี้</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-8 py-4 bg-white border-t border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <p class="text-sm text-gray-600">หน้า <?= $page ?> จาก <?= $totalPages ?> | แสดง <?= $result ? $result->num_rows : 0 ?> จาก <?= $totalRows ?> รายการ</p>
                <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $p]))) ?>" class="px-3 py-1 border border-gray-200 text-sm <?= $p === $page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?> rounded-md <?= $p === 1 ? 'rounded-l-md' : '' ?> <?= $p === $totalPages ? 'rounded-r-md' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        </div>
    </main>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectAll = document.getElementById('selectAll');
    if (!selectAll) return;
    selectAll.addEventListener('change', function(){
        document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = selectAll.checked);
    });
});

function collectEmails(useSelected = false) {
    const rows = document.querySelectorAll('tbody tr');
    const emails = [];
    rows.forEach(row => {
        const emailTd = row.querySelector('.recipient-email');
        const checkbox = row.querySelector('.rowCheckbox');
        if (!emailTd || !checkbox) return;
        const email = emailTd.textContent.trim();
        if (!email || email === 'ไม่ระบุ') return;
        if (useSelected) {
            if (!checkbox.checked) return;
        }
        emails.push(email);
    });
    return [...new Set(emails)].join(', ');
}

async function updatePaymentStatus(id, status) {
    try {
        const response = await fetch('update_payment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, status })
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'ไม่สามารถอัปเดตสถานะชำระเงินได้');
        }

        const select = document.querySelector(`select[data-booking-id="${id}"]`);
        if (!select) return;

        if (status === 'paid') {
            select.style.borderColor = '#10b981';
            select.style.color = '#10b981';
            select.style.backgroundColor = '#ecfdf5';
        } else {
            select.style.borderColor = '#ef4444';
            select.style.color = '#ef4444';
            select.style.backgroundColor = '#fef2f2';
        }

        alert('อัปเดตสถานะสำเร็จ');
    } catch (error) {
        console.error(error);
        const select = document.querySelector(`select[data-booking-id="${id}"]`);
        if (select) {
            const currentStatus = select.value;
            select.value = currentStatus === 'paid' ? 'pending' : 'paid';
        }
        alert(error.message || 'เกิดข้อผิดพลาดขณะอัปเดตสถานะ');
    }
}

function sendSelectedEmails() {
    const selected = collectEmails(true);
    if (!selected) {
        alert('กรุณาเลือกแถวอย่างน้อย 1 แถวก่อนส่ง');
        return;
    }
    document.getElementById('recipientEmailsInput').value = selected;
    document.getElementById('redirectForm').submit();
}

function sendFilteredEmails() {
    const all = collectEmails(false);
    if (!all) {
        alert('ไม่พบอีเมลจากผลการกรอง');
        return;
    }
    document.getElementById('recipientEmailsInput').value = all;
    document.getElementById('redirectForm').submit();
}
</script>
</body>
</html>

<!-- Script: when user presses browser back, redirect to admin_select.php -->
<script>
// Push a dummy history state so we can detect back navigation
(function(){
    try {
        history.pushState({noBackExitsApp: true}, document.title, location.href);
        window.addEventListener('popstate', function (e) {
            // Redirect to admin_select.php when back is pressed
            window.location.href = 'admin_select.php';
        });
    } catch (err) {
        // ignore
    }
})();
</script>
