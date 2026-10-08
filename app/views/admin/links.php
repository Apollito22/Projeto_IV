<?php
$erros = $erros ?? [];
$conteudos = $conteudos ?? [];
require __DIR__ . '/cabecalho.php'; ?>
<p>Associe um link HTTP ou HTTPS a cada notícia. Para remover, deixe o campo vazio e salve.</p>
<?php if ($erros): ?><div class="alert alert-danger message error" role="alert"><ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<?php if (!$conteudos): ?><p class="alerta-vazio">Cadastre uma notícia para gerenciar seu link.</p><?php endif; ?>
<div class="admin-link-list">
  <?php foreach ($conteudos as $item): ?>
    <article><h2><?= e($item['titulo']) ?></h2>
      <form class="admin-form" action="admin.php?acao=links" method="post">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
        <label for="link-<?= e((string) $item['id']) ?>">Link</label><input class="form-control" type="url" id="link-<?= e((string) $item['id']) ?>" name="link_youtube" value="<?= e($item['link_youtube']) ?>" maxlength="500">
        <div class="admin-actions"><button class="btn btn-primary btn-acesso" type="submit">Salvar link</button><?php if (!empty($item['link_youtube']) && link_video_valido($item['link_youtube'])): ?><a href="<?= e($item['link_youtube']) ?>" target="_blank" rel="noopener noreferrer">Abrir link</a><?php endif; ?></div>
      </form>
    </article>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/rodape.php'; ?>
