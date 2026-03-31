<?php
require_once __DIR__ . '/connexion.php';

header('Content-Type: text/plain; charset=UTF-8');

$lines = [
    'User-agent: *',
    'Allow: /',
    'Sitemap: ' . rtrim(getBaseUrl(), '/') . '/sitemap.xml',
];

if (!isLocalRequest()) {
    array_splice($lines, 2, 0, 'Disallow: /backoffice/');
}

echo implode(PHP_EOL, $lines) . PHP_EOL;
