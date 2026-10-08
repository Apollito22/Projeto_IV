<?php
$conteudos = $conteudos ?? [];
require __DIR__ . '/cabecalho.php'; ?>
<div class="admin-heading"><p>Crie, revise, publique ou arquive os textos do portal.</p><a class="btn btn-primary btn-acesso" href="admin.php?acao=novo">Nova notícia</a></div>
<?php if (!$conteudos): ?><p class="alerta-vazio">Nenhuma notícia cadastrada.</p><?php else: ?>
<div class="admin-table-wrap">
<table class="table table-striped align-middle admin-table">
  <caption>Até 100 notícias mais recentes</caption>
  <thead><tr><th scope="col">Título</th><th scope="col">Categoria</th><th scope="col">Autor</th><th scope="col">Status</th><th scope="col">Ações</th></tr></thead>
  <tbody>
    <?php foreach ($conteudos as $item): ?>
      <tr><td><?= e($item['titulo']) ?></td><td><?= e($item['categoria_nome']) ?></td><td><?= e($item['autor_nome']) ?></td><td><span class="admin-status"><?= e($item['status']) ?></span></td>
      <td class="admin-actions"><a href="admin.php?acao=editar&amp;id=<?= e((string) $item['id']) ?>">Editar</a><a href="admin.php?acao=excluir&amp;id=<?= e((string) $item['id']) ?>">Excluir</a><?php if ($item['status'] === 'publicado'): ?><a href="conteudo.php?slug=<?= e(rawurlencode($item['slug'])) ?>">Visualizar</a><?php endif; ?></td></tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>
<?php require __DIR__ . '/rodape.php'; ?>
