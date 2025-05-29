<?php

// Carrega o autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Teste do Router
$url = $_GET['url'] ?? '/'; // Pega a URL da query string (definida no .htaccess da pasta public)

$router = new App\Core\Router();
$router->dispatch($url);

?>