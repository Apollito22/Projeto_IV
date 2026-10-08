<?php
$titulo_pagina = $titulo_pagina ?? 'Administração';
require_once __DIR__ . '/../layout/header.php'; ?>
<div class="admin-shell container">
  <aside class="admin-menu" aria-label="Administração">
    <strong>Painel administrativo</strong>
    <p><?= e($_SESSION['usuario']['nome']) ?> · <?= e($_SESSION['usuario']['perfil']) ?></p>
    <nav aria-label="Seções administrativas">
      <a href="admin.php">Dashboard</a>
      <a href="admin.php?acao=conteudos">Notícias</a>
      <a href="admin.php?acao=novo">Nova notícia</a>
      <?php if ($_SESSION['usuario']['perfil'] === 'admin'): ?><a href="admin.php?acao=categorias">Categorias</a><?php endif; ?>
      <a href="admin.php?acao=midias">Imagens</a>
      <a href="admin.php?acao=links">Links</a>
      <a href="index.php">Ver portal</a>
    </nav>
  </aside>
  <section class="admin-content" aria-labelledby="titulo-admin">
    <h1 id="titulo-admin"><?= e($titulo_pagina) ?></h1>
    <?php if (isset($_SESSION['sucesso'])): ?>
      <p class="alert alert-success message success" role="status"><?= e($_SESSION['sucesso']) ?></p>
      <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>
