<?php

namespace App\Core;

class Router {
    public function __construct() {
        //echo "Router class loaded!<br>"; // Para teste
		$this->addRoute('fornecedores', ['controller' => 'FornecedorController', 'action' => 'index']);

    }

    // Método básico para despachar (será expandido depois)
    public function dispatch($uri) {
        // Lógica do roteamento virá aqui
        echo "URI solicitada: " . htmlspecialchars($uri);
    }
}