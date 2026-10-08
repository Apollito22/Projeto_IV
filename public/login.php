<?php
require_once __DIR__ . '/../config/bootstrap.php';
if (isset($_SESSION['usuario'])) { redirecionar('home.php'); }
$cadastro = false;
$erros = $_SERVER['REQUEST_METHOD'] === 'POST' ? (new AuthController())->processar(false) : [];
require __DIR__ . '/../app/Views/auth.php';
