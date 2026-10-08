<?php
class HomeController
{
    public function index(?string $slug = null): void
    {
        $titulo_pagina = 'Página Inicial';
        $categorias_menu = [];
        $conteudos_recentes = [];
        $categoria_atual = null;
        $erro_pagina = '';
        try {
            $pdo = Database::conectar();
            $modelCategoria = new Categoria($pdo);
            $categorias_menu = $modelCategoria->listarAtivas();
            if ($slug !== null) {
                $categoria_atual = $modelCategoria->buscarPorSlug($slug);
                if ($categoria_atual === null) {
                    http_response_code(404);
                    $titulo_pagina = 'Categoria não encontrada';
                    $erro_pagina = 'A categoria solicitada não está disponível.';
                } else {
                    $titulo_pagina = $categoria_atual['nome'];
                }
            }
            if ($erro_pagina === '') {
                $conteudos_recentes = (new Conteudo($pdo))->listarPublicados(9, $categoria_atual === null ? null : (int) $categoria_atual['id']);
            }
        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            http_response_code(503);
            $erro_pagina = 'Não foi possível carregar as publicações. Tente novamente em instantes.';
        }
        require __DIR__ . '/../Views/home/index.php';
    }
}
