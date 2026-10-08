<?php
$resumo = $resumo ?? ['total' => 0, 'publicados' => 0, 'rascunhos' => 0, 'arquivados' => 0, 'links' => 0];
$conteudos = $conteudos ?? [];
require __DIR__ . '/cabecalho.php'; ?>
<div class="admin-stats">
  <?php foreach (['total' => 'Notícias', 'publicados' => 'Publicados', 'rascunhos' => 'Rascunhos', 'arquivados' => 'Arquivados', 'links' => 'Com link'] as $chave => $rotulo): ?>
    <article><strong><?= e((string) $resumo[$chave]) ?></strong><span><?= e($rotulo) ?></span></article>
  <?php endforeach; ?>
</div>
<div class="admin-heading"><h2>Notícias recentes</h2><a class="btn btn-primary btn-acesso" href="admin.php?acao=novo">Nova notícia</a></div>
<?php if (!$conteudos): ?><p class="alerta-vazio">Nenhuma notícia cadastrada. Comece criando uma publicação.</p><?php else: ?>
<ul class="admin-recentes">
  <?php foreach ($conteudos as $item): ?><li><a href="admin.php?acao=editar&amp;id=<?= e((string) $item['id']) ?>"><?= e($item['titulo']) ?></a><span><?= e($item['status']) ?></span></li><?php endforeach; ?>
</ul>
<?php endif; ?>
<?php require __DIR__ . '/rodape.php'; ?>
