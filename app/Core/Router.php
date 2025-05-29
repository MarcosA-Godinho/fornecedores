<?php

namespace App\Core;

class Router {
    protected $routes = [];
    protected $params = [];

    public function __construct() {
        // Rota para listar fornecedores
        $this->addRoute('fornecedores', ['controller' => 'FornecedorController', 'action' => 'index']);

        // Adicione outras rotas aqui conforme necessário no futuro, por exemplo:
        // $this->addRoute('faturas', ['controller' => 'FaturaController', 'action' => 'index']);
        // $this->addRoute('fornecedores/novo', ['controller' => 'FornecedorController', 'action' => 'novo']);

        // DEPURAÇÃO: Verifique as rotas configuradas (descomente para testar)
        // echo "DEBUG: Rotas configuradas no construtor: <pre>"; var_dump($this->routes); echo "</pre><hr>";
    }

    public function addRoute($route, $params = []) {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    public function match($url) {
        // DEPURAÇÃO: Veja a URL que o método match está tentando encontrar (descomente para testar)
        // echo "DEBUG: Tentando match para URL (em match()): "; var_dump($url); echo "<hr>";

        foreach ($this->routes as $routeRegex => $params) {
            // DEPURAÇÃO: Veja cada rota regex sendo testada (descomente para testar)
            // echo "DEBUG: Testando URL contra regex da rota: "; var_dump($routeRegex); echo "<hr>";

            if (preg_match($routeRegex, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function dispatch($url) {
        // DEPURAÇÃO: URL original recebida pelo dispatch (descomente para testar)
        // echo "DEBUG: URL original recebida (em dispatch()): "; var_dump($url); echo "<hr>";

        $url = strtok($url, '?');
        // DEPURAÇÃO: URL após strtok (descomente para testar)
        // echo "DEBUG: URL após strtok(): "; var_dump($url); echo "<hr>";

        $url = trim($url, '/');
        // DEPURAÇÃO ESSENCIAL: Veja a URL final que será usada para o match
        echo "DEBUG: URL final para match (após trim): "; var_dump($url); echo "<hr>";

        // DEPURAÇÃO ESSENCIAL: Veja o array de rotas antes de chamar o match
        echo "DEBUG: Array de rotas completo (\$this->routes): <pre>"; var_dump($this->routes); echo "</pre><hr>";


        if ($this->match($url)) {
            $controllerName = $this->params['controller'];
            $controllerPath = "App\\Controllers\\" . $controllerName;

            if (class_exists($controllerPath)) {
                $controllerObject = new $controllerPath();
                $action = $this->params['action'];
                if (is_callable([$controllerObject, $action])) {
                    $controllerObject->$action();
                } else {
                    // O erro não deve ser aqui se a rota não foi encontrada antes
                    echo "ERRO: Método $action não encontrado no controller $controllerName";
                }
            } else {
                // O erro não deve ser aqui se a rota não foi encontrada antes
                echo "ERRO: Controller $controllerName não encontrado.";
            }
        } else {
            // Esta é a mensagem que você está vendo
            echo "Nenhuma rota encontrada para a URL: " . htmlspecialchars($url);
        }
    }
}