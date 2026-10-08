<?php
$erros = $erros ?? [];
$disponivel = $disponivel ?? false;
$mensagem = $mensagem ?? 'O cadastro inicial está encerrado.';
$titulo_pagina = 'Primeiro administrador';
require_once __DIR__ . '/../layout/header.php';
?>
<div class="screen-center">
  <section class="form-card login-card" aria-labelledby="titulo">
    <div class="avatar brand" aria-hidden="true">SI</div>
    <header class="auth-heading">
      <h1 id="titulo">Primeiro administrador</h1>
      <p><?= $disponivel ? 'Crie o administrador do portal. Este cadastro será encerrado após a criação.' : e($mensagem) ?></p>
    </header>
    <?php if ($erros): ?><div class="alert alert-danger message error" role="alert"><ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <?php if ($disponivel): ?>
      <form class="form-group" action="primeiro_admin.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <label for="nome">Nome completo</label>
        <input class="form-control" id="nome" name="nome" minlength="3" maxlength="150" autocomplete="name" value="<?= e(campo('nome')) ?>" required>
        <label for="email">E-mail</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="255" autocomplete="username" value="<?= e(campo('email')) ?>" required>
        <label for="senha">Senha</label>
        <input class="form-control" type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required aria-describedby="senha-ajuda">
        <small id="senha-ajuda">Use pelo menos 8 caracteres. Limite de 72 bytes.</small>
        <label for="confirmar_senha">Confirme a senha</label>
        <input class="form-control" type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required>
        <button class="btn btn-primary" type="submit">Criar administrador</button>
      </form>
    <?php endif; ?>
    <a class="login-register" href="login.php">Voltar ao login</a>
  </section>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
