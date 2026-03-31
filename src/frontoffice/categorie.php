<?php
require_once __DIR__ . '/fonction.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header("Location: /");
    exit();
}

$categorie = getCategorieBySlug($pdo, $slug);

if (!$categorie) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
    exit();
}

$categories = getCategories($pdo);
$articles   = getArticlesParCategorie($pdo, $slug);

$metaTitle = htmlspecialchars($categorie['nom']) . ' - Iran Actualités';
$metaDesc  = 'Tous les articles sur ' . htmlspecialchars($categorie['nom']) . ' concernant l\'Iran.';
$canonicalUrl = getCurrentUrl();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $metaTitle ?></title>
    <meta name="description" content="<?= $metaDesc ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $metaTitle ?>">
    <meta property="og:description" content="<?= $metaDesc ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars(getSiteName()) ?>">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary">

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => trim(strip_tags($metaTitle)),
        'description' => trim(strip_tags($metaDesc)),
        'url' => $canonicalUrl,
        'inLanguage' => 'fr-FR',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #f5f5f0;
            color: #222;
            line-height: 1.6;
        }

        header {
            background: #1a1a2e;
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            color: #fff;
            text-decoration: none;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        header a span { color: #e8a020; }

        nav {
            background: #16213e;
            padding: 0 40px;
        }

        nav ul { list-style: none; display: flex; }

        nav ul li a {
            display: block;
            color: #ccc;
            text-decoration: none;
            padding: 14px 20px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            text-transform: uppercase;
            transition: background 0.2s, color 0.2s;
        }

        nav ul li a:hover,
        nav ul li a[aria-current="page"] {
            background: #e8a020;
            color: #fff;
        }

        /* Bannière catégorie */
        .categorie-banner {
            background: #16213e;
            border-bottom: 4px solid #e8a020;
            padding: 30px 40px;
            text-align: center;
        }

        .categorie-banner h1 {
            color: #fff;
            font-size: 32px;
            letter-spacing: 1px;
        }

        .categorie-banner p {
            color: #aaa;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-top: 8px;
        }

        main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        section h2 {
            font-size: 18px;
            margin-bottom: 24px;
            color: #555;
            font-family: Arial, sans-serif;
            font-weight: normal;
        }

        section h2 strong {
            color: #1a1a2e;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
        }

        article.card {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        article.card:hover { transform: translateY(-3px); }

        article.card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }

        .card-body { padding: 18px; }

        .card-meta {
            font-size: 12px;
            color: #aaa;
            margin-bottom: 8px;
            font-family: Arial, sans-serif;
        }

        .card-body h3 {
            font-size: 17px;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .card-body h3 a { color: #1a1a2e; text-decoration: none; }
        .card-body h3 a:hover { color: #e8a020; }

        .card-body p { font-size: 14px; color: #555; }

        .empty {
            text-align: center;
            color: #888;
            padding: 60px 0;
            font-size: 16px;
        }

        footer {
            background: #1a1a2e;
            color: #aaa;
            text-align: center;
            padding: 24px;
            margin-top: 60px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        footer a { color: #e8a020; text-decoration: none; }

        @media (max-width: 768px) {
            header { padding: 16px 20px; }
            nav { padding: 0 10px; }
            .categorie-banner { padding: 20px; }
            .articles-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <a href="/"><span>Iran</span> Actualités</a>
        <p style="font-size:13px; color:#aaa; font-family:Arial,sans-serif;">L'information sur l'Iran en français</p>
    </header>

    <nav aria-label="Navigation principale">
        <ul>
            <li><a href="/">Accueil</a></li>
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>"
                       <?= $cat['slug'] === $slug ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="categorie-banner">
        <h1><?= htmlspecialchars($categorie['nom']) ?></h1>
        <p><?= count($articles) ?> article<?= count($articles) > 1 ? 's' : '' ?> dans cette catégorie</p>
    </div>

    <main>
        <section aria-labelledby="titre-categorie">
            <h2 id="titre-categorie">
                Articles : <strong><?= htmlspecialchars($categorie['nom']) ?></strong>
            </h2>

            <?php if (empty($articles)): ?>
                <p class="empty">Aucun article publié dans cette catégorie.</p>
            <?php else: ?>
                <div class="articles-grid">
                    <?php foreach ($articles as $article): ?>
                        <article class="card">
                            <?php if ($article['image_url']): ?>
                                <img
                                    src="<?= htmlspecialchars($article['image_url']) ?>"
                                    alt="<?= htmlspecialchars($article['image_alt'] ?? $article['titre']) ?>"
                                    width="300"
                                    height="180"
                                    loading="lazy"
                                >
                            <?php endif; ?>
                            <div class="card-body">
                                <p class="card-meta">
                                    <?= date('d/m/Y', strtotime($article['date_publication'])) ?>
                                </p>
                                <h3>
                                    <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                                        <?= htmlspecialchars($article['titre']) ?>
                                    </a>
                                </h3>
                                <?php if ($article['resume']): ?>
                                    <p><?= htmlspecialchars($article['resume']) ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Iran Actualités &mdash;
            <a href="/backoffice">Administration</a>
        </p>
    </footer>

</body>
</html>
