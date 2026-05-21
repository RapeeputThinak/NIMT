<?php
// Simple admin session auth helper
session_start();
require_once __DIR__ . '/email_config.php';

function require_admin()
{
    if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: admin_login.php');
        exit;
    }
}

function login_admin($username, $password)
{
    global $email_config;
    $u = $email_config['admin']['username'] ?? 'admin';
    $p = $email_config['admin']['password'] ?? '';
    if ($username === $u && $password === $p) {
        $_SESSION['is_admin'] = true;
        return true;
    }
    return false;
}

function logout_admin()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
