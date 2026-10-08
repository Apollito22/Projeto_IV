<?php
class Usuario
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function possuiAdministrador(): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE perfil = 'admin' LIMIT 1");
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    public function bloquearCadastroAdministrador(): void
    {
        $stmt = $this->pdo->prepare('LOCK TABLE usuarios IN SHARE ROW EXCLUSIVE MODE');
        $stmt->execute();
    }

    public function cadastrarAdministrador(string $nome, string $email, string $senha): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (:nome, :email, :senha_hash, 'admin')");
        $stmt->execute(['nome' => $nome, 'email' => $email, 'senha_hash' => password_hash($senha, PASSWORD_DEFAULT)]);
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public function cadastrar(string $nome, string $email, string $senha): bool
    {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)');
        return $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha_hash' => $senhaHash,
        ]);
    }

    public function atualizarSenha(int $id, string $senha): bool
    {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('UPDATE usuarios SET senha_hash = :senha_hash WHERE id = :id');
        return $stmt->execute([
            'senha_hash' => $senhaHash,
            'id' => $id,
        ]);
    }
}
