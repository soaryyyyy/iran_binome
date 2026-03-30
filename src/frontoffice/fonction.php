<?php
require_once __DIR__ . '/../connexion.php';

function getCategories(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();
}

function getDerniersArticles(PDO $pdo, int $limit = 9): array
{
    $stmt = $pdo->prepare("
        SELECT a.titre, a.slug, a.resume, a.image_url, a.image_alt, a.date_publication,
               c.nom AS categorie, c.slug AS categorie_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.is_published = TRUE
        ORDER BY a.date_publication DESC
        LIMIT :limit
    ");
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getArticleBySlug(PDO $pdo, string $slug): array|false
{
    $stmt = $pdo->prepare("
        SELECT a.*, c.nom AS categorie, c.slug AS categorie_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.slug = :slug AND a.is_published = TRUE
    ");
    $stmt->execute(['slug' => $slug]);
    return $stmt->fetch();
}

function getArticlesParCategorie(PDO $pdo, string $categorySlug, int $limit = 9): array
{
    $stmt = $pdo->prepare("
        SELECT a.titre, a.slug, a.resume, a.image_url, a.image_alt, a.date_publication,
               c.nom AS categorie, c.slug AS categorie_slug
        FROM articles a
        JOIN categories c ON a.category_id = c.id
        WHERE c.slug = :slug AND a.is_published = TRUE
        ORDER BY a.date_publication DESC
        LIMIT :limit
    ");
    $stmt->bindValue('slug', $categorySlug);
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCategorieBySlug(PDO $pdo, string $slug): array|false
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug");
    $stmt->execute(['slug' => $slug]);
    return $stmt->fetch();
}

function getArticlesLies(PDO $pdo, int $categoryId, string $excludeSlug, int $limit = 3): array
{
    $stmt = $pdo->prepare("
        SELECT titre, slug, resume, date_publication
        FROM articles
        WHERE category_id = :cat_id
          AND slug != :slug
          AND is_published = TRUE
        ORDER BY date_publication DESC
        LIMIT :limit
    ");
    $stmt->bindValue('cat_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue('slug', $excludeSlug);
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
