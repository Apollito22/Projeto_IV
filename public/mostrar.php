<?php
$erro_pagina = $erro_pagina ?? '';
$conteudo = $conteudo ?? [];
require_once __DIR__ . '/../layout/header.php';
?>
<section class="secao-publicacoes container" aria-labelledby="titulo-conteudo">
  <?php if (!empty($erro_pagina) || empty($conteudo)): ?>
    <h1 id="titulo-conteudo">Publicação indisponível</h1>
    <p class="message error" role="alert"><?= e($erro_pagina !== '' ? $erro_pagina : 'A publicação solicitada não está disponível.') ?></p>
  <?php else: ?>
    <article class="publicacao-completa">
      <header>
        <a class="cartao-categoria" href="categoria.php?slug=<?= e(rawurlencode($conteudo['categoria_slug'])) ?>"><?= e($conteudo['categoria_nome']) ?></a>
        <h1 id="titulo-conteudo"><?= e($conteudo['titulo']) ?></h1>
        <p class="cartao-autor">Por <?= e($conteudo['autor_nome']) ?> · <time datetime="<?= e(date('Y-m-d\TH:i:s', strtotime($conteudo['publicado_em']))) ?>"><?= e(date('d/m/Y', strtotime($conteudo['publicado_em']))) ?></time></p>
      </header>
      <?php if (!empty($conteudo['resumo'])): ?><p class="publicacao-resumo"><?= e($conteudo['resumo']) ?></p><?php endif; ?>
      <div class="publicacao-corpo"><?= e($conteudo['corpo']) ?></div>
    </article>
  <?php endif; ?>
  <a class="cartao-link voltar-link" href="index.php">&larr; Voltar ao início</a>
</section>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
