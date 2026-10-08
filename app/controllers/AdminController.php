<?php
class AdminController
{
    private PDO $pdo;
    private Conteudo $model;
    private ?int $autorId;
    private int $usuarioId;

    public function executar(): void
    {
        exigir_perfil(['admin', 'editor']);
        $this->pdo = Database::conectar();
        $this->model = new Conteudo($this->pdo);
        $this->usuarioId = (int) $_SESSION['usuario']['id'];
        $this->autorId = $_SESSION['usuario']['perfil'] === 'admin' ? null : $this->usuarioId;
        $acao = isset($_GET['acao']) && is_string($_GET['acao']) ? $_GET['acao'] : 'dashboard';
        switch ($acao) {
            case 'dashboard':
                $this->renderizar('dashboard', ['resumo' => $this->model->resumoAdministracao($this->autorId), 'conteudos' => array_slice($this->model->listarAdministracao($this->autorId), 0, 5)]);
                break;
            case 'conteudos':
                $this->renderizar('conteudos', ['conteudos' => $this->model->listarAdministracao($this->autorId)]);
                break;
            case 'categorias':
                $this->categorias();
                break;
            case 'novo':
            case 'editar':
                $id = $acao === 'editar' ? $this->id() : null;
                $conteudo = $id === null ? $this->vazio() : $this->model->buscarPorId($id, $this->autorId);
                if ($conteudo === null) { $this->naoEncontrado(); return; }
                $this->formulario($conteudo, []);
                break;
            case 'salvar':
                $this->salvar();
                break;
            case 'excluir':
                $this->excluir();
                break;
            case 'links':
                $this->links();
                break;
            case 'midias':
                $midias = (new Midia($this->pdo))->listar($this->autorId);
                $this->renderizar('midias', ['midias' => $midias]);
                break;
            default:
                $this->naoEncontrado();
        }
    }

    private function renderizar(string $view, array $dados = []): void
    {
        $titulos = ['dashboard' => 'Dashboard', 'conteudos' => 'Notícias', 'categorias' => 'Categorias', 'formulario-conteudo' => 'Editar notícia', 'excluir-conteudo' => 'Excluir notícia', 'links' => 'Gerenciar links', 'midias' => 'Imagens', 'mensagem' => 'Notícia indisponível'];
        $titulo_pagina = $view === 'formulario-conteudo' && empty($dados['conteudo']['id']) ? 'Nova notícia' : $titulos[$view];
        $categorias_menu = (new Categoria($this->pdo))->listarAtivas();
        extract($dados, EXTR_SKIP);
        require __DIR__ . '/../views/admin/' . $view . '.php';
    }

    private function id(): int
    {
        $valor = $_SERVER['REQUEST_METHOD'] === 'POST' ? campo('id') : ($_GET['id'] ?? '');
        return is_string($valor) && ctype_digit($valor) && strlen($valor) <= 10 && (int) $valor <= 2147483647 ? (int) $valor : 0;
    }

    private function vazio(): array
    {
        return ['id' => '', 'titulo' => '', 'slug' => '', 'resumo' => '', 'corpo' => '', 'categoria_id' => '', 'status' => 'rascunho', 'destaque' => false, 'link_youtube' => '', 'imagem_capa' => null, 'publicado_em' => null];
    }

    private function formulario(array $conteudo, array $erros): void
    {
        $this->renderizar('formulario-conteudo', ['conteudo' => $conteudo, 'erros' => $erros, 'categorias' => (new Categoria($this->pdo))->listarAtivas()]);
    }

    private function exigirPost(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Allow: POST');
            http_response_code(405);
            $this->renderizar('mensagem', ['mensagem' => 'Esta ação exige o envio do formulário.']);
            return false;
        }
        if (!validar_csrf()) {
            http_response_code(403);
            $this->renderizar('mensagem', ['mensagem' => 'O formulário expirou. Atualize a página e tente novamente.']);
            return false;
        }
        return true;
    }

    private function naoEncontrado(): void
    {
        http_response_code(404);
        $this->renderizar('mensagem', ['mensagem' => 'A notícia não existe ou não está disponível para seu perfil.']);
    }

    private function categorias(): void
    {
        exigir_perfil(['admin']);
        $model = new Categoria($this->pdo);
        $categoria = ['nome' => '', 'slug' => '', 'descricao' => ''];
        $erros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->exigirPost()) return;
            foreach ($categoria as $campo => $valor) $categoria[$campo] = trim(campo($campo));
            foreach ($categoria as $valor) {
                if (!mb_check_encoding($valor, 'UTF-8') || strpos($valor, "\0") !== false) $erros[] = 'O texto contém caracteres inválidos.';
            }
            if (mb_strlen($categoria['nome'], 'UTF-8') < 3 || mb_strlen($categoria['nome'], 'UTF-8') > 100) $erros[] = 'O nome deve ter entre 3 e 100 caracteres.';
            if ($categoria['slug'] === '' && !$erros) {
                $categoria['slug'] = gerar_slug($categoria['nome']);
            }
            if (strlen($categoria['slug']) > 150 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $categoria['slug'])) $erros[] = 'Use um identificador com letras minúsculas, números e hífens.';
            if (mb_strlen($categoria['descricao'], 'UTF-8') > 5000) $erros[] = 'A descrição deve ter até 5000 caracteres.';
            if (!$erros) {
                try {
                    $this->pdo->beginTransaction();
                    $id = $model->cadastrar($categoria['nome'], $categoria['slug'], $categoria['descricao']);
                    (new Auditoria($this->pdo))->registrar($this->usuarioId, 'criar', $id, ['nome' => $categoria['nome']], 'categorias');
                    $this->pdo->commit();
                    $_SESSION['sucesso'] = 'Categoria cadastrada com sucesso.';
                    redirecionar('admin.php?acao=categorias');
                } catch (Throwable $erro) {
                    if ($this->pdo->inTransaction()) $this->pdo->rollBack();
                    error_log($erro->getMessage());
                    $erros[] = $erro instanceof PDOException && $erro->getCode() === '23505' ? 'Já existe uma categoria com esse identificador.' : 'Não foi possível cadastrar a categoria.';
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Allow: GET, POST');
            http_response_code(405);
            $erros[] = 'Use o formulário para cadastrar a categoria.';
        }
        $this->renderizar('categorias', ['categorias' => $model->listarAtivas(), 'categoria' => $categoria, 'erros' => array_unique($erros)]);
    }

    private function salvar(): void
    {
        if (!$this->exigirPost()) return;
        $id = campo('id') === '' ? null : $this->id();
        $atual = $id === null ? $this->vazio() : $this->model->buscarPorId($id, $this->autorId);
        if ($atual === null) { $this->naoEncontrado(); return; }
        $conteudo = $atual;
        foreach (['titulo', 'slug', 'resumo', 'corpo', 'categoria_id', 'status', 'link_youtube'] as $campo) $conteudo[$campo] = trim(campo($campo));
        $conteudo['destaque'] = campo('destaque') === '1';
        $erros = [];
        foreach (['titulo', 'slug', 'resumo', 'corpo', 'link_youtube'] as $campo) {
            if (!mb_check_encoding($conteudo[$campo], 'UTF-8') || strpos($conteudo[$campo], "\0") !== false) $erros[] = 'O texto contém caracteres inválidos.';
        }
        if (mb_strlen($conteudo['titulo'], 'UTF-8') < 3 || mb_strlen($conteudo['titulo'], 'UTF-8') > 255) $erros[] = 'O título deve ter entre 3 e 255 caracteres.';
        if ($conteudo['slug'] === '' && !$erros) {
            $conteudo['slug'] = gerar_slug($conteudo['titulo']);
        }
        if (strlen($conteudo['slug']) > 255 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $conteudo['slug'])) $erros[] = 'Use um identificador com letras minúsculas, números e hífens.';
        if ($conteudo['corpo'] === '' || mb_strlen($conteudo['corpo'], 'UTF-8') > 100000) $erros[] = 'Preencha o texto da notícia com até 100 mil caracteres.';
        if (mb_strlen($conteudo['resumo'], 'UTF-8') > 1000) $erros[] = 'O resumo deve ter até 1000 caracteres.';
        if (!in_array($conteudo['status'], ['rascunho', 'publicado', 'arquivado'], true)) $erros[] = 'Selecione um status válido.';
        if (!link_video_valido($conteudo['link_youtube'])) $erros[] = 'Informe um link HTTP ou HTTPS válido, com até 500 caracteres.';
        $categorias = (new Categoria($this->pdo))->listarAtivas();
        if (!ctype_digit($conteudo['categoria_id']) || !in_array((int) $conteudo['categoria_id'], array_map(fn($cat) => (int) $cat['id'], $categorias), true)) $erros[] = 'Selecione uma categoria ativa.';
        if ($erros) { $this->formulario($conteudo, array_unique($erros)); return; }
        $imagem = null;
        $removerDepois = null;
        try {
            if (isset($_FILES['imagem']) && !is_array($_FILES['imagem'])) throw new InvalidArgumentException('Upload inválido.');
            $imagem = UploadImagem::salvar($_FILES['imagem'] ?? null);
            $this->pdo->beginTransaction();
            if ($id !== null) {
                $atual = $this->model->buscarPorId($id, $this->autorId, true);
                if ($atual === null) { $this->pdo->rollBack(); if ($imagem) UploadImagem::remover($imagem['caminho']); $this->naoEncontrado(); return; }
            }
            $capa = $imagem['caminho'] ?? (campo('remover_imagem') === '1' ? null : $atual['imagem_capa']);
            $dados = ['titulo' => $conteudo['titulo'], 'slug' => $conteudo['slug'], 'resumo' => $conteudo['resumo'], 'corpo' => $conteudo['corpo'], 'imagem_capa' => $capa, 'link_youtube' => $conteudo['link_youtube'] === '' ? null : $conteudo['link_youtube'], 'categoria_id' => (int) $conteudo['categoria_id'], 'autor_id' => $this->usuarioId, 'status' => $conteudo['status'], 'destaque' => $conteudo['destaque'], 'publicado_em' => $conteudo['status'] === 'publicado' ? ($atual['publicado_em'] ?: date('Y-m-d H:i:s')) : null];
            $salvoId = $this->model->salvar($dados, $id);
            $midias = new Midia($this->pdo);
            if ($imagem) $midias->cadastrar($imagem, $this->usuarioId);
            if ($atual['imagem_capa'] && $atual['imagem_capa'] !== $capa && $midias->removerSemUso($atual['imagem_capa'])) $removerDepois = $atual['imagem_capa'];
            (new Auditoria($this->pdo))->registrar($this->usuarioId, $id === null ? 'criar' : 'editar', $salvoId, ['titulo' => $conteudo['titulo']]);
            $this->pdo->commit();
        } catch (InvalidArgumentException $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            if ($imagem) UploadImagem::remover($imagem['caminho']);
            $this->formulario($conteudo, [$erro->getMessage()]);
            return;
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            if ($imagem) UploadImagem::remover($imagem['caminho']);
            error_log($erro->getMessage());
            $duplicado = $erro instanceof PDOException && $erro->getCode() === '23505';
            $this->formulario($conteudo, [$duplicado ? 'Já existe uma notícia com esse identificador. Use outro.' : 'Não foi possível salvar a notícia. Tente novamente.']);
            return;
        }
        UploadImagem::remover($removerDepois);
        $_SESSION['sucesso'] = 'Notícia salva com sucesso.';
        redirecionar('admin.php?acao=conteudos');
    }

    private function excluir(): void
    {
        $id = $this->id();
        $conteudo = $this->model->buscarPorId($id, $this->autorId);
        if ($conteudo === null) { $this->naoEncontrado(); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->renderizar('excluir-conteudo', ['conteudo' => $conteudo]); return; }
        if (!$this->exigirPost()) return;
        $removerDepois = null;
        try {
            $this->pdo->beginTransaction();
            $conteudo = $this->model->buscarPorId($id, $this->autorId, true);
            if ($conteudo === null) { $this->pdo->rollBack(); $this->naoEncontrado(); return; }
            $this->model->excluir($id);
            if ($conteudo['imagem_capa'] && (new Midia($this->pdo))->removerSemUso($conteudo['imagem_capa'])) $removerDepois = $conteudo['imagem_capa'];
            (new Auditoria($this->pdo))->registrar($this->usuarioId, 'excluir', $id, ['titulo' => $conteudo['titulo']]);
            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log($erro->getMessage());
            $this->renderizar('mensagem', ['mensagem' => 'Não foi possível excluir a notícia.']);
            return;
        }
        UploadImagem::remover($removerDepois);
        $_SESSION['sucesso'] = 'Notícia excluída com sucesso.';
        redirecionar('admin.php?acao=conteudos');
    }

    private function links(): void
    {
        $erros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->exigirPost()) return;
            $url = trim(campo('link_youtube'));
            if (!link_video_valido($url)) $erros[] = 'Informe um link HTTP ou HTTPS válido, com até 500 caracteres.';
            $id = $this->id();
            if (!$erros) {
                try {
                    $this->pdo->beginTransaction();
                    $conteudo = $this->model->buscarPorId($id, $this->autorId, true);
                    if ($conteudo === null) { $this->pdo->rollBack(); $this->naoEncontrado(); return; }
                    $this->model->atualizarLink($id, $url === '' ? null : $url);
                    (new Auditoria($this->pdo))->registrar($this->usuarioId, 'alterar_link', $id);
                    $this->pdo->commit();
                    $_SESSION['sucesso'] = 'Link atualizado com sucesso.';
                    redirecionar('admin.php?acao=links');
                } catch (Throwable $erro) {
                    if ($this->pdo->inTransaction()) $this->pdo->rollBack();
                    error_log($erro->getMessage());
                    $erros[] = 'Não foi possível atualizar o link.';
                }
            }
        }
        $this->renderizar('links', ['conteudos' => $this->model->listarAdministracao($this->autorId), 'erros' => $erros]);
    }
}
