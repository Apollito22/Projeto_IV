<?php
class Conteudo
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarPublicados(int $limite = 9, ?int $categoriaId = null): array
    {
        $sql = "SELECT c.id, c.titulo, c.slug, c.resumo, c.imagem_capa, c.publicado_em,
                    cat.nome AS categoria_nome, cat.slug AS categoria_slug, u.nome AS autor_nome
                FROM conteudos c
                INNER JOIN categorias cat ON c.categoria_id = cat.id
                INNER JOIN usuarios u ON c.autor_id = u.id
                WHERE c.status = 'publicado' AND c.publicado_em IS NOT NULL AND cat.ativo = TRUE";
        if ($categoriaId !== null) {
            $sql .= ' AND c.categoria_id = :categoria_id';
        }
        $sql .= ' ORDER BY c.publicado_em DESC, c.id DESC LIMIT :limite';
        $stmt = $this->pdo->prepare($sql);
        if ($categoriaId !== null) {
            $stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limite', max(1, min($limite, 100)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPublicadoPorSlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT c.id, c.titulo, c.slug, c.resumo, c.corpo, c.imagem_capa, c.link_youtube, c.publicado_em,
                                          cat.nome AS categoria_nome, cat.slug AS categoria_slug, u.nome AS autor_nome
                                   FROM conteudos c
                                   INNER JOIN categorias cat ON c.categoria_id = cat.id
                                   INNER JOIN usuarios u ON c.autor_id = u.id
                                   WHERE c.slug = :slug AND c.status = 'publicado'
                                     AND c.publicado_em IS NOT NULL AND cat.ativo = TRUE
                                   LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function listarAdministracao(?int $autorId = null): array
    {
        $sql = 'SELECT c.*, cat.nome AS categoria_nome, u.nome AS autor_nome FROM conteudos c INNER JOIN categorias cat ON c.categoria_id = cat.id INNER JOIN usuarios u ON c.autor_id = u.id';
        if ($autorId !== null) $sql .= ' WHERE c.autor_id = :autor';
        $sql .= ' ORDER BY c.id DESC LIMIT 100';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($autorId === null ? [] : ['autor' => $autorId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id, ?int $autorId = null, bool $bloquear = false): ?array
    {
        $sql = 'SELECT * FROM conteudos WHERE id = :id';
        $params = ['id' => $id];
        if ($autorId !== null) { $sql .= ' AND autor_id = :autor'; $params['autor'] = $autorId; }
        if ($bloquear) $sql .= ' FOR UPDATE';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function salvar(array $dados, ?int $id = null): int
    {
        if ($id === null) {
            $sql = 'INSERT INTO conteudos (titulo, slug, resumo, corpo, imagem_capa, link_youtube, categoria_id, autor_id, status, destaque, publicado_em) VALUES (:titulo, :slug, :resumo, :corpo, :imagem_capa, :link_youtube, :categoria_id, :autor_id, :status, :destaque, :publicado_em) RETURNING id';
        } else {
            unset($dados['autor_id']);
            $dados['id'] = $id;
            $sql = 'UPDATE conteudos SET titulo = :titulo, slug = :slug, resumo = :resumo, corpo = :corpo, imagem_capa = :imagem_capa, link_youtube = :link_youtube, categoria_id = :categoria_id, status = :status, destaque = :destaque, publicado_em = :publicado_em WHERE id = :id RETURNING id';
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($dados as $nome => $valor) {
            $tipo = $valor === null ? PDO::PARAM_NULL : (is_bool($valor) ? PDO::PARAM_BOOL : (is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR));
            $stmt->bindValue(':' . $nome, $valor, $tipo);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function excluir(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM conteudos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function atualizarLink(int $id, ?string $url): void
    {
        $stmt = $this->pdo->prepare('UPDATE conteudos SET link_youtube = :url WHERE id = :id');
        $stmt->execute(['url' => $url, 'id' => $id]);
    }

    public function resumoAdministracao(?int $autorId = null): array
    {
        $sql = "SELECT COUNT(*) AS total, COUNT(*) FILTER (WHERE status = 'publicado') AS publicados, COUNT(*) FILTER (WHERE status = 'rascunho') AS rascunhos, COUNT(*) FILTER (WHERE status = 'arquivado') AS arquivados, COUNT(*) FILTER (WHERE link_youtube IS NOT NULL AND link_youtube <> '') AS links FROM conteudos";
        if ($autorId !== null) $sql .= ' WHERE autor_id = :autor';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($autorId === null ? [] : ['autor' => $autorId]);
        return $stmt->fetch();
    }
}
