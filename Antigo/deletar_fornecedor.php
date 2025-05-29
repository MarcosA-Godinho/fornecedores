<?php
include 'conexao.php';

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    echo "ID do fornecedor não especificado.";
    exit;
}

$id = $_GET['id'];

// Inicia a transação para garantir que todas as exclusões aconteçam de forma segura
$con->begin_transaction();

try {
    // Primeiro, exclui todas as faturas associadas ao fornecedor
    $stmt_faturas = $con->prepare("DELETE FROM faturas WHERE fornecedor_id = ?");
    $stmt_faturas->bind_param("i", $id);
    if (!$stmt_faturas->execute()) {
        throw new Exception("Erro ao excluir faturas.");
    }

    // Depois, exclui o fornecedor
    $stmt_fornecedor = $con->prepare("DELETE FROM fornecedores WHERE id = ?");
    $stmt_fornecedor->bind_param("i", $id);
    if (!$stmt_fornecedor->execute()) {
        throw new Exception("Erro ao excluir fornecedor.");
    }

    // Commit da transação
    $con->commit();

    // Redireciona para a página de fornecedores com a mensagem de sucesso
    header("Location: visualizar_fornecedores.php?msg=Fornecedor+e+faturas+excluídos+com+sucesso");
    exit;

} catch (Exception $e) {
    // Se algo der errado, desfaz todas as alterações
    $con->rollback();
    echo "Erro: " . $e->getMessage();
}
