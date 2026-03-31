<?php
require_once 'fonction.php';
session_start();
redirigerSiNonConnecte();

$id      = isset($_GET['id']) ? (int) $_GET['id'] : null;
$article = null;
$erreur  = '';
$succes  = '';

$pageTitle = $id ? 'Modifier un article' : 'Nouvel article';

// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

// Si modification, charger l'article existant
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if (!$article) {
        header("Location: /backoffice/articles");
        exit();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre            = trim($_POST['titre'] ?? '');
    $slug             = trim($_POST['slug'] ?? '');
    $resume           = trim($_POST['resume'] ?? '');
    $contenu          = $_POST['contenu'] ?? '';  // HTML de TinyMCE — ne pas échapper
    $image_url        = trim($_POST['image_url'] ?? '');
    $image_alt        = trim($_POST['image_alt'] ?? '');
    $meta_title       = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $category_id      = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $is_published     = isset($_POST['is_published']) ? 'true' : 'false';
    $is_featured      = isset($_POST['is_featured'])  ? 'true' : 'false';

    // Validation
    if (empty($titre) || empty($slug) || empty($contenu)) {
        $erreur = "Le titre, le slug et le contenu sont obligatoires.";
    } else {
        try {
            if ($id) {
                // Mise à jour
                $stmt = $pdo->prepare("
                    UPDATE articles SET
                        titre = :titre,
                        slug = :slug,
                        resume = :resume,
                        contenu = :contenu,
                        image_url = :image_url,
                        image_alt = :image_alt,
                        meta_title = :meta_title,
                        meta_description = :meta_description,
                        category_id = :category_id,
                        is_published = :is_published,
                        is_featured  = :is_featured
                    WHERE id = :id
                ");
                $stmt->execute([
                    'titre'            => $titre,
                    'slug'             => $slug,
                    'resume'           => $resume,
                    'contenu'          => $contenu,
                    'image_url'        => $image_url ?: null,
                    'image_alt'        => $image_alt ?: null,
                    'meta_title'       => $meta_title ?: null,
                    'meta_description' => $meta_description ?: null,
                    'category_id'      => $category_id,
                    'is_published'     => $is_published,
                    'is_featured'      => $is_featured,
                    'id'               => $id,
                ]);
                $succes = "Article mis à jour avec succès.";
                // Recharger l'article
                $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $article = $stmt->fetch();
            } else {
                // Création
                $stmt = $pdo->prepare("
                    INSERT INTO articles
                        (titre, slug, resume, contenu, image_url, image_alt, meta_title, meta_description, category_id, is_published, is_featured, author_id)
                    VALUES
                        (:titre, :slug, :resume, :contenu, :image_url, :image_alt, :meta_title, :meta_description, :category_id, :is_published, :is_featured, :author_id)
                ");
                $stmt->execute([
                    'titre'            => $titre,
                    'slug'             => $slug,
                    'resume'           => $resume,
                    'contenu'          => $contenu,
                    'image_url'        => $image_url ?: null,
                    'image_alt'        => $image_alt ?: null,
                    'meta_title'       => $meta_title ?: null,
                    'meta_description' => $meta_description ?: null,
                    'category_id'      => $category_id,
                    'is_published'     => $is_published,
                    'is_featured'      => $is_featured,
                    'author_id'        => $_SESSION['user_id'],
                ]);
                $newId = $pdo->lastInsertId();
                header("Location: /backoffice/article/{$newId}/modifier?created=1");
                exit();
            }
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'unique') || str_contains($e->getMessage(), 'duplicate')) {
                $erreur = "Ce slug est déjà utilisé par un autre article.";
            } else {
                $erreur = "Erreur lors de l'enregistrement.";
            }
        }
    }
}

// Valeurs à afficher dans le formulaire
$v = [
    'titre'            => $_POST['titre']            ?? $article['titre']            ?? '',
    'slug'             => $_POST['slug']             ?? $article['slug']             ?? '',
    'resume'           => $_POST['resume']           ?? $article['resume']           ?? '',
    'contenu'          => $_POST['contenu']          ?? $article['contenu']          ?? '',
    'image_url'        => $_POST['image_url']        ?? $article['image_url']        ?? '',
    'image_alt'        => $_POST['image_alt']        ?? $article['image_alt']        ?? '',
    'meta_title'       => $_POST['meta_title']       ?? $article['meta_title']       ?? '',
    'meta_description' => $_POST['meta_description'] ?? $article['meta_description'] ?? '',
    'category_id'      => $_POST['category_id']      ?? $article['category_id']      ?? '',
    'is_published'     => isset($_POST['is_published'])
                            ? true
                            : ($article['is_published'] ?? false),
    'is_featured'      => isset($_POST['is_featured'])
                            ? true
                            : ($article['is_featured'] ?? false),
];

include 'layout_header.php';
?>

<?php if (isset($_GET['created'])): ?>
    <div class="alert alert-success">Article créé avec succès.</div>
<?php endif; ?>
<?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<form method="POST" action="">

    <div style="display:grid; grid-template-columns: 1fr 320px; gap:24px; align-items:start;">

        <!-- Colonne principale -->
        <div>

            <div class="card">
                <h3>Contenu</h3>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Titre <span style="color:red;">*</span>
                    </label>
                    <input type="text" name="titre" id="titre"
                           value="<?= htmlspecialchars($v['titre']) ?>"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:15px;"
                           required>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Slug (URL) <span style="color:red;">*</span>
                    </label>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <input type="text" name="slug" id="slug"
                               value="<?= htmlspecialchars($v['slug']) ?>"
                               style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; font-family:monospace;"
                               required>
                        <button type="button" onclick="genererSlug()"
                                style="padding:10px 14px; background:#f0f2f5; border:1px solid #ddd; border-radius:5px; cursor:pointer; font-size:13px;">
                            Auto
                        </button>
                    </div>
                    <p style="font-size:11px; color:#aaa; margin-top:4px;">
                        Ex : <code>guerre-iran-2024</code> → URL : /article/guerre-iran-2024
                    </p>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Résumé
                    </label>
                    <textarea name="resume" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;"
                              ><?= htmlspecialchars($v['resume']) ?></textarea>
                </div>

                <div>
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Contenu <span style="color:red;">*</span>
                    </label>
                    <!-- TinyMCE s'attache à ce textarea -->
                    <textarea name="contenu" id="contenu" rows="20"
                              style="width:100%;"><?= htmlspecialchars($v['contenu']) ?></textarea>
                </div>
            </div>

            <!-- SEO -->
            <div class="card">
                <h3>SEO</h3>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Meta title <span style="font-size:11px; color:#aaa;">(max 70 caractères)</span>
                    </label>
                    <input type="text" name="meta_title" maxlength="70"
                           value="<?= htmlspecialchars($v['meta_title']) ?>"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    <p style="font-size:11px; color:#aaa; margin-top:4px;">
                        Laisser vide pour utiliser le titre de l'article.
                    </p>
                </div>

                <div>
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Meta description <span style="font-size:11px; color:#aaa;">(max 160 caractères)</span>
                    </label>
                    <textarea name="meta_description" maxlength="160" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;"
                              ><?= htmlspecialchars($v['meta_description']) ?></textarea>
                </div>
            </div>

        </div>

        <!-- Colonne latérale -->
        <div>

            <!-- Publier -->
            <div class="card">
                <h3>Publication</h3>

                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:20px;">
                    <input type="checkbox" name="is_published" value="1"
                           <?= $v['is_published'] ? 'checked' : '' ?>
                           style="width:18px; height:18px;">
                    <span style="font-size:14px;">Publier l'article</span>
                </label>

                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:20px;">
                    <input type="checkbox" name="is_featured" value="1"
                           <?= $v['is_featured'] ? 'checked' : '' ?>
                           style="width:18px; height:18px; accent-color:#e8a020;">
                    <span style="font-size:14px;">⭐ À la une <span style="font-size:11px; color:#aaa;">(affiché en haut de l'accueil)</span></span>
                </label>

                <button type="submit" class="btn btn-success" style="width:100%;">
                    <?= $id ? 'Enregistrer les modifications' : 'Créer l\'article' ?>
                </button>

                <?php if ($id): ?>
                    <a href="/backoffice/articles" class="btn btn-secondary"
                       style="width:100%; margin-top:10px; text-align:center;">
                        Annuler
                    </a>
                    <a href="/backoffice/article/<?= $id ?>/supprimer"
                       class="btn btn-danger"
                       style="width:100%; margin-top:10px; text-align:center;"
                       onclick="return confirm('Supprimer définitivement cet article ?')">
                        Supprimer
                    </a>
                <?php endif; ?>
            </div>

            <!-- Catégorie -->
            <div class="card">
                <h3>Catégorie</h3>
                <select name="category_id"
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    <option value="">— Aucune —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (string)$v['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Image -->
            <div class="card">
                <h3>Image à la une</h3>

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">URL de l'image</label>
                    <input type="url" name="image_url"
                           value="<?= htmlspecialchars($v['image_url']) ?>"
                           placeholder="https://..."
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:13px;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; color:#555; margin-bottom:6px;">
                        Texte alternatif (alt) <span style="font-size:11px; color:#aaa;">— SEO & accessibilité</span>
                    </label>
                    <input type="text" name="image_alt"
                           value="<?= htmlspecialchars($v['image_alt']) ?>"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:13px;">
                </div>
            </div>

        </div>
    </div>

</form>

<script src="/assets/tinymce/tinymce.min.js" defer></script>
<script>
function initTinyMce() {
    if (!window.tinymce) {
        return;
    }

    window.tinymce.init({
        selector: '#contenu',
        base_url: '/assets/tinymce',
        suffix: '.min',
        license_key: 'gpl',
        height: 500,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        content_style: 'body { font-family: Georgia, serif; font-size: 16px; line-height: 1.7; max-width: 800px; margin: 20px auto; }',
        promotion: false,
        branding: false
    });
}

document.querySelector('form').addEventListener('submit', function () {
    if (window.tinymce) {
        window.tinymce.triggerSave();
    }
});

window.addEventListener('load', initTinyMce);

// Génération automatique du slug depuis le titre
function genererSlug() {
    const titre = document.getElementById('titre').value;
    const slug = titre
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    document.getElementById('slug').value = slug;
}

// Auto-slug à la saisie du titre (seulement si slug vide)
document.getElementById('titre').addEventListener('input', function () {
    const slugField = document.getElementById('slug');
    if (slugField.value === '') genererSlug();
});
</script>

<?php include 'layout_footer.php'; ?>
