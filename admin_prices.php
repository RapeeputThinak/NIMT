<?php
require_once __DIR__ . '/admin_auth.php';
require_admin();

$configFile = __DIR__ . '/pricing_config.json';
$config = [];
if (file_exists($configFile)) {
    $raw = file_get_contents($configFile);
    $config = json_decode($raw, true) ?? [];
}

$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = $_POST['pricing'] ?? [];
    foreach ($posted as $section => $values) {
        if (!isset($config[$section]) || !is_array($values)) {
            continue;
        }
        foreach ($values as $key => $value) {
            $config[$section][$key] = trim((string)$value);
        }
    }

    $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        $message = 'ไม่สามารถบันทึกค่าได้ โปรดลองใหม่อีกครั้ง';
        $isError = true;
    } else {
        file_put_contents($configFile, $json);
        $message = 'บันทึกค่าราคาสำเร็จแล้ว';
        $isError = false;
    }
}

// กำหนดลำดับตารางที่ต้องแสดง
$displayOrder = [
    'Inhouse_form_TH',
    'Inhouse_form_EN',
    'Inhouse_services_TH',
    'Inhouse_services_EN',
    'Metrology_form_TH',
    'Metrology_form_EN',
    'Metrology_services_TH',
    'Metrology_services_EN',
    'Academic_form_TH',
    'Academic_form_EN',
    'Academic_services_TH',
    'Academic_services_EN',
];

// จัดเรียงตารางตามลำดับที่กำหนด
$orderedConfig = [];
foreach ($displayOrder as $key) {
    if (isset($config[$key])) {
        $orderedConfig[$key] = $config[$key];
    }
}

function humanLabel(string $pageKey): string
{
    return str_replace(['_', '-'], ' ', $pageKey);
}

function getCategoryFromKey(string $pageKey): string
{
    if (strpos($pageKey, 'Inhouse') !== false) {
        return 'Inhouse';
    } elseif (strpos($pageKey, 'Metrology') !== false) {
        return 'Metrology';
    } elseif (strpos($pageKey, 'Academic') !== false) {
        return 'Academic';
    }
    return '';
}

function getFormTypeFromKey(string $pageKey): string
{
    if (strpos($pageKey, 'form') !== false) {
        return 'Form';
    } elseif (strpos($pageKey, 'services') !== false) {
        return 'Services';
    }
    return '';
}

function getLanguageFromKey(string $pageKey): string
{
    if (strpos($pageKey, '_TH') !== false) {
        return 'ไทย (TH)';
    } elseif (strpos($pageKey, '_EN') !== false) {
        return 'English (EN)';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แก้ไขราคาตาราง - NIMT Admin</title>
    <!-- โหลด Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- โหลดฟอนต์ Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- โหลด Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen text-slate-800 pb-16">
    
    <!-- Top Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 md:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 text-white p-2 rounded-lg shadow-sm">
                    <i data-lucide="settings-2" class="w-5 h-5"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-900 leading-none">จัดการราคาตาราง (Pricing Config)</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="admin_select.php" class="hidden sm:flex items-center space-x-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors bg-slate-100 hover:bg-blue-50 px-4 py-2 rounded-xl">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>กลับหน้าแอดมิน</span>
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto py-8 px-4 md:px-8">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">แก้ไขราคาตาราง</h2>
                <p class="text-slate-500 text-sm">จัดการราคาสำหรับ Inhouse / Metrology / Academic ทั้งภาษาไทยและอังกฤษ</p>
                <p class="text-xs text-slate-400 mt-2">รวม 12 ตารางแบบฟอร์ม (Form) และแบบรายละเอียดบริการ (Services)</p>
            </div>
            <div class="flex gap-3">
                <a href="pricing_config.json" target="_blank" class="inline-flex items-center justify-center space-x-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                    <i data-lucide="file-json" class="w-4 h-4 text-slate-400"></i>
                    <span>ดูไฟล์ JSON</span>
                </a>
            </div>
        </div>

        <!-- Alert Message -->
        <?php if ($message): ?>
            <div class="mb-8 p-4 rounded-xl border <?= $isError ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' ?> flex items-center shadow-sm">
                <i data-lucide="<?= $isError ? 'alert-circle' : 'check-circle-2' ?>" class="w-5 h-5 mr-3 shrink-0 <?= $isError ? 'text-red-500' : 'text-emerald-500' ?>"></i>
                <p class="font-medium text-sm"><?= htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE) ?></p>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <form action="" method="POST" class="space-y-8">
            
            <?php 
            $categories = ['Inhouse', 'Metrology', 'Academic'];
            foreach ($categories as $category): 
                $categoryItems = array_filter($orderedConfig, function($key) use ($category) {
                    return strpos($key, $category) !== false;
                }, ARRAY_FILTER_USE_KEY);
                
                if (empty($categoryItems)) continue;
            ?>
                <!-- หมวดหมู่หลัก -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-gradient-to-b from-blue-600 to-indigo-600 rounded-full"></div>
                        <h2 class="text-2xl font-bold text-slate-900 uppercase tracking-wide"><?= htmlspecialchars($category) ?> Training</h2>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <?php foreach ($categoryItems as $pageKey => $fields): 
                            $formType = getFormTypeFromKey($pageKey);
                            $language = getLanguageFromKey($pageKey);
                            $isTH = strpos($pageKey, '_TH') !== false;
                        ?>
                            <!-- Card สำหรับแต่ละตาราง -->
                            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all hover:shadow-lg hover:border-slate-300">
                                
                                <!-- Header Card -->
                                <div class="<?= $isTH ? 'bg-blue-50 border-b-2 border-blue-200' : 'bg-indigo-50 border-b-2 border-indigo-200' ?> px-6 py-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="<?= $isTH ? 'bg-blue-100 text-blue-600' : 'bg-indigo-100 text-indigo-600' ?> p-2 rounded-lg">
                                                <i data-lucide="<?= $formType === 'Form' ? 'file-text' : 'list' ?>" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-600 capitalize"><?= htmlspecialchars($formType) ?></p>
                                                <p class="text-xs text-slate-500 font-mono"><?= htmlspecialchars($pageKey) ?></p>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full <?= $isTH ? 'bg-blue-200 text-blue-700' : 'bg-indigo-200 text-indigo-700' ?>">
                                            <?= htmlspecialchars($language) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Fields Grid -->
                                <div class="p-6">
                                    <div class="grid gap-4">
                                        <?php foreach ($fields as $fieldKey => $fieldValue): ?>
                                            <label class="block group">
                                                <span class="block text-sm font-semibold text-slate-700 mb-1.5 capitalize transition-colors group-hover:text-blue-600">
                                                    <?= htmlspecialchars(str_replace(['_', '-'], ' ', $fieldKey)) ?>
                                                </span>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                        <i data-lucide="tag" class="w-4 h-4 text-slate-400"></i>
                                                    </div>
                                                    <input type="text"
                                                           name="pricing[<?= htmlspecialchars($pageKey) ?>][<?= htmlspecialchars($fieldKey) ?>]"
                                                           value="<?= htmlspecialchars($fieldValue) ?>"
                                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm font-medium text-slate-900"
                                                    />
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Action Area (Sticky) -->
            <div class="mt-12 sticky bottom-0 bg-white p-6 rounded-2xl shadow-lg border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center text-sm text-slate-500">
                    <i data-lucide="info" class="w-4 h-4 mr-2 text-blue-500"></i>
                    <span>บันทึกข้อมูลลงใน <code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded text-xs ml-1">pricing_config.json</code></span>
                </div>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-md hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:-translate-y-0.5 hover:shadow-lg">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>บันทึกการเปลี่ยนแปลง</span>
                </button>
            </div>
            
        </form>
    </div>

    <script>
        // สร้างไอคอนจาก Lucide
        lucide.createIcons();
    </script>
</body>
</html>