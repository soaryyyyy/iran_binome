<?php
require_once __DIR__ . '/fonction.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header("Location: /");
    exit();
}

$article = getArticleBySlug($pdo, $slug);

if (!$article) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
    exit();
}

$categories  = getCategories($pdo);
$articlesLies = $article['category_id']
    ? getArticlesLies($pdo, $article['category_id'], $slug)
    : [];

$metaTitle = htmlspecialchars($article['meta_title'] ?: $article['titre']);
$metaDesc  = htmlspecialchars($article['meta_description'] ?: $article['resume'] ?: '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $metaTitle ?> - Iran Actualités</title>
    <meta name="description" content="<?= $metaDesc ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= $metaTitle ?>">
    <meta property="og:description" content="<?= $metaDesc ?>">
    <?php if ($article['image_url']): ?>
    <meta property="og:image" content="<?= htmlspecialchars($article['image_url']) ?>">
    <?php endif; ?>
    <meta property="og:locale" content="fr_FR">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #f5f5f0;
            color: #222;
            line-height: 1.7;
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

        nav ul {
            list-style: none;
            display: flex;
        }

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

        .page-wrapper {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 40px;
        }

        article {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        article > img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            display: block;
        }

        .article-inner { padding: 32px 40px; }

        .article-meta {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #888;
            margin-bottom: 16px;
        }

        .article-meta a { color: #e8a020; text-decoration: none; }

        .article-inner h1 {
            font-size: 30px;
            line-height: 1.3;
            margin-bottom: 12px;
            color: #1a1a2e;
        }

        .article-resume {
            font-size: 17px;
            color: #555;
            font-style: italic;
            border-left: 4px solid #e8a020;
            padding-left: 16px;
            margin-bottom: 28px;
        }

        /* Styles pour le contenu TinyMCE */
        .article-content { font-size: 16px; color: #333; }
        .article-content h2 { font-size: 22px; margin: 28px 0 12px; color: #1a1a2e; }
        .article-content h3 { font-size: 18px; margin: 22px 0 10px; color: #1a1a2e; }
        .article-content h4, .article-content h5, .article-content h6 { margin: 18px 0 8px; color: #1a1a2e; }
        .article-content p { margin-bottom: 16px; }
        .article-content ul, .article-content ol { margin: 0 0 16px 24px; }
        .article-content li { margin-bottom: 6px; }
        .article-content blockquote {
            border-left: 4px solid #e8a020;
            padding: 12px 20px;
            margin: 20px 0;
            background: #fdf6ec;
            font-style: italic;
            color: #555;
        }
        .article-content img { max-width: 100%; height: auto; border-radius: 4px; margin: 12px 0; }
        .article-content a { color: #e8a020; }

        aside h2 {
            font-size: 17px;
            color: #1a1a2e;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 3px solid #e8a020;
            font-family: Arial, sans-serif;
        }

        .related-list { list-style: none; display: flex; flex-direction: column; gap: 14px; }

        .related-list li {
            background: #fff;
            border-radius: 5px;
            padding: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }

        .related-list li a {
            color: #1a1a2e;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.4;
        }

        .related-list li a:hover { color: #e8a020; }

        .related-list .date {
            display: block;
            font-size: 11px;
            color: #aaa;
            margin-top: 4px;
            font-family: Arial, sans-serif;
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
            .page-wrapper { grid-template-columns: 1fr; }
            .article-inner { padding: 20px; }
            header { padding: 16px 20px; }
            nav { padding: 0 10px; }
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
                       <?= $cat['slug'] === ($article['categorie_slug'] ?? '') ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="page-wrapper">

        <article>
            <?php if ($article['image_url']): ?>
                <img
                    src="<?= htmlspecialchars($article['image_url']) ?>"
                    alt="<?= htmlspecialchars($article['image_alt'] ?? $article['titre']) ?>"
                    width="760"
                    height="400"
                >
            <?php endif; ?>

            <div class="article-inner">
                <p class="article-meta">
                    <?= date('d/m/Y', strtotime($article['date_publication'])) ?>
                    <?php if ($article['categorie']): ?>
                        &bull;
                        <a href="/categorie/<?= htmlspecialchars($article['categorie_slug']) ?>">
                            <?= htmlspecialchars($article['categorie']) ?>
                        </a>
                    <?php endif; ?>
                </p>

                <h1><?= htmlspecialchars($article['titre']) ?></h1>

                <?php if ($article['resume']): ?>
                    <p class="article-resume"><?= htmlspecialchars($article['resume']) ?></p>
                <?php endif; ?>

                <!-- Contenu HTML généré par TinyMCE -->
                <div class="article-content">
                    <?= $article['contenu'] ?>
                </div>
            </div>
        </article>

        <aside aria-label="Articles liés">
            <h2>Articles liés</h2>
            <?php if (empty($articlesLies)): ?>
                <p style="font-size:14px; color:#888;">Aucun autre article dans cette catégorie.</p>
            <?php else: ?>
                <ul class="related-list">
                    <?php foreach ($articlesLies as $lie): ?>
                        <li>
                            <a href="/article/<?= htmlspecialchars($lie['slug']) ?>">
                                <?= htmlspecialchars($lie['titre']) ?>
                            </a>
                            <span class="date"><?= date('d/m/Y', strtotime($lie['date_publication'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </aside>

    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Iran Actualités &mdash;
            <a href="/backoffice">Administration</a>
        </p>
    </footer>

</body>
</html>
