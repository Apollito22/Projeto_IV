<?php
class PrimeiroAdmin
{
    private static function arquivo(): string
    {
        return getenv('PORTAL_PRIMEIRO_ADMIN_ESTADO') ?: __DIR__ . '/../../config/primeiro_admin.estado';
    }

    public static function acessoLocal(): bool
    {
        return in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true);
    }

    public static function disponivel(Usuario $usuario): bool
    {
        $arquivo = self::arquivo();
        if (is_file($arquivo)) {
            $estado = file_get_contents($arquivo);
            if ($estado === false || trim($estado) !== '') return false;
        }
        return !$usuario->possuiAdministrador();
    }

    public static function cadastrar(PDO $pdo, string $nome, string $email, string $senha): void
    {
        $arquivo = @fopen(self::arquivo(), 'c+');
        if ($arquivo === false) throw new RuntimeException('Não foi possível registrar o encerramento do cadastro.');
        $gravado = false;
        try {
            if (!flock($arquivo, LOCK_EX)) throw new RuntimeException('Não foi possível iniciar o cadastro.');
            rewind($arquivo);
            $estado = stream_get_contents($arquivo);
            if ($estado === false || trim($estado) !== '') throw new LogicException('O cadastro do primeiro administrador já foi encerrado.');
            $pdo->beginTransaction();
            $usuario = new Usuario($pdo);
            $usuario->bloquearCadastroAdministrador();
            if ($usuario->possuiAdministrador()) throw new LogicException('O primeiro administrador já foi cadastrado.');
            $usuario->cadastrarAdministrador($nome, $email, $senha);
            $gravado = true;
            $estado = "concluido\n";
            if (!rewind($arquivo) || !ftruncate($arquivo, 0) || fwrite($arquivo, $estado) !== strlen($estado) || !fflush($arquivo)) {
                throw new RuntimeException('Não foi possível registrar o encerramento do cadastro.');
            }
            $pdo->commit();
        } catch (Throwable $erro) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($gravado) { rewind($arquivo); ftruncate($arquivo, 0); fflush($arquivo); }
            throw $erro;
        } finally {
            flock($arquivo, LOCK_UN);
            fclose($arquivo);
        }
    }
}
