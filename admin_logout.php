<?php
require_once __DIR__ . '/admin_auth.php';
logout_admin();
header('Location: admin_login.php');
exit;
