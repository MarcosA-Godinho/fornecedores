<?php

namespace App\Models;

use mysqli; // Para type hinting da conexão

class FornecedorModel {
    private $db; // Armazenará a conexão mysqli

    public function __construct() {
        // Obtém a instância da conexão mysqli da nossa classe Database
        $this->db = \App\Core\Database::getInstance();
    }

    /**
     * Busca todos os fornecedores no banco de dados.
     * @return array Lista de fornecedores ou array vazio em caso de falha.
     */
    public function listarTodos(): array {
        // Ajustamos a query para usar as colunas da sua tabela: id, nome, codigo, e as novas colunas
        $sql = "SELECT id, nome, codigo, contato, email, telefone FROM fornecedores ORDER BY nome ASC";
        $result = $this->db->query($sql);

        if (!$result) {
            // Em um cenário real, você poderia logar o erro:
            // error_log("Erro na query listarTodos (FornecedorModel): " . $this->db->error);
            return []; // Retorna array vazio em caso de erro
        }

        // MYSQLI_ASSOC faz com que cada linha seja um array associativo (nome_coluna => valor)
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Futuramente, aqui entrarão métodos como:
    // public function buscarPorId($id) { ... }
    // public function salvar($dados) { ... }
    // public function atualizar($id, $dados) { ... }
    // public function deletar($id) { ... }
}