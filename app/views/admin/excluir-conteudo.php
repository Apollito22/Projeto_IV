<?php
$conteudo = $conteudo ?? ['id' => '', 'titulo' => ''];
require __DIR__ . '/cabecalho.php'; ?>
<p>Deseja excluir a notícia <strong><?= e($conteudo['titulo']) ?></strong>?</p>
<p>O texto será removido e deixará de aparecer no portal. A imagem associada também será removida se não estiver em uso.</p>
<form class="admin-actions" method="post" action="admin.php?acao=excluir">
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
  <input type="hidden" name="id" value="<?= e((string) $conteudo['id']) ?>">
  <button class="btn btn-danger btn-acesso admin-danger" type="submit">Confirmar exclusão</button><a href="admin.php?acao=conteudos">Cancelar</a>
</form>
<?php require __DIR__ . '/rodape.php'; ?>
