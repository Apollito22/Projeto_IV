<?php
function exigir_login(): void
{
    if (!isset($_SESSION['usuario'])) {
        redirecionar('login.php');
    }
}

function pode_gerenciar(): bool
{
    return isset($_SESSION['usuario']) && in_array($_SESSION['usuario']['perfil'] ?? '', ['admin', 'editor'], true);
}

function exigir_perfil(array $perfis): void
{
    exigir_login();
    $usuario = (new Usuario(Database::conectar()))->buscarPorId((int) $_SESSION['usuario']['id']);
    if ($usuario === null || !in_array($usuario['ativo'], [true, 1, '1', 't', 'true'], true)) {
        $_SESSION = [];
        redirecionar('login.php');
    }
    unset($usuario['senha_hash']);
    $_SESSION['usuario'] = $usuario;
    if (!in_array($usuario['perfil'], $perfis, true)) {
        http_response_code(403);
        $titulo_pagina = 'Acesso negado';
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
}
