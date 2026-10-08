<?php
$mensagem = $mensagem ?? 'Notícia indisponível.';
require __DIR__ . '/cabecalho.php'; ?>
<p class="alert alert-danger message error" role="alert"><?= e($mensagem) ?></p>
<a class="cartao-link" href="admin.php?acao=conteudos">Voltar às notícias</a>
<?php require __DIR__ . '/rodape.php'; ?>
