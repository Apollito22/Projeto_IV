<?php
$erros = $erros ?? [];
$categorias = $categorias ?? [];
$categoria = $categoria ?? ['nome' => '', 'slug' => '', 'descricao' => ''];
require __DIR__ . '/cabecalho.php'; ?>
<p>Cadastre as categorias que serão usadas nas notícias e no menu do portal.</p>
<?php if ($erros): ?><div class="alert alert-danger message error" role="alert"><ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form class="admin-form" action="admin.php?acao=categorias" method="post">
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
  <label for="nome">Nome</label><input class="form-control" id="nome" name="nome" value="<?= e($categoria['nome']) ?>" minlength="3" maxlength="100" required>
  <label for="slug">Identificador do link</label><input class="form-control" id="slug" name="slug" value="<?= e($categoria['slug']) ?>" maxlength="150" pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="Gerado pelo nome se ficar vazio">
  <label for="descricao">Descrição</label><textarea class="form-control" id="descricao" name="descricao" rows="3" maxlength="5000"><?= e($categoria['descricao']) ?></textarea>
  <div class="admin-actions"><button class="btn btn-primary btn-acesso" type="submit">Cadastrar categoria</button><a href="admin.php?acao=novo">Criar notícia</a></div>
</form>
<h2>Categorias ativas</h2>
<?php if (!$categorias): ?><p class="alerta-vazio">Nenhuma categoria cadastrada.</p><?php else: ?>
<ul class="admin-recentes"><?php foreach ($categorias as $item): ?><li><strong><?= e($item['nome']) ?></strong><span><?= e($item['slug']) ?></span></li><?php endforeach; ?></ul>
<?php endif; ?>
<?php require __DIR__ . '/rodape.php'; ?>
