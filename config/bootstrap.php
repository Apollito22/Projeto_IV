<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../app/helpers/seguranca.php';
require_once __DIR__ . '/../app/Models/Usuario.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Models/Categoria.php';
require_once __DIR__ . '/../app/Models/Conteudo.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/ConteudoController.php';

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0, 'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true, 'samesite' => 'Lax',
]);
session_start();
header('Cache-Control: no-store');
if (isset($_SESSION['usuario'], $_SESSION['ultimo_acesso']) && time() - $_SESSION['ultimo_acesso'] > 1800) {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['sucesso'] = 'Sua sessão expirou. Entre novamente.';
}
if (isset($_SESSION['usuario'])) {
    $_SESSION['ultimo_acesso'] = time();
}
$_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
