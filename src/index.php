<?php
require_once __DIR__ . '/frontoffice/fonction.php';

$articleUne = getArticleALaUne($pdo);
$articles   = getDerniersArticles($pdo);
$categories = getCategories($pdo);
$pageTitle = "Iran Actualites - Suivez l'actualite iranienne";
$metaDescription = getDefaultMetaDescription();
$canonicalUrl = getCurrentUrl();
$homeUrl = buildAbsoluteUrl('/');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iran Actualités - Suivez l'actualité iranienne</title>
    <meta name="description" content="Retrouvez toute l'actualité sur l'Iran : politique, économie, société, culture. Articles fiables et mis à jour régulièrement.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta name="author" content="Iran Actualités">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars(getSiteName()) ?>">
    <meta property="og:title" content="Iran Actualités - Suivez l'actualité iranienne">
    <meta property="og:description" content="Retrouvez toute l'actualité sur l'Iran : politique, économie, société, culture.">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary_large_image">

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => getSiteName(),
        'url' => $homeUrl,
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

        header h1 { font-size: 28px; letter-spacing: 1px; }
        header h1 span { color: #e8a020; }

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
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s, color 0.2s;
        }

        nav ul li a:hover { background: #e8a020; color: #fff; }

        main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        section h2 {
            font-size: 22px;
            margin-bottom: 24px;
            padding-bottom: 8px;
            border-bottom: 3px solid #e8a020;
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

        .card-body h3 { font-size: 17px; margin-bottom: 8px; line-height: 1.4; }

        .card-body h3 a { color: #1a1a2e; text-decoration: none; }
        .card-body h3 a:hover { color: #e8a020; }

        .card-meta {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
        }

        .card-meta a { color: #e8a020; text-decoration: none; }

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
            .articles-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <h1>Iran <span>Actualités</span></h1>
        <p style="font-size:13px; color:#aaa; font-family:Arial,sans-serif;">L'information sur l'Iran en français</p>
    </header>

    <nav aria-label="Navigation principale">
        <ul>
            <li><a href="/">Accueil</a></li>
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>">
                        <?= htmlspecialchars($cat['nom']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <main>

        <?php if ($articleUne): ?>
        <section aria-labelledby="titre-une" style="margin-bottom:40px;">
            <h2 id="titre-une" style="font-size:13px; text-transform:uppercase; letter-spacing:2px; color:#e8a020; font-family:Arial,sans-serif; margin-bottom:16px;">
                À la une
            </h2>
            <a href="/article/<?= htmlspecialchars($articleUne['slug']) ?>" style="text-decoration:none; display:block;">
                <div style="position:relative; border-radius:8px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.15); background:#1a1a2e; min-height:320px; display:flex; align-items:flex-end;">
                    <?php if ($articleUne['image_url']): ?>
                        <img src="<?= htmlspecialchars($articleUne['image_url']) ?>"
                             alt="<?= htmlspecialchars($articleUne['image_alt'] ?? $articleUne['titre']) ?>"
                             style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0.45;">
                    <?php endif; ?>
                    <div style="position:relative; padding:32px; width:100%;">
                        <?php if ($articleUne['categorie']): ?>
                            <span style="display:inline-block; background:#e8a020; color:#fff; font-size:11px; font-family:Arial,sans-serif; text-transform:uppercase; letter-spacing:1px; padding:4px 12px; border-radius:3px; margin-bottom:12px;">
                                <?= htmlspecialchars($articleUne['categorie']) ?>
                            </span>
                        <?php endif; ?>
                        <h3 style="color:#fff; font-size:28px; line-height:1.3; margin-bottom:12px; text-shadow:0 2px 6px rgba(0,0,0,0.5);">
                            <?= htmlspecialchars($articleUne['titre']) ?>
                        </h3>
                        <?php if ($articleUne['resume']): ?>
                            <p style="color:#ddd; font-size:15px; line-height:1.6; text-shadow:0 1px 4px rgba(0,0,0,0.5);">
                                <?= htmlspecialchars($articleUne['resume']) ?>
                            </p>
                        <?php endif; ?>
                        <p style="color:#aaa; font-size:12px; font-family:Arial,sans-serif; margin-top:12px;">
                            <?= date('d/m/Y', strtotime($articleUne['date_publication'])) ?>
                        </p>
                    </div>
                </div>
            </a>
        </section>
        <?php endif; ?>

        <section aria-labelledby="titre-articles">
            <h2 id="titre-articles">Derniers articles</h2>

            <?php if (empty($articles)): ?>
                <p class="empty">Aucun article publié pour le moment.</p>
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
                                    <?php if ($article['categorie']): ?>
                                        &bull;
                                        <a href="/categorie/<?= htmlspecialchars($article['categorie_slug']) ?>">
                                            <?= htmlspecialchars($article['categorie']) ?>
                                        </a>
                                    <?php endif; ?>
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
            <!-- <a href="/backoffice/login">Administration</a> -->
        </p>
    </footer>

</body>
</html>
