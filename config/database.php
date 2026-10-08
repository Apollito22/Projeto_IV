<?php
class Database
{
    public static function conectar(): PDO
    {
        static $pdo = null;
        if ($pdo === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $porta = getenv('DB_PORT') ?: '5432';
            $banco = getenv('DB_NAME') ?: 'portal_si';
            $usuario = getenv('DB_USER') ?: 'postgres';
            $senha = getenv('DB_PASSWORD') ?: 'admin';

            $pdo = new PDO("pgsql:host=$host;port=$porta;dbname=$banco", $usuario, $senha, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return $pdo;
    }
}
