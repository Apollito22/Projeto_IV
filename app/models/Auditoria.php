<?php
class Auditoria
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function registrar(int $usuarioId, string $acao, int $conteudoId, array $detalhes = [], string $tabela = 'conteudos'): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if (!is_string($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) $ip = null;
        $stmt = $this->pdo->prepare("INSERT INTO auditoria (usuario_id, acao, tabela_afetada, registro_id, detalhes, ip_origem) VALUES (:usuario, :acao, :tabela, :registro, CAST(:detalhes AS JSONB), CAST(:ip AS INET))");
        $stmt->execute(['usuario' => $usuarioId, 'acao' => $acao, 'tabela' => $tabela, 'registro' => $conteudoId, 'detalhes' => json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), 'ip' => $ip]);
    }
}
