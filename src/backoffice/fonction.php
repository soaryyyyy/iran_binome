<?php
require_once __DIR__ . '/../connexion.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function verifierConnexion($username, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mdp'])) {
        return $user;
    }
    return false;
}

function redirigerSiNonConnecte() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}