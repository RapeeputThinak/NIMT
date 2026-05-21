<?php
require_once __DIR__ . '/admin_auth.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เลือกหมวดหมู่ - NIMT Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-[#1e3a8a] via-[#1e3163] to-[#0f172a] min-h-screen flex flex-col items-center justify-center py-12 px-4">
    
    <!-- Header Section -->
    <header class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/10 backdrop-blur-md mb-4 shadow-lg border border-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-blue-200">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-2">เลือกระบบจัดการข้อมูล</h1>
        <p class="text-blue-200 text-sm md:text-base">คลิกเลือกหมวดหมู่และภาษาที่ต้องการเข้าสู่ระบบหลังบ้าน</p>
    </header>

    <div class="w-full max-w-5xl">
        
        <!-- แบนเนอร์รายการทั้งหมด (แยกออกมาให้อยู่ด้านบนแบบเต็มความกว้าง) -->
        <a href="admin.php?category=&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 md:p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden mb-8 w-full max-w-3xl mx-auto">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-green-50 rounded-full transition-transform duration-700 group-hover:scale-[3.5] z-0"></div>
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300 shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12a8.25 8.25 0 1116.5 0 8.25 8.25 0 01-16.5 0zM9 8.25h6M12 12h.008v.008H12V12zm3 0h.008v.008H15V12zm-6 3h.008v.008H9V15z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1 group-hover:text-green-900 transition-colors">รายการทั้งหมด</h2>
                        <p class="text-gray-500 font-medium">ดูข้อมูลการลงทะเบียนรวมทุกหมวดหมู่</p>
                    </div>
                </div>
                <div class="bg-gray-100 text-gray-600 rounded-full px-4 py-2 text-sm font-bold flex items-center gap-2 shadow-sm border border-gray-200">
                    📊 ALL
                </div>
            </div>
        </a>

        <!-- เส้นแบ่งหมวดหมู่ย่อย -->
        <div class="flex items-center gap-4 mb-6 max-w-5xl mx-auto">
            <div class="h-[1px] flex-1 bg-white/10"></div>
            <div class="text-blue-200/80 text-sm font-medium tracking-wide">หรือเลือกดูตามหมวดหมู่เฉพาะ</div>
            <div class="h-[1px] flex-1 bg-white/10"></div>
        </div>

        <!-- Cards Grid (6 การ์ดเรียงแบบ 3 คอลัมน์ จะลงตัวพอดี) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Inhouse TH -->
            <a href="admin.php?category=Inhouse-TH&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">🇹🇭 TH</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Inhouse</h2>
                    <p class="text-gray-500 font-medium">ภาษาไทย</p>
                </div>
            </a>

            <!-- Inhouse EN -->
            <a href="admin.php?category=Inhouse-EN&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">🇬🇧 EN</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Inhouse</h2>
                    <p class="text-gray-500 font-medium">English</p>
                </div>
            </a>

            <!-- Academic TH -->
            <a href="admin.php?category=Academic-TH&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">🇹🇭 TH</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Academic</h2>
                    <p class="text-gray-500 font-medium">ภาษาไทย</p>
                </div>
            </a>

            <!-- Academic EN -->
            <a href="admin.php?category=Academic-EN&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">🇬🇧 EN</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Academic</h2>
                    <p class="text-gray-500 font-medium">English</p>
                </div>
            </a>

            <!-- Metrology TH -->
            <a href="admin.php?category=Metrology-TH&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-teal-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">🇹🇭 TH</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Metrology</h2>
                    <p class="text-gray-500 font-medium">ภาษาไทย</p>
                </div>
            </a>

            <!-- Metrology EN -->
            <a href="admin.php?category=Metrology-EN&fiscal_year=" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-teal-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">🇬🇧 EN</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Metrology</h2>
                    <p class="text-gray-500 font-medium">English</p>
                </div>
            </a>

            <!-- Price Editor -->
            <a href="admin_prices.php" class="group relative block bg-white rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-fuchsia-50 rounded-full transition-transform duration-500 group-hover:scale-[2.5] z-0"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-fuchsia-100 rounded-2xl flex items-center justify-center text-fuchsia-600 group-hover:bg-fuchsia-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0l-3-3m3 3l3-3M4.5 19.5h15a2.25 2.25 0 002.25-2.25v-11A2.25 2.25 0 0019.5 4h-15A2.25 2.25 0 002.25 6.25v11A2.25 2.25 0 004.5 19.5z" /></svg>
                        </div>
                        <div class="bg-gray-100 text-gray-600 rounded-full px-3 py-1 text-xs font-bold flex items-center gap-1 shadow-sm border border-gray-200">ราคาตาราง</div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">แก้ไขราคาตาราง</h2>
                    <p class="text-gray-500 font-medium">หน้าจัดการราคา Inhouse/Metrology/Academic</p>
                </div>
            </a>
        </div>
    </div>

    <footer class="mt-14 text-center text-blue-200/60 text-sm">
        &copy; <?php echo date('Y'); ?> NIMT Admin Registration System
    </footer>

</body>
</html>