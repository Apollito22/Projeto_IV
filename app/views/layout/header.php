<?php
if (!isset($categorias_menu)) {
    $categorias_menu = [];
    try {
        $categorias_menu = (new Categoria(Database::conectar()))->listarAtivas();
    } catch (PDOException $erro) {
        error_log($erro->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Portal acadêmico de Sistemas de Informação da ESUCRI: projetos, ações sociais, eventos e produções científicas.">
  <title><?= e($titulo_pagina ?? 'Página Inicial') ?> | Portal SI — ESUCRI</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<a class="pular-conteudo" href="#conteudo-principal">Ir para o conteúdo</a>
<header class="cabecalho-principal">
  <div class="container container-flex">
    <div class="marca">
      <a href="index.php"><span class="marca-sigla">SI</span><span class="marca-nome">Portal Acadêmico</span></a>
    </div>
    <nav class="navegacao-global" aria-label="Menu principal">
      <ul class="menu-lista">
        <li><a href="index.php">Início</a></li>
        <?php foreach ($categorias_menu as $cat): ?>
          <li><a href="categoria.php?slug=<?= e(rawurlencode($cat['slug'])) ?>"<?= isset($categoria_atual) && $categoria_atual['slug'] === $cat['slug'] ? ' aria-current="page"' : '' ?>><?= e($cat['nome']) ?></a></li>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['usuario'])): ?>
          <li><a href="<?= pode_gerenciar() ? 'admin.php' : 'home.php' ?>">Painel</a></li>
          <li>
            <form action="logout.php" method="post">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <button type="submit" class="btn btn-primary btn-acesso">Sair</button>
            </form>
          </li>
        <?php else: ?>
          <li><a href="login.php" class="btn btn-primary btn-acesso">Acessar</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
<main id="conteudo-principal" class="conteudo-principal">
