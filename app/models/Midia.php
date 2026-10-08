<?php
class Midia
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function cadastrar(array $imagem, int $usuarioId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO midias (nome_arquivo, caminho, tipo_mime, tamanho_bytes, enviado_por) VALUES (:nome, :caminho, :mime, :tamanho, :usuario)');
        $stmt->execute(['nome' => $imagem['nome_arquivo'], 'caminho' => $imagem['caminho'], 'mime' => $imagem['tipo_mime'], 'tamanho' => $imagem['tamanho_bytes'], 'usuario' => $usuarioId]);
    }

    public function listar(?int $usuarioId = null): array
    {
        $sql = 'SELECT m.*, u.nome AS autor_nome FROM midias m INNER JOIN usuarios u ON m.enviado_por = u.id';
        if ($usuarioId !== null) $sql .= ' WHERE m.enviado_por = :usuario';
        $sql .= ' ORDER BY m.criado_em DESC, m.id DESC LIMIT 100';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($usuarioId === null ? [] : ['usuario' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function removerSemUso(string $caminho): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM midias WHERE caminho = :caminho AND NOT EXISTS (SELECT 1 FROM conteudos WHERE imagem_capa = :imagem)');
        $stmt->execute(['caminho' => $caminho, 'imagem' => $caminho]);
        return $stmt->rowCount() > 0;
    }
}
