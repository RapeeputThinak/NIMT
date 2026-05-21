<?php
require_once __DIR__ . '/admin_auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login_admin($username, $password)) {
        header('Location: admin_select.php');
        exit;
    } else {
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Login - Secure Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 p-4">
    
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md transform transition-all">
        
        <div class="text-center mb-8">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i data-lucide="shield-check" class="w-8 h-8 text-blue-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">เข้าสู่ระบบผู้ดูแล</h2>
            <p class="text-slate-500 text-sm mt-1">กรุณากรอกข้อมูลเพื่อเข้าถึงระบบหลังบ้าน</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 flex items-center bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg" role="alert">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <p class="text-sm font-medium"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ชื่อผู้ใช้</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input name="username" type="text" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="แอดมิน" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">รหัสผ่าน</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input name="password" type="password" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="••••••••" required>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium px-4 py-3 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    <span>เข้าสู่ระบบ</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
            
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>