# Front-end

As páginas usam PHP, HTML5 semântico e Bootstrap CSS local e CSS próprio, sem JavaScript.

## Layout

`app/views/layout/header.php` abre o documento, exibe o menu dinâmico e abre o único `main`. `footer.php` fecha o `main`, apresenta os créditos e encerra o documento. As views reutilizam ambos com `require_once`.

## Portal público

O Front Controller `public/index.php` encaminha as rotas aos controllers. A home apresenta as nove publicações mais recentes, com capa quando disponível. Os cartões levam à leitura do conteúdo publicado, com texto escapado e link relacionado. O menu lista as categorias ativas do PostgreSQL.

## Autenticação e administração

Visitantes recebem o link Acessar. Usuários autenticados recebem Painel e Sair; a saída usa POST e CSRF. Login e cadastro_usuarios.php ficam em `app/views/admin`.

O painel administrativo possui navegação lateral, dashboard, cadastro de categorias, listagem de notícias, formulário de criação e edição, confirmação de exclusão, galeria de imagens e gerenciamento de links. Os formulários funcionam com envios PHP, sem JavaScript.

## Responsividade

A grade pública usa Flexbox: três colunas em desktop, duas até 1024px e uma até 768px. No celular, o menu e o rodapé ficam verticais. A navegação administrativa fica acima do conteúdo e os campos ocupam a largura disponível. Tabelas possuem rolagem dentro do próprio bloco. A paleta original permanece nas variáveis CSS.
