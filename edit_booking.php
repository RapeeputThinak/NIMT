<?php
$conn = new mysqli("localhost", "root", "", "nimt_training");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = intval($_GET['id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $conn->real_escape_string($_POST['company_name'] ?? '');
    $billing_address = $conn->real_escape_string($_POST['billing_address'] ?? '');
    $tax_id = $conn->real_escape_string($_POST['tax_id'] ?? '');
    $branch_code = $conn->real_escape_string($_POST['branch_code'] ?? '');
    $course_title = $conn->real_escape_string($_POST['course_title'] ?? '');
    $instructor_name = $conn->real_escape_string($_POST['instructor_name'] ?? '');
    $participant_count = intval($_POST['participant_count'] ?? 0);
    $training_date = $conn->real_escape_string($_POST['training_date'] ?? '');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $contact_name = $conn->real_escape_string($_POST['contact_name'] ?? '');
    $contact_position = $conn->real_escape_string($_POST['contact_position'] ?? '');
    $contact_phone = $conn->real_escape_string($_POST['contact_phone'] ?? '');
    $contact_email = $conn->real_escape_string($_POST['contact_email'] ?? '');
    $arrange_service = $conn->real_escape_string($_POST['arrange'] ?? '');
    $site_vehicle = isset($_POST['site_vehicle']) ? 'ใช่' : 'ไม่';
    $site_hotel = isset($_POST['site_hotel']) ? 'ใช่' : 'ไม่';
    $nimt_room = isset($_POST['nimt_room']) ? 'ใช่' : 'ไม่';
    $nimt_food = isset($_POST['nimt_food']) ? 'ใช่' : 'ไม่';
    $consent = isset($_POST['consent']) ? 'ยินยอม' : 'ไม่ยินยอม';
    $selected_fiscal_year = $conn->real_escape_string($_POST['fiscal_year'] ?? '');

    if (!empty($selected_fiscal_year)) {
        $fiscal_year = $selected_fiscal_year;
    } else {
        $fiscal_year = '';
        if (!empty($training_date)) {
            try {
                $date = new DateTime($training_date);
                $year = (int)$date->format('Y');
                $month = (int)$date->format('n');
                $fiscal_year = ($month >= 10) ? ($year . '-' . ($year + 1)) : (($year - 1) . '-' . $year);
            } catch (Exception $e) {
                $fiscal_year = '';
            }
        }
    }

    $sql = "UPDATE bookings SET
                company_name = '$company_name',
                billing_address = '$billing_address',
                tax_id = '$tax_id',
                branch_code = '$branch_code',
                course_title = '$course_title',
                instructor_name = '$instructor_name',
                participant_count = $participant_count,
                training_date = '$training_date',
                location = '$location',
                contact_name = '$contact_name',
                contact_position = '$contact_position',
                contact_phone = '$contact_phone',
                contact_email = '$contact_email',
                arrange_service = '$arrange_service',
                site_vehicle = '$site_vehicle',
                site_hotel = '$site_hotel',
                nimt_room = '$nimt_room',
                nimt_food = '$nimt_food',
                consent = '$consent',
                fiscal_year = '$fiscal_year'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $message = 'บันทึกการแก้ไขเรียบร้อยแล้ว';
    } else {
        $message = 'เกิดข้อผิดพลาด: ' . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM bookings WHERE id = $id");
$data = $result->fetch_assoc();
if (!$data) {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลการสมัคร - NIMT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-slate-50 py-8 px-4">
    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-lg border border-gray-200 overflow-hidden">
        <div class="bg-blue-900 p-6 text-white flex justify-between items-center">
            <h1 class="text-xl font-bold">แก้ไขข้อมูลการสมัคร</h1>
            <a href="admin.php" class="bg-white text-blue-900 px-4 py-2 rounded shadow hover:bg-slate-100">กลับหน้าจัดการ</a>
        </div>
        <div class="p-8">
            <?php if (!empty($message)): ?>
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm mb-1">ชื่อหน่วยงาน</label>
                        <input type="text" name="company_name" value="<?= htmlspecialchars($data['company_name'] ?? '') ?>" required class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">เลขประจำตัวผู้เสียภาษี</label>
                        <input type="text" name="tax_id" value="<?= htmlspecialchars($data['tax_id'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1">ที่อยู่ออกใบเสร็จ/ใบกำกับภาษี</label>
                        <textarea name="billing_address" rows="2" class="w-full border-gray-300 rounded-md p-2 border shadow-sm"><?= htmlspecialchars($data['billing_address'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">สำนักงานใหญ่ / สาขาเลขที่</label>
                        <input type="text" name="branch_code" value="<?= htmlspecialchars($data['branch_code'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">หัวข้ออบรม</label>
                        <input type="text" name="course_title" value="<?= htmlspecialchars($data['course_title'] ?? '') ?>" required class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">วิทยากร</label>
                        <input type="text" name="instructor_name" value="<?= htmlspecialchars($data['instructor_name'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">จำนวนผู้เข้าอบรม (คน)</label>
                        <input type="number" name="participant_count" value="<?= htmlspecialchars($data['participant_count'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">วันที่อบรม</label>
                        <input type="date" name="training_date" value="<?= htmlspecialchars($data['training_date'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">ปีงบประมาณ</label>
                        <select name="fiscal_year" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                            <option value="">เลือกปีงบประมาณ</option>
                            <option value="2023-2024" <?= ($data['fiscal_year'] ?? '') === '2023-2024' ? 'selected' : '' ?>>2023-2024</option>
                            <option value="2024-2025" <?= ($data['fiscal_year'] ?? '') === '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
                            <option value="2025-2026" <?= ($data['fiscal_year'] ?? '') === '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
                            <option value="2026-2027" <?= ($data['fiscal_year'] ?? '') === '2026-2027' ? 'selected' : '' ?>>2026-2027</option>
                            <option value="2027-2028" <?= ($data['fiscal_year'] ?? '') === '2027-2028' ? 'selected' : '' ?>>2027-2028</option>
                            <option value="2028-2029" <?= ($data['fiscal_year'] ?? '') === '2028-2029' ? 'selected' : '' ?>>2028-2029</option>
                            <option value="2029-2030" <?= ($data['fiscal_year'] ?? '') === '2029-2030' ? 'selected' : '' ?>>2029-2030</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1">สถานที่จัด</label>
                        <input type="text" name="location" value="<?= htmlspecialchars($data['location'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">ชื่อผู้ติดต่อ</label>
                        <input type="text" name="contact_name" value="<?= htmlspecialchars($data['contact_name'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">ตำแหน่ง</label>
                        <input type="text" name="contact_position" value="<?= htmlspecialchars($data['contact_position'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">เบอร์โทรศัพท์</label>
                        <input type="tel" name="contact_phone" value="<?= htmlspecialchars($data['contact_phone'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">อีเมล</label>
                        <input type="email" name="contact_email" value="<?= htmlspecialchars($data['contact_email'] ?? '') ?>" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm mb-1">ต้องการให้จัดบริการ</label>
                        <select name="arrange" class="w-full border-gray-300 rounded-md p-2 border shadow-sm">
                            <option value="">ไม่ระบุ</option>
                            <option value="ต้องการขอใช้บริการ" <?= ($data['arrange_service'] ?? '') === 'ต้องการขอใช้บริการ' ? 'selected' : '' ?>>ต้องการขอใช้บริการ</option>
                            <option value="ไม่ต้องการขอใช้บริการ" <?= ($data['arrange_service'] ?? '') === 'ไม่ต้องการขอใช้บริการ' ? 'selected' : '' ?>>ไม่ต้องการขอใช้บริการ</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <p class="text-sm font-semibold">บริการเพิ่มเติม</p>
                        <label class="flex items-center gap-2"><input type="checkbox" name="site_vehicle" value="1" <?= ($data['site_vehicle'] ?? '') === 'ใช่' ? 'checked' : '' ?> class="w-4 h-4"> พาหนะ</label>
                        <label class="flex items-center gap-2"><input type="checkbox" name="site_hotel" value="1" <?= ($data['site_hotel'] ?? '') === 'ใช่' ? 'checked' : '' ?> class="w-4 h-4"> ที่พัก</label>
                        <label class="flex items-center gap-2"><input type="checkbox" name="nimt_room" value="1" <?= ($data['nimt_room'] ?? '') === 'ใช่' ? 'checked' : '' ?> class="w-4 h-4"> ห้องอบรม</label>
                        <label class="flex items-center gap-2"><input type="checkbox" name="nimt_food" value="1" <?= ($data['nimt_food'] ?? '') === 'ใช่' ? 'checked' : '' ?> class="w-4 h-4"> อาหาร/เครื่องดื่ม</label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1">การยินยอมเปิดเผยข้อมูล</label>
                        <div class="flex gap-6 items-center">
                            <label class="flex items-center gap-2"><input type="radio" name="consent" value="ยินยอม" <?= ($data['consent'] ?? '') === 'ยินยอม' ? 'checked' : '' ?> class="w-4 h-4"> ยินยอม</label>
                            <label class="flex items-center gap-2"><input type="radio" name="consent" value="ไม่ยินยอม" <?= ($data['consent'] ?? '') === 'ไม่ยินยอม' ? 'checked' : '' ?> class="w-4 h-4"> ไม่ยินยอม</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-800 transition">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>