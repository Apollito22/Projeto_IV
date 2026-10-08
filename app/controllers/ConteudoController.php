<?php
class ConteudoController
{
    public function mostrar(string $slug): void
    {
        $titulo_pagina = 'Publicação';
        $categorias_menu = [];
        $conteudo = null;
        $erro_pagina = '';
        try {
            $pdo = Database::conectar();
            $categorias_menu = (new Categoria($pdo))->listarAtivas();
            $conteudo = (new Conteudo($pdo))->buscarPublicadoPorSlug($slug);
            if ($conteudo === null) {
                http_response_code(404);
                $erro_pagina = 'A publicação solicitada não está disponível.';
            } else {
                $titulo_pagina = $conteudo['titulo'];
            }
        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            http_response_code(503);
            $erro_pagina = 'Não foi possível carregar a publicação. Tente novamente em instantes.';
        }
        require __DIR__ . '/../views/home/mostrar.php';
    }
}
