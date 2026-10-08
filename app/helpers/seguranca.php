<?php
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
}

function campo(string $nome): string
{
    return isset($_POST[$nome]) && is_string($_POST[$nome]) ? $_POST[$nome] : '';
}

function redirecionar(string $pagina): void
{
    header('Location: ' . $pagina, true, 303);
    exit;
}

function validar_csrf(): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], campo('csrf_token'));
}

function exigir_login(): void
{
    if (!isset($_SESSION['usuario'])) {
        redirecionar('login.php');
    }
}
