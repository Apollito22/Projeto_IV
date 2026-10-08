<?php
class Categoria
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarAtivas(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, slug, descricao, icone FROM categorias WHERE ativo = TRUE ORDER BY ordem ASC, id ASC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorSlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, slug, descricao FROM categorias WHERE slug = :slug AND ativo = TRUE LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function cadastrar(string $nome, string $slug, string $descricao): int
    {
        $stmt = $this->pdo->prepare('LOCK TABLE categorias IN SHARE ROW EXCLUSIVE MODE');
        $stmt->execute();
        $stmt = $this->pdo->prepare('INSERT INTO categorias (id, nome, slug, descricao) SELECT COALESCE(MAX(id), 0) + 1, :nome, :slug, :descricao FROM categorias RETURNING id');
        $stmt->execute(['nome' => $nome, 'slug' => $slug, 'descricao' => $descricao === '' ? null : $descricao]);
        return (int) $stmt->fetchColumn();
    }
}
