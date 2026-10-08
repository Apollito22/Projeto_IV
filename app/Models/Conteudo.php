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
        $stmt = $this->pdo->prepare("SELECT c.id, c.titulo, c.slug, c.resumo, c.corpo, c.imagem_capa, c.publicado_em,
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
}
