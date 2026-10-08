INSERT INTO categorias (id, nome, slug, descricao, ordem) VALUES
    (1, 'Ações Sociais', 'acoes-sociais', 'Iniciativas do curso em benefício da comunidade.', 1),
    (2, 'Atividades Complementares', 'atividades-complementares', 'Atividades que complementam a formação acadêmica.', 2),
    (3, 'Projetos de Extensão', 'projetos-de-extensao', 'Projetos que conectam o conhecimento acadêmico à sociedade.', 3),
    (4, 'Eventos Acadêmicos', 'eventos-academicos', 'Encontros, palestras e eventos do curso.', 4),
    (5, 'Produção Científica', 'producao-cientifica', 'Pesquisas e produções científicas da comunidade acadêmica.', 5),
    (6, 'Notícias Gerais', 'noticias-gerais', 'Notícias e comunicados de Sistemas de Informação.', 6)
ON CONFLICT DO NOTHING;
