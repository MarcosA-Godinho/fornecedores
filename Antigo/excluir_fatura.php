<?php
// ✅ Inclui a conexão com o banco
include 'conexao.php';

// ✅ Recebe os parâmetros pela URL
$fatura_id = $_GET['id'] ?? null;
$fornecedor_id = $_GET['fornecedor_id'] ?? null;

// ✅ Valida se os parâmetros foram enviados corretamente
if (!$fatura_id || !$fornecedor_id) {
    die("Parâmetros inválidos.");
}

// ✅ Prepara a query para excluir a fatura com segurança
$stmt = $con->prepare("DELETE FROM faturas WHERE id = ?");
$stmt->bind_param("i", $fatura_id);

// ✅ Executa a exclusão
if ($stmt->execute()) {
    // ✅ Redireciona de volta para faturas.php com parâmetro de sucesso
    header("Location: faturas.php?fornecedor_id=$fornecedor_id&excluido=1");
    exit;
} else {
    // ⚠️ Mostra erro se a exclusão falhar
    echo "Erro ao excluir fatura: " . $stmt->error;
}
