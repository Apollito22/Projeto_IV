# Portal SI — Aula 09

Portal acadêmico em PHP nativo, HTML5, CSS3 e PostgreSQL, seguindo a apostila da Aula 09. Não utiliza JavaScript.

## Executar no XAMPP

1. Mantenha a pasta em `/Applications/XAMPP/xamppfiles/htdocs/portal-si_equipe_Fatal_etapa1` no Mac ou na pasta `htdocs` do XAMPP no Windows.
2. Inicie o Apache e o serviço PostgreSQL.
3. Em um banco novo chamado `portal_si`, execute `database/schema.sql` e depois `database/seed.sql`. Em um banco que já possui as oito tabelas, execute apenas `database/seed.sql` se as categorias ainda não estiverem cadastradas. Não execute novamente o schema sobre tabelas existentes.
4. Confira `config/database.php`: por padrão, host `127.0.0.1`, porta `5432`, banco `portal_si`, usuário `postgres` e senha `admin`. A conexão também aceita DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASSWORD.
5. Abra `http://localhost/portal-si_equipe_Fatal_etapa1/public/`.

Requer PHP 7.4 ou superior, com `pdo_pgsql` e `mbstring`. Em hospedagem, configure o diretório público do servidor como a pasta `public`, mantendo `app`, `config` e `database` fora do acesso público.

O arquivo `database/postgres.sql` cria apenas a tabela de usuários para a etapa anterior. Para uma instalação completa da Aula 09, use o schema com as oito tabelas.

## Rotas

- `public/index.php`: home pública com hero, nove publicações recentes e apresentação do projeto.
- `public/login.php`: autenticação existente por e-mail e senha.
- `public/cadastro.php`: cadastro público com perfil aluno.
- `public/home.php`: painel do usuário autenticado.
- `public/logout.php`: saída por POST e token CSRF.
- `public/categoria.php?slug=acoes-sociais`: filtro de publicações por categoria.
- `public/conteudo.php?slug=slug-da-publicacao`: leitura do conteúdo publicado.

As duas rotas de leitura reutilizam os models e o layout para que os links do menu e dos cartões funcionem. Conteúdos inexistentes ou não publicados retornam 404.

## Organização

- `app/Models/Categoria.php`: busca das categorias ativas pelo campo `ativo`, em ordem de `ordem` e `id`.
- `app/Models/Conteudo.php`: consultas preparadas com junções de categorias e usuários pelo campo `autor_id`.
- `app/Controllers/HomeController.php`: dados e tratamento de erros da home.
- `app/Controllers/ConteudoController.php`: leitura de publicação.
- `app/Views/layout/header.php` e `footer.php`: estrutura compartilhada com `require_once`.
- `app/Views/home/index.php`: conteúdo da home.
- `app/Views/auth.php`: formulário compartilhado de login e cadastro.
- `public/assets/css/style.css`: estilos do portal, formulários e painel.

Foram mantidos os nomes `Models` e `Views` já existentes no projeto. As consultas usam `ativo` e `autor_id`, conforme o schema, e a navegação usa a sessão `usuario` do login existente.

## Dados iniciais

O seed contém as seis categorias da apostila: Ações Sociais, Atividades Complementares, Projetos de Extensão, Eventos Acadêmicos, Produção Científica e Notícias Gerais. Pode ser executado novamente sem duplicar registros. Não cria usuários ou publicações de exemplo.

A home exibe apenas conteúdos com `status = 'publicado'`, data de publicação preenchida e categoria ativa, ordenados por data decrescente. Enquanto não houver conteúdos, apresenta uma mensagem de lista vazia. Novos registros de conteúdo devem receber um `id` válido, conforme a estrutura atual da tabela.

## Verificar

Confira a home sem login, o menu com as seis categorias, o cadastro e a autenticação, o painel protegido e a saída. Teste a grade em desktop, tablet até 1024px e celular até 768px. A grade possui três, duas e uma coluna, respectivamente; o menu se torna vertical no celular.

Todas as consultas ficam nos models. Saídas dinâmicas usam `e()` e os parâmetros externos são vinculados pelo PDO.
