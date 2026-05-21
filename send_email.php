<?php
// รับข้อมูลจากฟอร์ม
$send_action = $_POST['send_action'] ?? '';
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$extra = trim($_POST['extra_emails'] ?? '');
$recipient_emails = trim($_POST['recipient_emails'] ?? '');
$category = $_POST['category'] ?? '';
$fiscal_year = $_POST['fiscal_year'] ?? '';
$month = $_POST['month'] ?? '';
$selected_ids = $_POST['selected_ids'] ?? [];

$attachmentFields = ['attachBill', 'attachExcel', 'attachOutline'];
$attachments = [];
foreach ($attachmentFields as $field) {
    if (!empty($_FILES[$field]) && is_array($_FILES[$field]) && ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$field]['tmp_name'];
        if (is_uploaded_file($tmpName)) {
            $attachments[] = [
                'path' => $tmpName,
                'name' => basename($_FILES[$field]['name']),
                'type' => $_FILES[$field]['type'] ?: mime_content_type($tmpName),
            ];
        }
    }
}

$emails = [];
// เพิ่มอีเมลจากช่องเพิ่มพิเศษ
if (!empty($extra)) {
    $parts = array_map('trim', explode(',', $extra));
    foreach ($parts as $e) {
        if (filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
    }
}

// เพิ่มอีเมลจากหน้า EmailConfirmationForm
if (!empty($recipient_emails)) {
    $parts = array_map('trim', explode(',', $recipient_emails));
    foreach ($parts as $e) {
        if (filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
    }
}

// ใช้ mailing list ผ่านหน้า form เป็น priority หากถูกเรียกจาก EmailConfirmationForm
if (!empty($recipient_emails) && $send_action === 'manual') {
    // ไม่มีการเพิ่มจาก DB ต่อไป
}
// เชื่อมต่อฐานข้อมูล เพื่อดึงอีเมลจาก bookings ตามเงื่อนไข
$conn = new mysqli("localhost", "root", "", "nimt_training");
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8');

$where = [];
if (!empty($fiscal_year)) $where[] = "fiscal_year = '" . $conn->real_escape_string($fiscal_year) . "'";
if (!empty($category)) $where[] = "category = '" . $conn->real_escape_string($category) . "'";
if (!empty($month)) $where[] = "MONTH(training_date) = " . intval($month);

$whereSql = '';
if (!empty($where)) $whereSql = 'WHERE ' . implode(' AND ', $where);

if ($send_action === 'all') {
    $res = $conn->query("SELECT contact_email FROM bookings $whereSql");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $e = trim($r['contact_email'] ?? '');
            if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
        }
    }
} elseif ($send_action === 'selected' && !empty($selected_ids)) {
    $ids = array_map('intval', $selected_ids);
    $in = implode(',', $ids);
    $res = $conn->query("SELECT contact_email FROM bookings WHERE id IN ($in)");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $e = trim($r['contact_email'] ?? '');
            if ($e && filter_var($e, FILTER_VALIDATE_EMAIL)) $emails[] = $e;
        }
    }
}

$emails = array_unique($emails);

if (empty($emails)) {
    // อัปเกรดหน้าแจ้งเตือนกรณีไม่พบอีเมล
    echo '<!DOCTYPE html><html lang="th"><head><meta charset="utf-8"><title>ไม่พบอีเมล</title><script src="https://cdn.tailwindcss.com"></script><link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet"><script src="https://unpkg.com/lucide@latest"></script><style>body{font-family:"Prompt",sans-serif;}</style></head><body class="min-h-screen bg-slate-50 flex items-center justify-center p-4"><div class="bg-white p-8 rounded-2xl shadow-lg text-center max-w-sm w-full"><i data-lucide="mail-search" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i><h2 class="text-xl font-semibold text-slate-800 mb-2">ไม่พบอีเมลปลายทาง</h2><p class="text-slate-500 text-sm mb-6">ไม่มีรายชื่ออีเมลที่ตรงกับเงื่อนไขที่คุณเลือก</p><a href="admin.php?category=' . urlencode($category) . '&fiscal_year=' . urlencode($fiscal_year) . '&month=' . urlencode($month) . '" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl transition-colors w-full"><i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>กลับไปหน้าจัดการ</a></div><script>lucide.createIcons();</script></body></html>';
    exit;
}

// โหลดคอนฟิกอีเมล (ตั้งค่า SMTP ถ้าต้องการ)
require 'email_config.php';
// ตรวจสอบสิทธิ์ผู้ใช้งาน (ต้องล็อกอินเป็นแอดมินเท่านั้น)
require_once __DIR__ . '/admin_auth.php';
require_admin();

// โหลด PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$fromAddress = $email_config['from']['address'] ?? 'no-reply@nimt.local';
$fromName = $email_config['from']['name'] ?? 'NIMT';

// ฟังก์ชัน: โหลดเทมเพลตจาก EmailConfirmationForm.html
function load_email_template($path)
{
    $html = file_get_contents($path);
    if ($html === false) return null;

    // โหลด DOM
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

    $xpath = new DOMXPath($dom);

    // ดึง div#email-content (body ส่วนที่ต้องการส่งเป็น HTML)
    $contentNode = $xpath->query('//*[@id="email-content"]')->item(0);
    $contentHtml = '';
    if ($contentNode) {
        // รับ innerHTML
        $inner = '';
        foreach ($contentNode->childNodes as $child) {
            $inner .= $dom->saveHTML($child);
        }
        $contentHtml = $inner;
    }

    // ดึงค่า default ของ input/textarea ที่มี id="input-..."
    $inputs = [];
    $nodes = $xpath->query('//*[starts-with(@id, "input-")]');
    foreach ($nodes as $n) {
        $id = $n->getAttribute('id');
        $key = preg_replace('/^input-/', '', $id);
        $value = '';
        if ($n->tagName === 'input') {
            $value = $n->getAttribute('value');
        } elseif ($n->tagName === 'textarea') {
            $value = '';
            foreach ($n->childNodes as $c) $value .= $dom->saveHTML($c);
            $value = trim($value);
        }
        $inputs[$key] = $value;
    }

    return ['content' => $contentHtml, 'defaults' => $inputs];
}

// ฟังก์ชันช่วยตั้งค่าสถานะว่าได้ส่งอีเมลแล้วสำหรับ booking ที่ตรงกับอีเมล
function mark_email_sent_for_booking($conn, $bookingId = null)
{
    // ถ้าไม่มี booking id ให้ยกเลิก (ไม่อัปเดตตามอีเมลเพื่อหลีกเลี่ยงการตั้งค่าหลายแถว)
    if (empty($bookingId)) {
        return;
    }
    $check = $conn->query("SHOW COLUMNS FROM bookings LIKE 'email_sent'");
    if (!$check || $check->num_rows === 0) {
        $conn->query("ALTER TABLE bookings ADD COLUMN email_sent TINYINT(1) DEFAULT 0");
        $check = $conn->query("SHOW COLUMNS FROM bookings LIKE 'email_sent'");
    }
    if ($check && $check->num_rows > 0) {
        $id = intval($bookingId);
        $conn->query("UPDATE bookings SET email_sent = 1 WHERE id = $id");
    }
}

$template = load_email_template(__DIR__ . '/EmailConfirmationForm.html');
// โหลด defaults จากไฟล์ที่บันทึกไว้ถ้ามี
$defaultsFile = __DIR__ . '/email_defaults.json';
if (file_exists($defaultsFile)) {
    $saved = json_decode(file_get_contents($defaultsFile), true);
    if (is_array($saved)) {
        // รวมค่าที่บันทึกไว้เข้ากับ defaults ที่อ่านจากเทมเพลต (saved ชนะ)
        $template['defaults'] = array_merge($template['defaults'] ?? [], $saved);
    }
}

// แทนค่าที่ส่งมาจากฟอร์ม ปรับให้ใช้งานได้กับข้อมูลที่แก้จากหน้า EmailConfirmationForm
if (!empty($template['defaults']) && !empty($_POST)) {
    foreach ($template['defaults'] as $key => $value) {
        if (isset($_POST[$key])) {
            $template['defaults'][$key] = trim((string) $_POST[$key]);
        }
    }
}

$success = 0;
$failures = [];

// หากผู้ใช้แก้ไขพรีวิวและส่งเป็น HTML ตรงมา จะมีฟิลด์ use_custom_body=1 และ custom_body
$use_custom_body = (!empty($_POST['use_custom_body']) && (string)$_POST['use_custom_body'] === '1');
$custom_body = $use_custom_body ? ($_POST['custom_body'] ?? '') : '';

foreach ($emails as $to) {
    // พยายามดึงข้อมูล booking ของผู้รับถ้ามี
    $booking = null;
    // หากฟอร์มส่ง booking_id มา ให้ค้นโดย id (ปลอดภัยและแม่นยำ)
    $postedBookingId = intval($_POST['booking_id'] ?? 0);
    if ($postedBookingId > 0) {
        $res = $conn->query("SELECT * FROM bookings WHERE id = " . $postedBookingId . " LIMIT 1");
        if ($res && $res->num_rows) {
            $booking = $res->fetch_assoc();
        }
    }

    // หากยังไม่พบ booking และมีอีเมล ปรับ fallback เป็นค้นจาก contact_email
    if (!$booking && !empty($to)) {
        $res = $conn->query("SELECT * FROM bookings WHERE contact_email = '" . $conn->real_escape_string($to) . "' LIMIT 1");
        if ($res && $res->num_rows) {
            $booking = $res->fetch_assoc();
        }
    }

    // ถ้ามี custom body จากผู้ใช้ ให้ใช้เลย (ไม่ทำการแทนเทมเพลต)
    $mailBody = '';
    if ($use_custom_body && $custom_body !== '') {
        // ข้อควรระวัง: สมมติว่า admin เป็นผู้แก้ไข จึงยอมให้ส่ง HTML ได้โดยตรง
        $mailBody = $custom_body;
    } elseif ($template && $template['content']) {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?><div id="root">' . $template['content'] . '</div>');
        $xp = new DOMXPath($doc);

        // ฟังก์ชันช่วยแทนข้อความใน element by id
        $setById = function($id, $value) use ($doc, $xp) {
            $el = $xp->query('//*[@id="' . $id . '"]')->item(0);
            if ($el) {
                if ($el->tagName === 'a') {
                    $url = trim((string) $value);
                    if ($url !== '') {
                        $el->setAttribute('href', $url);
                        $style = $el->getAttribute('style');
                        $style = preg_replace('/display:\s*none;?/', '', $style);
                        $style = trim($style);
                        $style = $style ? $style . '; display: inline;' : 'display: inline;';
                        $el->setAttribute('style', $style);
                    } else {
                        $el->setAttribute('style', 'display: none;');
                    }
                    return;
                }

                // ลบ children เดิม
                while ($el->firstChild) {
                    $el->removeChild($el->firstChild);
                }
                $el->appendChild($doc->createTextNode($value));
            }
        };

        // เริ่มจาก defaults
        foreach ($template['defaults'] as $k => $v) {
            $setById('preview-' . $k, $v);
        }

        // อัปเดตวันที่ซ้ำถ้ามีค่า paymentDueDate
        if (!empty($template['defaults']['paymentDueDate'])) {
            $setById('preview-paymentDueDate2', $template['defaults']['paymentDueDate']);
        }

        // แทนค่าจาก booking ถ้ามี
        if ($booking && $send_action !== 'manual') {
            // mapping fields
            $map = [
                'recipientName' => $booking['contact_name'] ?? ($booking['company_name'] ?? ''),
                'courseName' => $booking['course_title'] ?? '',
                'trainingDate' => (!empty($booking['training_date']) && $booking['training_date'] != '0000-00-00') ? date('j F Y', strtotime($booking['training_date'])) : ($template['defaults']['trainingDate'] ?? ''),
                'attendeeCount' => $booking['participant_count'] ?? $booking['participant_count'] ?? ($template['defaults']['attendeeCount'] ?? ''),
                'speakerName' => $booking['instructor_name'] ?? ($template['defaults']['speakerName'] ?? ''),
                'speakerPhone' => $booking['contact_phone'] ?? ($template['defaults']['speakerPhone'] ?? ''),
            ];
            foreach ($map as $k => $v) {
                $setById('preview-' . $k, $v);
            }
        }

        // เติมชื่อไฟล์แนบลงในพรีวิวอีเมล (ถ้ามีไฟล์อัปโหลดเข้ามา)
        $attachPreviewFields = ['attachBill', 'attachExcel', 'attachOutline'];
        foreach ($attachPreviewFields as $af) {
            $fileName = '';
            if (!empty($_FILES[$af]) && (($_FILES[$af]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK)) {
                $fileName = basename($_FILES[$af]['name']);
            }
            // ใส่เป็นข้อความ เช่น " (แนบ: filename)" จะปรากฏใน <span> ที่มี id ตามเทมเพลต
            $setById('preview-' . $af . 'File', $fileName ? " (แนบ: $fileName)" : '');
        }

        // แปลง DOM เป็น HTML string (เนื้อหาภายใน #root)
        $root = $xp->query('//*[@id="root"]')->item(0);
        $inner = '';
        if ($root) {
            foreach ($root->childNodes as $c) $inner .= $doc->saveHTML($c);
        }
        $mailBody = $inner;
    } else {
        // fallback: ใช้ข้อความธรรมดาจาก POST
        $mailBody = nl2br(htmlspecialchars($message));
    }

    $mail = new PHPMailer(true);
    try {
        // หากเปิด SMTP ในคอนฟิก ให้ตั้งค่าการส่งแบบ SMTP
        if (!empty($email_config['smtp']['enabled'])) {
            $mail->isSMTP();
            $mail->Host = $email_config['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $email_config['smtp']['username'];
            $mail->Password = $email_config['smtp']['password'];
            $mail->SMTPSecure = $email_config['smtp']['encryption'] ?? 'tls';
            $mail->Port = $email_config['smtp']['port'] ?? 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject ?: 'ประกาศจาก NIMT';
        $mail->isHTML(true);
        $mail->Body = $mailBody;
        $mail->AltBody = strip_tags($mailBody);

        foreach ($attachments as $attach) {
            if (file_exists($attach['path'])) {
                $mail->addAttachment($attach['path'], $attach['name']);
            }
        }

        // Support a temporary test flag to force-mark as sent without actually sending (useful for local testing)
        $forceSend = (!empty($_POST['__test_force_send']) && (string)$_POST['__test_force_send'] === '1');
        $sentOk = false;
        if ($forceSend) {
            $sentOk = true;
        } else {
            $sentOk = $mail->send();
        }

        if (!$sentOk) {
            $failures[$to] = $mail->ErrorInfo ?? 'send failed';
        } else {
            $success++;
            if ($booking) {
                // อัปเดตสถานะเฉพาะแถว booking นี้เท่านั้น
                if (isset($booking['id'])) {
                    mark_email_sent_for_booking($conn, $booking['id']);
                }
            }
        }
    } catch (Exception $e) {
        $failures[$to] = $e->getMessage();
    }
}

// -------------------------------------------------------------
// เริ่มส่วนแสดงผลลัพธ์ที่ได้รับการออกแบบใหม่ (UI/UX)
// -------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สรุปผลการส่งอีเมล - NIMT</title>
    <!-- โหลด Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- โหลดฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- โหลด Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; }
        /* ตกแต่ง Scrollbar ให้สวยงาม */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #fee2e2;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #f87171;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #ef4444;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden transform transition-all">
        
        <!-- ส่วนหัวแบนเนอร์ (Header) -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 md:p-8 text-white text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 shadow-sm backdrop-blur-sm">
                <i data-lucide="send" class="w-8 h-8 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold">รายงานผลการส่งอีเมล</h2>
            <p class="text-blue-100 text-sm mt-1">ระบบได้ทำการประมวลผลการส่งอีเมลเสร็จสิ้นแล้ว</p>
        </div>

        <!-- กล่องสถิติ (Stats Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 md:p-8 border-b border-slate-100">
            <!-- ส่งสำเร็จ -->
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center shadow-sm">
                <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mx-auto mb-3"></i>
                <p class="text-emerald-700 font-medium text-sm mb-1">ส่งสำเร็จ</p>
                <div class="text-4xl font-bold text-emerald-600">
                    <?= $success ?> <span class="text-lg font-normal">รายการ</span>
                </div>
            </div>
            
            <!-- ล้มเหลว -->
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center shadow-sm">
                <i data-lucide="x-circle" class="w-10 h-10 text-red-500 mx-auto mb-3"></i>
                <p class="text-red-700 font-medium text-sm mb-1">ล้มเหลว</p>
                <div class="text-4xl font-bold text-red-600">
                    <?= count($failures) ?> <span class="text-lg font-normal">รายการ</span>
                </div>
            </div>
        </div>

        <!-- รายการที่ผิดพลาด (Error List) จะแสดงต่อเมื่อมี Error เท่านั้น -->
        <?php if (!empty($failures)): ?>
        <div class="px-6 md:px-8 py-6 bg-white">
            <div class="bg-white border border-red-200 rounded-xl overflow-hidden shadow-sm">
                <div class="bg-red-50 px-4 py-3 border-b border-red-100 flex items-center text-red-800 font-semibold">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 text-red-600"></i> 
                    รายละเอียดข้อผิดพลาด
                </div>
                <div class="p-4 bg-white">
                    <ul class="space-y-3 text-sm max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    <?php foreach ($failures as $to => $err): ?>
                        <li class="flex items-start bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <i data-lucide="mail-warning" class="w-4 h-4 mr-3 mt-0.5 text-slate-400 flex-shrink-0"></i>
                            <div>
                                <strong class="text-slate-800 block mb-0.5"><?= htmlspecialchars($to) ?></strong>
                                <span class="text-red-600 text-xs"><?= htmlspecialchars($err) ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ส่วนท้าย & ปุ่มกลับ -->
        <div class="bg-slate-50 p-6 border-t border-slate-200 text-center flex justify-center">
            <a href="admin.php?category=<?= urlencode($category) ?>&fiscal_year=<?= urlencode($fiscal_year) ?>&month=<?= urlencode($month) ?>" class="inline-flex items-center justify-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white font-medium px-8 py-3 rounded-xl shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>กลับไปหน้าจัดการ</span>
            </a>
        </div>
        
    </div>

    <!-- Script ควบคุม Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>