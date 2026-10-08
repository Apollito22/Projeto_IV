<?php
require_once __DIR__ . '/../config/bootstrap.php';
exigir_login();
$usuario = $_SESSION['usuario'];
$titulo_pagina = 'Painel';
require_once __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="dashboard">
  <aside class="sidebar">
    <div class="avatar brand" aria-hidden="true">SI</div>
    <p class="profile-role">Perfil: <?= e(ucfirst($usuario['perfil'])) ?></p>
    <div class="profile-data">
      <strong><?= e($usuario['nome']) ?></strong>
      <span><?= e($usuario['email']) ?></span>
      <small>Matrícula no portal: <?= (int) $usuario['id'] ?></small>
    </div>
    <form action="logout.php" method="post">
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
      <button class="btn btn-primary" type="submit">Sair</button>
    </form>
  </aside>
  <section class="dashboard-content" aria-labelledby="titulo-painel">
    <div class="content-placeholder">
      <p>PORTAL DE COMUNICAÇÃO SI</p>
      <h1 id="titulo-painel">Olá, <?= e($usuario['nome']) ?>!</h1>
      <p>Você está conectado. Seja bem-vindo à sua página inicial.</p>
      <article class="welcome-card">
        <h2>Seu espaço acadêmico</h2>
        <p>Acompanhe as publicações e iniciativas do curso na página pública do portal.</p>
        <p><a class="cartao-link" href="index.php">Ver publicações &rarr;</a></p>
      </article>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../app/Views/layout/footer.php'; ?>