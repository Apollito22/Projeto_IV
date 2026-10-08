# Front-end

As páginas usam PHP, HTML5 semântico e um único CSS, sem JavaScript.

## Layout

`app/Views/layout/header.php` abre o documento, exibe o menu dinâmico e abre o único `main`. `footer.php` fecha o `main`, apresenta os créditos e encerra o documento. As views reutilizam ambos com `require_once`.

## Home pública

`public/index.php` chama `HomeController`, que consulta as categorias e as nove publicações mais recentes. A view exibe hero institucional, cartões e apresentação do Projeto de Extensão IV. Nenhuma publicação fictícia é inserida no banco.

## Navegação e autenticação

O menu lista categorias ativas do PostgreSQL. Visitantes recebem o link Acessar para `login.php`; usuários autenticados recebem Painel e Sair. A saída usa POST e CSRF.

A categoria selecionada filtra a grade. Os cartões levam à leitura do conteúdo publicado. Login, cadastro e painel mantêm a lógica da etapa anterior.

## Responsividade

A grade usa Flexbox: três colunas em desktop, duas até 1024px e uma até 768px. No celular, o menu e o rodapé ficam verticais. A paleta original permanece nas variáveis CSS.
