<?php
require_once 'fonction.php';
session_start();
redirigerSiNonConnecte();

$pageTitle = 'Tableau de bord';

// Statistiques
$nbArticles  = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$nbPublies   = $pdo->query("SELECT COUNT(*) FROM articles WHERE is_published = TRUE")->fetchColumn();
$nbBrouillon = $nbArticles - $nbPublies;
$nbCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// 5 derniers articles
$derniersArticles = $pdo->query("
    SELECT a.id, a.titre, a.slug, a.is_published, a.date_publication, c.nom AS categorie
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    ORDER BY a.date_publication DESC
    LIMIT 5
")->fetchAll();

include 'layout_header.php';
?>

<!-- Cartes statistiques -->
<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap:20px; margin-bottom:32px;">

    <div class="card" style="text-align:center; padding:28px;">
        <div style="font-size:36px; font-weight:bold; color:#4a90e2;"><?= $nbArticles ?></div>
        <div style="font-size:14px; color:#888; margin-top:6px;">Articles total</div>
    </div>

    <div class="card" style="text-align:center; padding:28px;">
        <div style="font-size:36px; font-weight:bold; color:#27ae60;"><?= $nbPublies ?></div>
        <div style="font-size:14px; color:#888; margin-top:6px;">Publiés</div>
    </div>

    <div class="card" style="text-align:center; padding:28px;">
        <div style="font-size:36px; font-weight:bold; color:#e8a020;"><?= $nbBrouillon ?></div>
        <div style="font-size:14px; color:#888; margin-top:6px;">Brouillons</div>
    </div>

    <div class="card" style="text-align:center; padding:28px;">
        <div style="font-size:36px; font-weight:bold; color:#9b59b6;"><?= $nbCategories ?></div>
        <div style="font-size:14px; color:#888; margin-top:6px;">Catégories</div>
    </div>

</div>

<!-- Derniers articles -->
<div class="card">
    <h3>Derniers articles</h3>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($derniersArticles as $article): ?>
                <tr>
                    <td><?= htmlspecialchars($article['titre']) ?></td>
                    <td><?= htmlspecialchars($article['categorie'] ?? '—') ?></td>
                    <td><?= date('d/m/Y', strtotime($article['date_publication'])) ?></td>
                    <td>
                        <?php if ($article['is_published']): ?>
                            <span class="badge badge-green">Publié</span>
                        <?php else: ?>
                            <span class="badge badge-grey">Brouillon</span>
                        <?php endif; ?>
                    </td>
                    <td style="display:flex; gap:6px;">
                        <a href="/backoffice/article/<?= $article['id'] ?>/modifier" class="btn btn-primary btn-sm">Modifier</a>
                        <a href="/article/<?= htmlspecialchars($article['slug']) ?>" class="btn btn-secondary btn-sm" target="_blank">Voir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top:16px;">
        <a href="/backoffice/articles" class="btn btn-secondary">Voir tous les articles</a>
        <a href="/backoffice/article/nouveau" class="btn btn-success" style="margin-left:10px;">+ Nouvel article</a>
    </div>
</div>

<?php include 'layout_footer.php'; ?>
