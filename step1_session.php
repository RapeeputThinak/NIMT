<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['data_page1'] = $_POST;

    $nextPage = $_POST['next_page'] ?? '';
    $allowedPages = [
        'Inhouse_services.html',
        'academic_services.html',
        'metrology_services.html',
        'Inhouse_services_en.html',
        'academic_services_en.html',
        'metrology_services_en.html',
        'index2.html'
    ];

    if (!in_array($nextPage, $allowedPages, true)) {
        $nextPage = 'index2.html';
    }

    header("Location: $nextPage");
    exit();
}
?>