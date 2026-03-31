<?php
require_once 'fonction.php';
session_start();
redirigerSiNonConnecte();

$pageTitle = 'Articles';

// Publier / dépublier via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $id = (int) $_POST['toggle_id'];
    $stmt = $pdo->prepare("UPDATE articles SET is_published = NOT is_published WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: /backoffice/articles");
    exit();
}

// Récupérer tous les articles
$articles = $pdo->query("
    SELECT a.id, a.titre, a.slug, a.is_published, a.date_publication, c.nom AS categorie
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    ORDER BY a.date_publication DESC
")->fetchAll();

include 'layout_header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <p style="color:#888; font-size:14px;"><?= count($articles) ?> article(s) au total</p>
    <a href="/backoffice/article/nouveau" class="btn btn-success">+ Nouvel article</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Article supprimé avec succès.</div>
<?php endif; ?>

<div class="card">
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
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= htmlspecialchars($article['titre']) ?></td>
                    <td><?= htmlspecialchars($article['categorie'] ?? '—') ?></td>
                    <td><?= date('d/m/Y', strtotime($article['date_publication'])) ?></td>
                    <td>
                        <!-- Toggle publié/brouillon -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="toggle_id" value="<?= $article['id'] ?>">
                            <button type="submit" class="badge <?= $article['is_published'] ? 'badge-green' : 'badge-grey' ?>"
                                    style="cursor:pointer; border:none; background:inherit;">
                                <?= $article['is_published'] ? 'Publié' : 'Brouillon' ?>
                            </button>
                        </form>
                    </td>
                    <td style="display:flex; gap:6px;">
                        <a href="/backoffice/article/<?= $article['id'] ?>/modifier"
                           class="btn btn-primary btn-sm">Modifier</a>
                        <a href="/article/<?= htmlspecialchars($article['slug']) ?>"
                           class="btn btn-secondary btn-sm" target="_blank">Voir</a>
                        <a href="/backoffice/article/<?= $article['id'] ?>/supprimer"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($articles)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:#888; padding:40px;">
                        Aucun article pour le moment.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>
