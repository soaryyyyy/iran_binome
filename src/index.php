<?php
require_once 'connexion.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    echo $user['username'];
}
