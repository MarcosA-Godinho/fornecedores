<?php
include 'conexao.php';

// Verifica se foi passado o ID
if (!isset($_GET['id'])) {
    echo "ID do fornecedor não especificado.";
    exit;
}

$id = $_GET['id'];

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $codigo = $_POST['codigo'];

    // Atualiza o fornecedor
    $stmt = $con->prepare("UPDATE fornecedores SET nome = ?, codigo = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nome, $codigo, $id);

    if ($stmt->execute()) {
        header("Location: visualizar_fornecedores.php?msg=Fornecedor+atualizado+com+sucesso");
        exit;
    } else {
        echo "Erro ao atualizar fornecedor.";
    }
}

// Busca os dados do fornecedor atual
$result = $con->query("SELECT * FROM fornecedores WHERE id = $id");
$fornecedor = $result->fetch_assoc();

if (!$fornecedor) {
    echo "Fornecedor não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Fornecedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4 text-center">✏️ Editar Fornecedor</h1>

    <form method="post" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Fornecedor</label>
            <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($fornecedor['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" name="codigo" id="codigo" class="form-control" value="<?= htmlspecialchars($fornecedor['codigo']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
        <a href="visualizar_fornecedores.php" class="btn btn-secondary">← Cancelar</a>
    </form>
</div>
</body>
</html>
