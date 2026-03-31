<?php
require_once 'fonction.php';
session_start();
redirigerSiNonConnecte();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    header("Location: /backoffice/articles");
    exit();
}

// Vérifier que l'article existe
$stmt = $pdo->prepare("SELECT id, titre FROM articles WHERE id = :id");
$stmt->execute(['id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    header("Location: /backoffice/articles");
    exit();
}

// Supprimer
$stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: /backoffice/articles?deleted=1");
exit();
