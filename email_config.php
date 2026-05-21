<?php
function load_dotenv($path)
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        [$name, $value] = array_map('trim', explode('=', $line, 2) + ['', '']);
        if ($name === '' || $value === '') {
            continue;
        }
        if (($value[0] === '"' && $value[strlen($value) - 1] === '"') || ($value[0] === "'" && $value[strlen($value) - 1] === "'")) {
            $value = substr($value, 1, -1);
        }
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

load_dotenv(__DIR__ . '/.env');

// การตั้งค่าอีเมล (ตัวอย่าง)
// ตั้งค่า 'enabled' เป็น true เพื่อใช้ SMTP แทนฟังก์ชัน mail() ของ PHP
$email_config = [
    'smtp' => [
        'enabled' => true,
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => getenv('SMTP_PORT') ?: 587,
        'username' => getenv('SMTP_USERNAME') ?: 'ponrapeeput@gmail.com',
        'password' => getenv('SMTP_PASSWORD') ?: 'dbiu lkpt syli nxxs',
        'encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls', // 'tls' or 'ssl'
    ],
    'from' => [
        'address' => 'no-reply@nimt.local',
        'name' => 'NIMT'
    ]
    ,
    'admin' => [
        // ค่าตัวอย่างสำหรับการล็อกอินหน้าแอดมิน (เปลี่ยนเป็นรหัสที่ปลอดภัย)
        'username' => 'admin',
        'password' => 'admin'
        
    ]
];

// NOTE: For production, store credentials securely (not in plaintext).
