<?php
class AuthController
{
    public function processar(bool $cadastro): array
    {
        if (!validar_csrf()) {
            return ['O formulário expirou. Atualize a página e tente novamente.'];
        }
        $nome = trim(campo('nome'));
        $email = strtolower(trim(campo('email')));
        $senha = campo('senha');
        $erros = [];
        if (strpos($senha, "\0") !== false) {
            $erros[] = 'A senha contém um caractere inválido.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            $erros[] = 'Informe um e-mail válido.';
        }
        if ($cadastro) {
            $erros = array_merge($erros, $this->validarCadastro($nome, $senha));
        } elseif ($senha === '' || strlen($senha) > 72) {
            $erros[] = 'E-mail ou senha incorretos, ou usuário inativo.';
        }
        if ($erros) {
            return $erros;
        }
        try {
            $model = new Usuario(Database::conectar());
            $usuario = $model->buscarPorEmail($email);
            if ($cadastro) {
                if ($usuario) {
                    return ['Este e-mail já está cadastrado.'];
                }
                $model->cadastrar($nome, $email, $senha);
                $_SESSION['sucesso'] = 'Cadastro realizado! Entre com seu e-mail e senha.';
                redirecionar('login.php');
            }
            if (!$usuario || !in_array($usuario['ativo'], [true, 1, '1', 't', 'true'], true) || !password_verify($senha, $usuario['senha_hash'])) {
                return ['E-mail ou senha incorretos, ou usuário inativo.'];
            }
            if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
                $model->atualizarSenha((int) $usuario['id'], $senha);
            }
            session_regenerate_id(true);
            unset($usuario['senha_hash']);
            $_SESSION['usuario'] = $usuario;
            $_SESSION['ultimo_acesso'] = time();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            redirecionar(pode_gerenciar() ? 'admin.php' : 'home.php');
        } catch (PDOException $erro) {
            $sqlstate = $erro->getCode();
            if ($cadastro && ($sqlstate === '23505' || ($erro->errorInfo[0] ?? '') === '23505')) {
                return ['Este e-mail já está cadastrado.'];
            }
            error_log($erro->getMessage());
            return ['Não foi possível acessar o banco de dados. Tente novamente em instantes.'];
        }

        return [];
    }

    private function validarCadastro(string $nome, string $senha): array
    {
        $erros = [];
        if (!mb_check_encoding($nome, 'UTF-8') || strpos($nome, "\0") !== false || mb_strlen($nome, 'UTF-8') < 3 || mb_strlen($nome, 'UTF-8') > 150) {
            $erros[] = 'O nome deve ter entre 3 e 150 caracteres.';
        }
        if (strlen($senha) < 8 || strlen($senha) > 72) {
            $erros[] = 'A senha deve ter entre 8 e 72 bytes (acentos podem ocupar mais de um byte).';
        }
        if ($senha !== campo('confirmar_senha')) {
            $erros[] = 'A confirmação de senha não confere.';
        }
        return $erros;
    }

    public function primeiroAdmin(): void
    {
        $pdo = Database::conectar();
        $usuario = new Usuario($pdo);
        $erros = [];
        $mensagem = '';
        $disponivel = PrimeiroAdmin::acessoLocal() && PrimeiroAdmin::disponivel($usuario);
        if (!$disponivel) {
            http_response_code(403);
            $mensagem = PrimeiroAdmin::acessoLocal() ? 'O cadastro do primeiro administrador já foi encerrado.' : 'O cadastro inicial deve ser realizado no computador do servidor.';
        } elseif (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
            header('Allow: GET, POST');
            http_response_code(405);
            $disponivel = false;
            $mensagem = 'Use o formulário para realizar o cadastro.';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validar_csrf()) {
                http_response_code(403);
                $erros[] = 'O formulário expirou. Atualize a página e tente novamente.';
            } else {
                $nome = trim(campo('nome'));
                $email = strtolower(trim(campo('email')));
                $senha = campo('senha');
                $erros = $this->validarCadastro($nome, $senha);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) $erros[] = 'Informe um e-mail válido.';
                if (strpos($senha, "\0") !== false) $erros[] = 'A senha contém um caractere inválido.';
                if (!$erros) {
                    try {
                        PrimeiroAdmin::cadastrar($pdo, $nome, $email, $senha);
                        $_SESSION['sucesso'] = 'Administrador criado. O cadastro inicial foi encerrado. Entre com seu e-mail e senha.';
                        redirecionar('login.php');
                    } catch (LogicException $erro) {
                        http_response_code(403);
                        $disponivel = false;
                        $mensagem = $erro->getMessage();
                    } catch (PDOException $erro) {
                        error_log($erro->getMessage());
                        $erros[] = $erro->getCode() === '23505' ? 'Este e-mail já está cadastrado. Use outro e-mail.' : 'Não foi possível cadastrar o administrador. Tente novamente.';
                    } catch (Throwable $erro) {
                        error_log($erro->getMessage());
                        $erros[] = 'Não foi possível concluir o cadastro. Tente novamente.';
                    }
                }
            }
        }
        require __DIR__ . '/../views/admin/primeiro_admin.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_destroy();
        redirecionar('index.php');
    }
}
