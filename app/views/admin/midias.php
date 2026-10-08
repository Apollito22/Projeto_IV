<?php
$midias = $midias ?? [];
require __DIR__ . '/cabecalho.php'; ?>
<p>As imagens enviadas pelos formulários de notícias ficam registradas aqui.</p>
<?php if (!$midias): ?><p class="alerta-vazio">Nenhuma imagem enviada. Envie uma capa ao criar ou editar uma notícia.</p><?php endif; ?>
<div class="admin-gallery">
  <?php foreach ($midias as $item): ?>
    <figure><?php if (imagem_local($item['caminho'])): ?><img src="<?= e($item['caminho']) ?>" alt="<?= e($item['nome_arquivo']) ?>" loading="lazy"><?php endif; ?><figcaption><?= e($item['nome_arquivo']) ?><br><?= e(number_format((int) $item['tamanho_bytes'] / 1024, 1, ',', '.')) ?> KB · <?= e($item['autor_nome']) ?></figcaption></figure>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/rodape.php'; ?>
