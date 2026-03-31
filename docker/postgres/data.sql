BEGIN;

-- Mot de passe : admin123
INSERT INTO users (username, mdp)
VALUES ('admin', '$2y$10$S6gbcqi02qFNzO.kSpv0CuRJ4Bn06RIPsnqhCXr5QrXE0WwTbsp92')
ON CONFLICT (username) DO NOTHING;

WITH seed_categories (nom, slug) AS (
    VALUES
        ('Actualites du conflit', 'actualites-conflit'),
        ('Front interieur', 'front-interieur'),
        ('Diplomatie de guerre', 'diplomatie-guerre'),
        ('Impact humanitaire', 'impact-humanitaire'),
        ('Forces et strategie', 'forces-strategie'),
        ('Energie sous tension', 'energie-sous-tension'),
        ('Cyberconflit', 'cyberconflit'),
        ('Chronologie du conflit', 'chronologie-conflit')
)
INSERT INTO categories (nom, slug)
SELECT nom, slug
FROM seed_categories
ON CONFLICT (slug) DO UPDATE
SET nom = EXCLUDED.nom;

WITH params AS (
    SELECT 24::int AS articles_per_category
),
seed_categories (slug) AS (
    VALUES
        ('actualites-conflit'),
        ('front-interieur'),
        ('diplomatie-guerre'),
        ('impact-humanitaire'),
        ('forces-strategie'),
        ('energie-sous-tension'),
        ('cyberconflit'),
        ('chronologie-conflit')
),
author_ref AS (
    SELECT id
    FROM users
    WHERE username = 'admin'
    LIMIT 1
),
category_ref AS (
    SELECT
        c.id,
        c.nom,
        c.slug,
        ROW_NUMBER() OVER (ORDER BY c.slug) AS category_rank
    FROM categories c
    JOIN seed_categories s ON s.slug = c.slug
),
generated_articles AS (
    SELECT
        FORMAT('%s : %s', v.prefix_label, v.theme_label) AS titre,
        FORMAT('%s-%s-%s', c.slug, v.prefix_slug, v.theme_slug) AS slug,
        LEFT(
            FORMAT(
                '%s sur la guerre en Iran. Focus sur %s, avec une attention particuliere pour %s.',
                v.prefix_label,
                v.theme_label,
                v.angle_label
            ),
            300
        ) AS resume,
        FORMAT(
            '<p>%s suit l''evolution de la guerre en Iran a travers %s. L''analyse insiste sur %s, en observant le role de %s.</p><p>Le dossier met en perspective les repercussions immediates du conflit: %s. Le texte est genere pour fournir une base de test credible, tout en restant entierement centre sur la guerre en Iran.</p><p>Cette publication est rattachee a la categorie %s afin de verifier les filtres, les listes, les pages detail et les metadonnees SEO.</p>',
            v.prefix_label,
            v.theme_label,
            v.angle_label,
            v.actor_label,
            v.stakes_label,
            c.nom
        ) AS contenu,
        CASE
            WHEN gs.n % 3 = 0 THEN FORMAT('https://picsum.photos/seed/%s-%s/1200/700', c.slug, gs.n)
            ELSE NULL
        END AS image_url,
        FORMAT('%s pendant la guerre en Iran', v.theme_label) AS image_alt,
        LEFT(FORMAT('%s - %s - guerre en Iran', v.prefix_label, c.nom), 70) AS meta_title,
        LEFT(
            FORMAT(
                '%s sur %s dans la guerre en Iran. Angle: %s.',
                v.prefix_label,
                v.theme_label,
                v.angle_label
            ),
            160
        ) AS meta_description,
        NOW()
            - MAKE_INTERVAL(days => (((c.category_rank - 1) * p.articles_per_category) + gs.n)::int)
            + MAKE_INTERVAL(hours => (gs.n % 18)::int) AS date_publication,
        (gs.n % 9) <> 0 AS is_published,
        (c.slug = 'actualites-conflit' AND gs.n = 1) OR (gs.n % 17 = 0) AS is_featured,
        c.id AS category_id,
        a.id AS author_id
    FROM category_ref c
    CROSS JOIN params p
    CROSS JOIN GENERATE_SERIES(1, p.articles_per_category) AS gs(n)
    CROSS JOIN author_ref a
    CROSS JOIN LATERAL (
        SELECT
            (CASE c.slug
                WHEN 'actualites-conflit' THEN ARRAY['point-de-situation', 'alerte-terrain', 'bilan-quotidien', 'surveillance-regionale']
                WHEN 'front-interieur' THEN ARRAY['tension-interieure', 'rapport-local', 'suivi-des-villes', 'climat-de-guerre']
                WHEN 'diplomatie-guerre' THEN ARRAY['canal-diplomatique', 'pression-internationale', 'negociation-en-coulisse', 'lecture-regionale']
                WHEN 'impact-humanitaire' THEN ARRAY['urgence-humanitaire', 'bilan-civil', 'terrain-associatif', 'suivi-des-deplacements']
                WHEN 'forces-strategie' THEN ARRAY['lecture-militaire', 'mouvement-des-forces', 'analyse-strategique', 'etat-major']
                WHEN 'energie-sous-tension' THEN ARRAY['alerte-energie', 'pression-sur-les-sites', 'surveillance-des-flux', 'impact-maritime']
                WHEN 'cyberconflit' THEN ARRAY['alerte-cyber', 'operations-numeriques', 'surveillance-des-reseaux', 'riposte-informationnelle']
                ELSE ARRAY['repere-chronologique', 'retour-sur-sequence', 'etape-cle', 'mise-en-perspective']
            END)[((gs.n - 1) / 6) + 1] AS prefix_slug,
            (CASE c.slug
                WHEN 'actualites-conflit' THEN ARRAY['Point de situation', 'Alerte terrain', 'Bilan quotidien', 'Surveillance regionale']
                WHEN 'front-interieur' THEN ARRAY['Tension interieure', 'Rapport local', 'Suivi des villes', 'Climat de guerre']
                WHEN 'diplomatie-guerre' THEN ARRAY['Canal diplomatique', 'Pression internationale', 'Negociation en coulisse', 'Lecture regionale']
                WHEN 'impact-humanitaire' THEN ARRAY['Urgence humanitaire', 'Bilan civil', 'Terrain associatif', 'Suivi des deplacements']
                WHEN 'forces-strategie' THEN ARRAY['Lecture militaire', 'Mouvement des forces', 'Analyse strategique', 'Etat-major']
                WHEN 'energie-sous-tension' THEN ARRAY['Alerte energie', 'Pression sur les sites', 'Surveillance des flux', 'Impact maritime']
                WHEN 'cyberconflit' THEN ARRAY['Alerte cyber', 'Operations numeriques', 'Surveillance des reseaux', 'Riposte informationnelle']
                ELSE ARRAY['Repere chronologique', 'Retour sur sequence', 'Etape cle', 'Mise en perspective']
            END)[((gs.n - 1) / 6) + 1] AS prefix_label,
            (CASE c.slug
                WHEN 'actualites-conflit' THEN ARRAY['frappes-sites-militaires', 'combats-axes-ouest', 'deploiements-sites-sensibles', 'tirs-espace-aerien', 'mobilisation-reserves', 'pression-ravitaillement']
                WHEN 'front-interieur' THEN ARRAY['controle-quartiers-strategiques', 'restrictions-deplacements-civils', 'coupures-reseaux-urbains', 'tension-autour-administrations', 'economies-locales-sous-pression', 'organisation-des-evacuations']
                WHEN 'diplomatie-guerre' THEN ARRAY['mediation-voisins-golfe', 'pressions-au-conseil-securite', 'durcissement-sanctions', 'positionnement-des-allies', 'canaux-militaires-indirects', 'risque-d-extension-regionale']
                WHEN 'impact-humanitaire' THEN ARRAY['afflux-blesses-hopitaux', 'besoins-alimentaires-urgents', 'deplacements-familles', 'pression-sur-les-soins', 'protection-des-enfants', 'coordination-des-secours']
                WHEN 'forces-strategie' THEN ARRAY['rotation-des-unites', 'defense-des-sites-radars', 'usage-des-drones', 'repli-sur-les-lignes-arriere', 'lecture-des-doctrines', 'saturation-des-capacites']
                WHEN 'energie-sous-tension' THEN ARRAY['risque-sur-les-terminaux', 'securite-des-raffineries', 'flux-gaziers-perturbes', 'routes-maritimes-surveillees', 'stocks-strategiques', 'pression-sur-les-exportations']
                WHEN 'cyberconflit' THEN ARRAY['attaques-sur-les-reseaux', 'campagnes-de-desinformation', 'protection-des-donnees-publiques', 'blocages-de-plateformes', 'coordination-des-cellules-cyber', 'ciblage-des-infrastructures-connectees']
                ELSE ARRAY['sequence-des-premieres-frappes', 'extension-du-theatre-des-operations', 'durcissement-des-reponses', 'phase-de-redploiement', 'moment-de-bascule-diplomatique', 'stabilisation-precaire-du-front']
            END)[((gs.n - 1) % 6) + 1] AS theme_slug,
            (CASE c.slug
                WHEN 'actualites-conflit' THEN ARRAY['frappes sur les sites militaires', 'combats autour des axes de l''ouest', 'deploiements autour des sites sensibles', 'tirs et interceptions dans l''espace aerien', 'mobilisation des reserves locales', 'pression sur les lignes de ravitaillement']
                WHEN 'front-interieur' THEN ARRAY['controle des quartiers strategiques', 'restrictions sur les deplacements civils', 'coupures de reseaux en zone urbaine', 'tension autour des administrations locales', 'economies locales sous pression', 'organisation des evacuations']
                WHEN 'diplomatie-guerre' THEN ARRAY['mediation des voisins du Golfe', 'pressions au Conseil de securite', 'durcissement des sanctions', 'positionnement des allies regionaux', 'canaux militaires indirects', 'risque d''extension regionale']
                WHEN 'impact-humanitaire' THEN ARRAY['afflux de blesses dans les hopitaux', 'besoins alimentaires urgents', 'deplacements de familles', 'pression sur l''acces aux soins', 'protection des enfants et des personnes agees', 'coordination des secours']
                WHEN 'forces-strategie' THEN ARRAY['rotation des unites de combat', 'defense des sites radars', 'usage des drones sur le theatre d''operations', 'repli sur les lignes arriere', 'lecture des doctrines d''engagement', 'saturation des capacites de defense']
                WHEN 'energie-sous-tension' THEN ARRAY['risque sur les terminaux energetiques', 'securite des raffineries', 'flux gaziers perturbes', 'routes maritimes sous surveillance', 'gestion des stocks strategiques', 'pression sur les exportations']
                WHEN 'cyberconflit' THEN ARRAY['attaques sur les reseaux sensibles', 'campagnes de desinformation', 'protection des donnees publiques', 'blocages de plateformes critiques', 'coordination des cellules cyber', 'ciblage des infrastructures connectees']
                ELSE ARRAY['sequence des premieres frappes', 'extension du theatre des operations', 'durcissement des reponses', 'phase de redploiement', 'moment de bascule diplomatique', 'stabilisation precaire du front']
            END)[((gs.n - 1) % 6) + 1] AS theme_label,
            (CASE c.slug
                WHEN 'actualites-conflit' THEN ARRAY['l''etat des installations touchees et des chaines de commandement', 'la maitrise des routes et des points de passage', 'la protection des centres de commandement et des depots', 'les interceptions, alertes et capacites de defense', 'la rapidite de mobilisation des unites locales', 'la tenue des convois et la continuite logistique']
                WHEN 'front-interieur' THEN ARRAY['les effets des combats sur l''ordre public', 'les contraintes imposees aux habitants', 'la resilience des services urbains', 'la securisation des institutions locales', 'la pression sur les commerces et les activites civiles', 'les corridors de sortie pour les habitants exposes']
                WHEN 'diplomatie-guerre' THEN ARRAY['les marges de mediation ouvertes par les capitales voisines', 'les prises de position des membres permanents', 'la capacite de contrainte economique sur le conflit', 'les calculs strategiques des partenaires regionaux', 'les messages transmis par des canaux discrets', 'les scenarios d''embrasement au-dela des frontieres']
                WHEN 'impact-humanitaire' THEN ARRAY['la charge immediate sur les structures medicales', 'la disponibilite des denrees et des convois', 'les besoins d''accueil pour les populations deplacees', 'la continuite des soins dans les zones exposees', 'la vulnerabilite des publics les plus fragiles', 'la coordination entre secours locaux et partenaires externes']
                WHEN 'forces-strategie' THEN ARRAY['le rythme des releves et la fatigue operationnelle', 'la couverture defensive des installations de veille', 'la place prise par les moyens sans pilote', 'la capacite a tenir les lignes arriere', 'la coherence entre doctrine et execution', 'la consommation rapide des moyens engages']
                WHEN 'energie-sous-tension' THEN ARRAY['la securite physique des sites de sortie', 'la continuite de production sous menace', 'les perturbations sur l''acheminement gazier', 'la protection des couloirs maritimes', 'la capacite d''amortir une rupture longue', 'l''impact sur les recettes et les contrats']
                WHEN 'cyberconflit' THEN ARRAY['la vulnerabilite des architectures critiques', 'la circulation de narratifs hostiles', 'la protection des bases de donnees institutionnelles', 'la dependance a des services numeriques fragiles', 'la synchronisation des equipes de reponse', 'l''exposition des equipements relies aux reseaux']
                ELSE ARRAY['les signaux qui ont precede l''ouverture du conflit', 'l''elargissement progressif du champ des operations', 'la logique de surenchere dans les reponses', 'les raisons du redploiement des forces', 'la place du tournant diplomatique dans la sequence', 'les conditions d''une stabilisation encore incertaine']
            END)[((gs.n - 1) % 6) + 1] AS angle_label,
            CASE c.slug
                WHEN 'actualites-conflit' THEN 'les etats-majors, les forces sur le terrain et les autorites de securite'
                WHEN 'front-interieur' THEN 'les autorites locales, les forces de l''ordre et les habitants des zones urbaines'
                WHEN 'diplomatie-guerre' THEN 'les chancelleries regionales, les mediateurs et les partenaires occidentaux'
                WHEN 'impact-humanitaire' THEN 'les civils, les secours locaux et les organisations humanitaires'
                WHEN 'forces-strategie' THEN 'les commandements militaires, les unites specialisees et les reseaux de soutien'
                WHEN 'energie-sous-tension' THEN 'les operateurs energetiques, les autorites portuaires et les acteurs du commerce regional'
                WHEN 'cyberconflit' THEN 'les equipes cyber, les services de renseignement et les operateurs de reseaux'
                ELSE 'les observateurs, les redacteurs et les analystes du conflit'
            END AS actor_label,
            CASE c.slug
                WHEN 'actualites-conflit' THEN 'le rythme des combats, la securite des villes et la capacite de reaction immediate'
                WHEN 'front-interieur' THEN 'la vie quotidienne, les restrictions de circulation et la tenue des services essentiels'
                WHEN 'diplomatie-guerre' THEN 'les sanctions, les canaux de negociation et le risque d''isolement diplomatique'
                WHEN 'impact-humanitaire' THEN 'les deplacements, l''acces aux soins et la distribution des biens de premiere necessite'
                WHEN 'forces-strategie' THEN 'la posture defensive, les redploiements et l''usure des moyens engages'
                WHEN 'energie-sous-tension' THEN 'les exportations, les stocks strategiques et la securite des infrastructures'
                WHEN 'cyberconflit' THEN 'les communications, la propagande et la resilience numerique'
                ELSE 'la lecture de la sequence, la memoire des operations et la coherence du recit chronologique'
            END AS stakes_label
    ) AS v
)
INSERT INTO articles (
    titre,
    slug,
    resume,
    contenu,
    image_url,
    image_alt,
    meta_title,
    meta_description,
    date_publication,
    is_published,
    is_featured,
    category_id,
    author_id
)
SELECT
    titre,
    slug,
    resume,
    contenu,
    image_url,
    image_alt,
    meta_title,
    meta_description,
    date_publication,
    is_published,
    is_featured,
    category_id,
    author_id
FROM generated_articles
ON CONFLICT (slug) DO UPDATE
SET titre = EXCLUDED.titre,
    resume = EXCLUDED.resume,
    contenu = EXCLUDED.contenu,
    image_url = EXCLUDED.image_url,
    image_alt = EXCLUDED.image_alt,
    meta_title = EXCLUDED.meta_title,
    meta_description = EXCLUDED.meta_description,
    date_publication = EXCLUDED.date_publication,
    is_published = EXCLUDED.is_published,
    is_featured = EXCLUDED.is_featured,
    category_id = EXCLUDED.category_id,
    author_id = EXCLUDED.author_id;

CREATE INDEX IF NOT EXISTS idx_articles_published_date
    ON articles (date_publication DESC)
    WHERE is_published = TRUE;

CREATE INDEX IF NOT EXISTS idx_articles_category_published_date
    ON articles (category_id, date_publication DESC)
    WHERE is_published = TRUE;

CREATE INDEX IF NOT EXISTS idx_articles_featured_date
    ON articles (date_publication DESC)
    WHERE is_published = TRUE AND is_featured = TRUE;

COMMIT;
