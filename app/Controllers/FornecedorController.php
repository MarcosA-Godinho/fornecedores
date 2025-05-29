<?php

namespace App\Controllers;

// Indica que usaremos a classe FornecedorModel do namespace App\Models
use App\Models\FornecedorModel;

class FornecedorController {

    /**
     * Método principal para listar os fornecedores.
     */
    public function index() {
        // Cria uma instância do FornecedorModel
        $fornecedorModel = new FornecedorModel();

        // Busca todos os fornecedores através do Model
        $fornecedores = $fornecedorModel->listarTodos();

        // Define o título da página (opcional, mas útil para a view)
        $titulo = "Lista de Fornecedores";

        // Carrega a View para exibir os fornecedores.
        // Passamos as variáveis $titulo e $fornecedores para a view.
        require_once __DIR__ . '/../Views/fornecedores/index.php';
    }

    // Futuramente, aqui entrarão métodos como:
    // public function criar() { /* Carrega view do formulário de novo fornecedor */ }
    // public function salvar() { /* Processa dados do POST e chama o Model */ }
    // public function editar($id) { /* Carrega model, busca por ID, carrega view do formulário de edição */ }
    // public function atualizar($id) { /* Processa dados do POST e chama o Model para atualizar */ }
    // public function deletar($id) { /* Chama o Model para deletar */ }
}