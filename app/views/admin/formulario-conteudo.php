<?php
$erros = $erros ?? [];
$categorias = $categorias ?? [];
$conteudo = $conteudo ?? ['id' => '', 'titulo' => '', 'slug' => '', 'resumo' => '', 'corpo' => '', 'categoria_id' => '', 'status' => 'rascunho', 'destaque' => false, 'link_youtube' => '', 'imagem_capa' => null];
require __DIR__ . '/cabecalho.php'; ?>
<?php if ($erros): ?><div class="alert alert-danger message error" role="alert"><ul><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<?php if (!$categorias): ?><p class="alert alert-warning">É necessário cadastrar uma categoria antes de salvar a notícia.<?php if ($_SESSION['usuario']['perfil'] === 'admin'): ?> <a href="admin.php?acao=categorias">Cadastrar categoria</a><?php else: ?> Solicite o cadastro ao administrador.<?php endif; ?></p><?php endif; ?>
<form class="admin-form" action="admin.php?acao=salvar" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
  <input type="hidden" name="id" value="<?= e((string) $conteudo['id']) ?>">
  <label for="titulo">Título</label><input class="form-control" id="titulo" name="titulo" value="<?= e($conteudo['titulo']) ?>" minlength="3" maxlength="255" required>
  <label for="slug">Identificador do link</label><input class="form-control" id="slug" name="slug" value="<?= e($conteudo['slug']) ?>" maxlength="255" pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="Gerado pelo título se ficar vazio">
  <label for="categoria">Categoria</label><select class="form-select" id="categoria" name="categoria_id" required><option value="">Selecione</option><?php foreach ($categorias as $cat): ?><option value="<?= e((string) $cat['id']) ?>"<?= (string) $cat['id'] === (string) $conteudo['categoria_id'] ? ' selected' : '' ?>><?= e($cat['nome']) ?></option><?php endforeach; ?></select>
  <label for="resumo">Resumo</label><textarea class="form-control" id="resumo" name="resumo" rows="3" maxlength="1000"><?= e($conteudo['resumo']) ?></textarea>
  <label for="corpo">Texto da publicação</label><textarea class="form-control" id="corpo" name="corpo" rows="12" required><?= e($conteudo['corpo']) ?></textarea>
  <label for="status">Status</label><select class="form-select" id="status" name="status"><?php foreach (['rascunho' => 'Rascunho', 'publicado' => 'Publicado', 'arquivado' => 'Arquivado'] as $valor => $rotulo): ?><option value="<?= e($valor) ?>"<?= $conteudo['status'] === $valor ? ' selected' : '' ?>><?= e($rotulo) ?></option><?php endforeach; ?></select>
  <label class="admin-check"><input class="form-check-input" type="checkbox" name="destaque" value="1"<?= in_array($conteudo['destaque'], [true, 1, '1', 't'], true) ? ' checked' : '' ?>> Marcar como destaque</label>
  <label for="link_youtube">Link relacionado ou vídeo</label><input class="form-control" type="url" id="link_youtube" name="link_youtube" value="<?= e($conteudo['link_youtube']) ?>" maxlength="500" placeholder="https://">
  <label for="imagem">Imagem de capa</label><input class="form-control" type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp" aria-describedby="imagem-ajuda">
  <small id="imagem-ajuda">JPEG, PNG ou WebP, até 5 MB. Ao editar, deixe vazio para manter a imagem atual.</small>
  <?php if (imagem_local($conteudo['imagem_capa'])): ?><img class="admin-preview" src="<?= e($conteudo['imagem_capa']) ?>" alt="Capa atual"><label class="admin-check"><input class="form-check-input" type="checkbox" name="remover_imagem" value="1"> Remover imagem atual</label><?php endif; ?>
  <div class="admin-actions"><button class="btn btn-primary btn-acesso" type="submit">Salvar notícia</button><a href="admin.php?acao=conteudos">Cancelar</a></div>
</form>
<?php require __DIR__ . '/rodape.php'; ?>
