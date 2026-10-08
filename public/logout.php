<?php
require_once __DIR__ . '/../config/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Use o botão Sair na página inicial.');
}
if (!validar_csrf()) {
    http_response_code(403);
    exit('Formulário inválido. Volte à página inicial e tente novamente.');
}
(new AuthController())->logout();