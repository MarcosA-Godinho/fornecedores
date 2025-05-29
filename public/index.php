<?php

// Carrega o autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Pega a URL da query string (definida no .htaccess da pasta public)
$url = $_GET['url'] ?? '/'; // Se 'url' não estiver definida, usa '/'

// Cria uma instância do Router
$router = new App\Core\Router();

// Envia a URL para o método dispatch do Router
$router->dispatch($url);

?>