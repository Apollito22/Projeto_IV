<?php
$cadastro = $cadastro ?? false;
$erros = $erros ?? [];
$cadastro_admin_disponivel = $cadastro_admin_disponivel ?? false;
$titulo_pagina = $cadastro ? 'Cadastro' : 'Login';
require_once __DIR__ . '/../layout/header.php';
?>
<div class="screen-center">
  <section class="form-card login-card" aria-labelledby="titulo">
    <div class="avatar brand" aria-hidden="true">SI</div>
    <header class="auth-heading">
      <h1 id="titulo"><?= $cadastro ? 'Crie sua conta' : 'Bem-vindo!' ?></h1>
      <p><?= $cadastro ? 'Cadastre-se para acessar o Portal SI.' : 'Entre com seu e-mail e senha.' ?></p>
    </header>
    <?php if (!empty($erros)): ?>
      <div class="alert alert-danger message error" role="alert"><ul>
        <?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
      </ul></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['sucesso'])): ?>
      <p class="alert alert-success message success" role="status"><?= e($_SESSION['sucesso']) ?></p>
      <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>
    <form class="form-group" action="<?= $cadastro ? 'cadastro_usuarios.php' : 'login.php' ?>" method="post">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
      <?php if ($cadastro): ?>
        <label for="nome">Nome completo</label>
        <input class="form-control" id="nome" name="nome" autocomplete="name" minlength="3" maxlength="150" value="<?= e(campo('nome')) ?>" required>
      <?php endif; ?>
      <label for="email">E-mail</label>
      <input class="form-control" type="email" id="email" name="email" autocomplete="username" maxlength="255" placeholder="voce@exemplo.com" value="<?= e(campo('email')) ?>" required>
      <label for="senha">Senha</label>
      <input class="form-control" type="password" id="senha" name="senha" autocomplete="<?= $cadastro ? 'new-password' : 'current-password' ?>" <?= $cadastro ? 'minlength="8" aria-describedby="senha-ajuda"' : '' ?> required>
      <?php if ($cadastro): ?>
        <small id="senha-ajuda">Use pelo menos 8 caracteres. Limite de 72 bytes.</small>
        <label for="confirmar_senha">Confirme a senha</label>
        <input class="form-control" type="password" id="confirmar_senha" name="confirmar_senha" autocomplete="new-password" minlength="8" required>
      <?php endif; ?>
      <button class="btn btn-primary" type="submit"><?= $cadastro ? 'Criar conta' : 'Entrar' ?></button>
    </form>
    <a class="login-register" href="<?= $cadastro ? 'login.php' : 'cadastro_usuarios.php' ?>"><?= $cadastro ? 'Já tenho uma conta — entrar' : 'Ainda não tem conta? Cadastre-se' ?></a>
    <?php if (!$cadastro && $cadastro_admin_disponivel): ?>
      <a class="login-register" href="primeiro_admin.php">Criar o primeiro administrador</a>
    <?php endif; ?>
  </section>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>