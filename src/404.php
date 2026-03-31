<?php
require_once __DIR__ . '/connexion.php';

$homeUrl = rtrim(getBaseUrl(), '/') . '/';
$canonicalUrl = getCurrentUrl();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable - Iran Actualites</title>
    <meta name="description" content="La page demandee est introuvable. Retournez a l'accueil du site Iran Actualites.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f5f5f0;
            color: #1a1a2e;
            font-family: Georgia, 'Times New Roman', serif;
        }

        main {
            max-width: 640px;
            background: #fff;
            border-radius: 8px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        h1 {
            font-size: 34px;
            margin-bottom: 12px;
        }

        p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        a {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 6px;
            background: #1a1a2e;
            color: #fff;
            text-decoration: none;
        }

        a:hover {
            background: #e8a020;
        }
    </style>
</head>
<body>
    <main>
        <h1>Erreur 404</h1>
        <p>La page que vous cherchez n'existe pas ou n'est plus disponible.</p>
        <a href="<?= htmlspecialchars($homeUrl) ?>">Retour a l'accueil</a>
    </main>
</body>
</html>
