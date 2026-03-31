-- Mot de passe : admin123 (hashé avec bcrypt)
INSERT INTO users (username, mdp) VALUES
('admin', '$2y$10$S6gbcqi02qFNzO.kSpv0CuRJ4Bn06RIPsnqhCXr5QrXE0WwTbsp92');

INSERT INTO categories (nom, slug) VALUES
('Actualités', 'actualites'),
('Analyses', 'analyses'),
('Histoire', 'histoire');

INSERT INTO articles (titre, slug, resume, contenu, image_url, image_alt, meta_title, meta_description, is_published, category_id, author_id) VALUES
(
    'La situation politique en Iran en 2024',
    'situation-politique-iran-2024',
    'Un aperçu de l''évolution politique iranienne au cours de l''année 2024.',
    'L''Iran a traversé une période de tensions intenses en 2024. Entre les négociations sur le programme nucléaire, les manifestations internes et les relations avec les puissances occidentales, la scène politique iranienne reste au cœur de l''actualité internationale. Le gouvernement d''Ebrahim Raïssi a dû faire face à une opposition croissante, tandis que les Gardiens de la Révolution consolidaient leur influence sur les institutions.',
    NULL,
    'Manifestation à Téhéran en 2024',
    'Situation politique Iran 2024',
    'Analyse de la situation politique iranienne en 2024 : tensions, négociations nucléaires et manifestations.',
    TRUE,
    1,
    1
),
(
    'Économie iranienne : l''impact des sanctions',
    'economie-iranienne-impact-sanctions',
    'Comment les sanctions internationales affectent-elles le quotidien des Iraniens ?',
    'Les sanctions économiques imposées à l''Iran par les États-Unis et l''Union européenne continuent de peser lourdement sur l''économie du pays. Le rial iranien a perdu une grande partie de sa valeur, l''inflation dépasse les 40%, et le chômage touche une large partie de la population jeune. Pourtant, l''Iran a développé des mécanismes d''adaptation, notamment via des échanges avec la Chine et la Russie.',
    NULL,
    'Marché bazaar de Téhéran',
    'Économie iranienne et sanctions',
    'Analyse de l''impact des sanctions internationales sur l''économie et la population iranienne.',
    TRUE,
    2,
    1
),
(
    'Histoire de la Révolution islamique de 1979',
    'histoire-revolution-islamique-1979',
    'Retour sur les événements qui ont bouleversé l''Iran et le Moyen-Orient.',
    'Le 11 février 1979 marque la chute du Shah Mohammad Reza Pahlavi et l''instauration de la République islamique dirigée par l''ayatollah Khomeini. Cette révolution a profondément transformé la société iranienne et redéfini les équilibres géopolitiques au Moyen-Orient. De la montée des protestations de 1978 aux premières années de la République islamique, cet article retrace les moments clés de cette révolution historique.',
    NULL,
    'Révolution islamique iranienne 1979',
    'La Révolution islamique iranienne de 1979',
    'Histoire de la Révolution islamique de 1979 en Iran : causes, déroulement et conséquences.',
    TRUE,
    3,
    1
);
