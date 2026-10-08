# portal-si

Portal acadêmico e painel administrativo em PHP nativo, HTML5, CSS3 e Bootstrap 5.3.8. O Bootstrap utiliza somente o CSS local em `public/assets/css/bootstrap.min.css`. Não há JavaScript.

## Executar no XAMPP

1. Coloque a pasta `portal-si` em `htdocs` e inicie Apache e PostgreSQL.
2. Crie um banco PostgreSQL chamado `portal_si` e execute somente `database/schema.sql`. As oito tabelas começam vazias. Não existe seed.sql no projeto.
3. Confira os parâmetros em `config/database.php`: host `127.0.0.1`, porta `5432`, banco `portal_si`, usuário `postgres` e senha `admin`. Também são aceitas as variáveis DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASSWORD.
4. Abra `http://localhost/portal-si/public/`.

Em um banco com estrutura anterior, `database/admin.sql` habilita IDs automáticos de conteúdos, mídias e auditoria sem inserir dados. Não execute novamente schema.sql sobre tabelas existentes. `database/postgres.sql` contém somente a tabela de usuários da etapa anterior; a instalação completa usa schema.sql.

Requer PHP 7.4 ou superior com pdo_pgsql, mbstring, fileinfo e gd. A pasta `public/assets/uploads` precisa permitir gravação pelo Apache. Em hospedagem, configure a raiz pública como `public`.

## Organização Mini-MVC

- `app/controllers`: AuthController, HomeController, ConteudoController e AdminController coordenam as solicitações.
- `app/models`: Usuario, Categoria, Conteudo, Midia e Auditoria concentram as consultas PDO.
- `app/helpers`: auth.php controla acesso por perfil; seguranca.php contém escape e CSRF; upload.php valida e salva imagens.
- `app/views/admin`: login, cadastro_usuarios.php e telas administrativas.
- `app/views/home`: página inicial e leitura de conteúdo.
- `app/views/layout`: header.php e footer.php compartilhados.
- `app/views/errors/403.php`: acesso negado.
- `config/database.php`: conexão PDO singleton.
- `config/session.php`: configuração da sessão.
- `public/index.php`: Front Controller. Os demais arquivos públicos encaminham para ele.

## Funcionalidades

- `public/index.php`: portal com publicações recentes.
- `public/categoria.php?slug=slug-da-categoria`: filtro por categoria.
- `public/conteudo.php?slug=slug-da-publicacao`: leitura de publicação.
- `public/login.php`: autenticação por e-mail.
- `public/cadastro_usuarios.php`: cadastro público com perfil aluno.
- `public/home.php`: painel conforme perfil.
- `public/admin.php`: dashboard, cadastro de categorias, CRUD de notícias, upload de capa, galeria e gerenciamento de links.
- `public/logout.php`: saída por POST com CSRF.

Administradores gerenciam todos os conteúdos. Editores gerenciam os próprios conteúdos e visualizam as próprias imagens. Alunos não acessam a administração. O perfil é verificado no banco a cada solicitação administrativa.

A instalação por schema.sql começa com todas as tabelas vazias. Dados e o estado do cadastro inicial do ambiente local não são incluídos no repositório. Não existe conta administrativa inicial. Para criar o primeiro administrador, abra `public/primeiro_admin.php` no navegador do próprio computador do servidor. O link também aparece no login enquanto o cadastro inicial está disponível. Depois da criação, o formulário é bloqueado e o link desaparece. O encerramento fica registrado em `config/primeiro_admin.estado`, fora do acesso público, e permanece mesmo se a conta for apagada do banco. Não há cadastro de administradores pelo painel. A pasta config precisa permitir a gravação desse registro pelo Apache. O administrador cadastra as categorias em `admin.php?acao=categorias` antes de criar as notícias. Os textos das notícias usam a tabela existente conteudos. O cadastro público mantém o perfil aluno.

O formulário permite escolher status, destaque e categoria; criar, editar e excluir texto; enviar, substituir e remover a capa; adicionar, alterar e remover o link relacionado. Exclusões exigem confirmação no formulário. As alterações ficam registradas em auditoria.

Imagens: JPEG, PNG ou WebP até 5 MB, com até 6000 pixels por lado e 12 milhões de pixels no total. Links HTTP e HTTPS usam o campo existente link_youtube.

As tabelas são usuarios, categorias, conteudos, tags, conteudo_tags, eventos, midias e auditoria. Conteúdos publicados com data preenchida e categoria ativa aparecem no portal.

## Bootstrap

CSS oficial: https://getbootstrap.com/docs/5.3/getting-started/download/

Os formulários usam form-control, form-select, form-check-input e btn; as mensagens usam alert; a tabela administrativa usa table. A aparência original é complementada por style.css. Nenhum componente depende de JavaScript.
