<?php
$host = 'postgres';
$db = 'mon_db';
$user = 'user';
$password = 'password';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $password);
    echo "Connexion réussie à PostgreSQL 🚀";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}