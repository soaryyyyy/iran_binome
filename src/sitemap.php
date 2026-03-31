<?php
require_once __DIR__ . '/frontoffice/fonction.php';

header('Content-Type: application/xml; charset=UTF-8');

$pages = [
    [
        'loc' => buildAbsoluteUrl('/'),
        'lastmod' => date(DATE_ATOM),
        'changefreq' => 'daily',
        'priority' => '1.0',
    ],
];

foreach (getCategories($pdo) as $categorie) {
    $pages[] = [
        'loc' => buildAbsoluteUrl('/categorie/' . $categorie['slug']),
        'lastmod' => date(DATE_ATOM),
        'changefreq' => 'weekly',
        'priority' => '0.8',
    ];
}

foreach (getPublishedArticlesForSitemap($pdo) as $article) {
    $pages[] = [
        'loc' => buildAbsoluteUrl('/article/' . $article['slug']),
        'lastmod' => !empty($article['date_publication']) ? date(DATE_ATOM, strtotime($article['date_publication'])) : date(DATE_ATOM),
        'changefreq' => 'monthly',
        'priority' => '0.7',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $page): ?>
    <url>
        <loc><?= htmlspecialchars($page['loc'], ENT_XML1) ?></loc>
        <lastmod><?= htmlspecialchars($page['lastmod'], ENT_XML1) ?></lastmod>
        <changefreq><?= htmlspecialchars($page['changefreq'], ENT_XML1) ?></changefreq>
        <priority><?= htmlspecialchars($page['priority'], ENT_XML1) ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
