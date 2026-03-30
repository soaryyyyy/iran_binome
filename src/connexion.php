<?php
$host = 'postgres';
$db = 'mon_db';
$user = 'user';
$password = 'password';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $password);
    
    // Options importantes
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}