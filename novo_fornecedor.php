<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $codigo = $_POST['codigo'];

    $con->query("INSERT INTO fornecedores (nome, codigo) VALUES ('$nome', '$codigo')");
    header("Location: visualizar_fornecedores.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Fornecedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center">➕ Cadastrar Novo Fornecedor</h1>

    <form method="post" class="col-md-6 offset-md-3">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Fornecedor</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="codigo" class="form-label">Código do Fornecedor</label>
            <input type="text" name="codigo" id="codigo" class="form-control" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="index.php" class="btn btn-secondary">← Voltar</a>
        </div>
    </form>
</div>

</body>
</html>
