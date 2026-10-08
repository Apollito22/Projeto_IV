<?php
require_once __DIR__ . '/../config/bootstrap.php';
$pagina = isset($_GET['pagina']) && is_string($_GET['pagina']) ? $_GET['pagina'] : 'inicio';
try {
    switch ($pagina) {
        case 'inicio':
            (new HomeController())->index();
            break;
        case 'categoria':
            $slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
            (new HomeController())->index(mb_strlen($slug, 'UTF-8') <= 150 ? $slug : '');
            break;
        case 'conteudo':
            $slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
            (new ConteudoController())->mostrar(mb_strlen($slug, 'UTF-8') <= 255 ? $slug : '');
            break;
        case 'login':
        case 'cadastro':
            if (isset($_SESSION['usuario'])) { redirecionar('home.php'); }
            $erros = $_SERVER['REQUEST_METHOD'] === 'POST' ? (new AuthController())->processar($pagina === 'cadastro') : [];
            $cadastro_admin_disponivel = $pagina === 'login' && PrimeiroAdmin::acessoLocal() && PrimeiroAdmin::disponivel(new Usuario(Database::conectar()));
            require __DIR__ . '/../app/views/admin/' . ($pagina === 'cadastro' ? 'cadastro_usuarios.php' : 'login.php');
            break;
        case 'primeiro_admin':
            (new AuthController())->primeiroAdmin();
            break;
        case 'logout':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Allow: POST');
                http_response_code(405);
                exit('Use o botão Sair.');
            }
            if (!validar_csrf()) {
                http_response_code(403);
                exit('Formulário inválido. Atualize a página.');
            }
            (new AuthController())->logout();
            break;
        case 'painel':
            (new HomeController())->painel();
            break;
        case 'admin':
            (new AdminController())->executar();
            break;
        default:
            http_response_code(404);
            $titulo_pagina = 'Página não encontrada';
            require_once __DIR__ . '/../app/views/layout/header.php';
            echo '<section class="container secao-publicacoes"><h1>Página não encontrada</h1><a href="index.php">Voltar ao início</a></section>';
            require_once __DIR__ . '/../app/views/layout/footer.php';
    }
} catch (PDOException $erro) {
    error_log($erro->getMessage());
    http_response_code(503);
    $titulo_pagina = 'Serviço indisponível';
    $categorias_menu = [];
    require_once __DIR__ . '/../app/views/layout/header.php';
    echo '<section class="container secao-publicacoes"><h1>Serviço indisponível</h1><p>Não foi possível acessar o banco de dados. Tente novamente em instantes.</p></section>';
    require_once __DIR__ . '/../app/views/layout/footer.php';
}
