<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . ltrim($uri, '/');

$baseDir = __DIR__;
$templatesDir = $baseDir . '/templates';
$staticDir = $baseDir . '/static';

// Servir fichiers statiques
$staticFilePath = realpath($staticDir . $uri);
if ($staticFilePath && str_starts_with($staticFilePath, realpath($staticDir)) && file_exists($staticFilePath)) {
    $mimeType = mime_content_type($staticFilePath);
    header("Content-Type: $mimeType; charset=utf-8");
    readfile($staticFilePath);
    exit;
}

// Rediriger "/" vers index.php
if ($uri === '/') {
    $uri = '/index.php';
}

// Chemin complet du fichier PHP
$filePath = realpath($templatesDir . $uri);
if ($filePath && str_starts_with($filePath, realpath($templatesDir)) && file_exists($filePath)) {
    chdir($templatesDir); // Définir le répertoire courant sur templates/
    include $filePath;
} else {
    http_response_code(404);
    echo "<h1>404 - Page non trouvée</h1>";
}
