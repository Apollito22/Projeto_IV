<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0, 'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true, 'samesite' => 'Lax',
]);
session_start();
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
if (isset($_SESSION['usuario'], $_SESSION['ultimo_acesso']) && time() - $_SESSION['ultimo_acesso'] > 1800) {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['sucesso'] = 'Sua sessão expirou. Entre novamente.';
}
if (isset($_SESSION['usuario'])) {
    $_SESSION['ultimo_acesso'] = time();
}
$_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
