<?php require_once __DIR__ . '/../layout/header.php'; ?>
<section class="secao-hero" aria-labelledby="titulo-hero">
  <div class="container">
    <p class="hero-identificacao">Sistemas de Informação · ESUCRI</p>
    <h1 id="titulo-hero"><?= e($categoria_atual['nome'] ?? 'Inovação e Extensão Universitária em Sistemas de Informação') ?></h1>
    <p><?= e($categoria_atual['descricao'] ?? 'Acompanhe os projetos, as produções científicas e o impacto social do curso na comunidade regional.') ?></p>
    <a class="hero-link" href="#publicacoes">Explore as publicações &rarr;</a>
  </div>
</section>
<section id="publicacoes" class="secao-publicacoes container" aria-labelledby="titulo-publicacoes">
  <h2 id="titulo-publicacoes" class="subtitulo-secao"><?= $categoria_atual === null ? 'Publicações Recentes' : 'Publicações da categoria' ?></h2>
  <?php if ($erro_pagina !== ''): ?>
    <p class="message error" role="alert"><?= e($erro_pagina) ?></p>
  <?php elseif (empty($conteudos_recentes)): ?>
    <p class="alerta-vazio">Nenhuma publicação foi disponibilizada até o momento.</p>
  <?php else: ?>
    <div class="grade-publicacoes">
      <?php foreach ($conteudos_recentes as $item): ?>
        <article class="cartao-conteudo">
          <header class="cartao-cabecalho">
            <a class="cartao-categoria" href="categoria.php?slug=<?= e(rawurlencode($item['categoria_slug'])) ?>"><?= e($item['categoria_nome']) ?></a>
            <h3 class="cartao-titulo"><?= e($item['titulo']) ?></h3>
          </header>
          <p class="cartao-resumo"><?= e($item['resumo']) ?></p>
          <p class="cartao-autor">Por <?= e($item['autor_nome']) ?></p>
          <footer class="cartao-rodape">
            <time datetime="<?= e(date('Y-m-d\TH:i:s', strtotime($item['publicado_em']))) ?>"><?= e(date('d/m/Y', strtotime($item['publicado_em']))) ?></time>
            <a href="conteudo.php?slug=<?= e(rawurlencode($item['slug'])) ?>" class="cartao-link" aria-label="<?= e('Ler mais: ' . $item['titulo']) ?>">Ler mais &rarr;</a>
          </footer>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<section class="secao-projeto container" aria-labelledby="titulo-projeto">
  <h2 id="titulo-projeto" class="subtitulo-secao">Projeto de Extensão IV</h2>
  <p>O Portal SI aproxima a comunidade acadêmica e a sociedade por meio da divulgação de ações sociais, atividades complementares, projetos de extensão, eventos acadêmicos, produção científica e notícias do curso.</p>
  <p>Um espaço para compartilhar conhecimento e apresentar as iniciativas dos estudantes de Sistemas de Informação da ESUCRI.</p>
</section>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
