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

function getBaseUrl(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $forwardedProto === 'https';
    $scheme = $isHttps ? 'https' : 'http';

    return $scheme . '://' . $host;
}

function getCurrentUrl(): string
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    return getBaseUrl() . $requestUri;
}

function isLocalRequest(): bool
{
    $host = strtolower($_SERVER['HTTP_HOST'] ?? 'localhost');
    $host = explode(':', $host)[0];

    return in_array($host, ['localhost', '127.0.0.1'], true);
}
