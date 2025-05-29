<?php
include 'conexao.php';

$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

$fornecedor = $con->query("SELECT * FROM fornecedores WHERE id = $fornecedor_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mes = $_POST['mes'];
    $data_emissao = $_POST['data_emissao'];
    $data_vencimento = $_POST['data_vencimento'];
    $valor = $_POST['valor'];
    $detalhes = $_POST['detalhes']; // ✅ Campo já existente
    $filial = $_POST['filial'];     // ✅ Campo filial (caso já exista)
    $pedido = $_POST['pedido'];     // ✅ Novo campo "pedido" capturado do formulário

    // ✅ Campo "pedido" incluído na instrução SQL
    $sql = "INSERT INTO faturas (fornecedor_id, mes, data_emissao, data_vencimento, valor, detalhes, filial, pedido)
            VALUES ($fornecedor_id, '$mes', '$data_emissao', '$data_vencimento', '$valor', '$detalhes', $filial, '$pedido')";

    if ($con->query($sql) === TRUE) {
        echo "<script>alert('Fatura cadastrada com sucesso!'); window.location.href='faturas.php?fornecedor_id=$fornecedor_id';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar fatura.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Fatura - <?= $fornecedor['nome'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center">➕ Cadastrar Nova Fatura</h1>

    <form action="cadastrar_fatura.php?fornecedor_id=<?= $fornecedor_id ?>" method="POST">
        <!-- Campo Mês -->
        <div class="mb-3">
            <label for="mes" class="form-label">Mês</label>
            <select class="form-select" id="mes" name="mes" required>
                <?php
                $meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
                foreach ($meses as $m) {
                    echo "<option value=\"$m\">$m</option>";
                }
                ?>
            </select>
        </div>

        <!-- Campo Data de Emissão -->
        <div class="mb-3">
            <label for="data_emissao" class="form-label">Data de Emissão</label>
            <input type="date" class="form-control" id="data_emissao" name="data_emissao" required>
        </div>

        <!-- Campo Data de Vencimento -->
        <div class="mb-3">
            <label for="data_vencimento" class="form-label">Data de Vencimento</label>
            <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" required>
        </div>

        <!-- Campo Valor -->
        <div class="mb-3">
            <label for="valor" class="form-label">Valor</label>
            <input type="number" class="form-control" id="valor" name="valor" step="0.01" required>
        </div>

        <!-- Campo Detalhes -->
        <div class="mb-3">
            <label for="detalhes" class="form-label">Detalhes (opcional)</label>
            <textarea class="form-control" id="detalhes" name="detalhes" rows="3"></textarea>
        </div>

        <!-- ✅ Campo Filial -->
        <div class="mb-3">
            <label for="filial" class="form-label">Filial</label>
            <input type="number" class="form-control" id="filial" name="filial" required>
        </div>

        <!-- ✅ Novo Campo Pedido -->
        <div class="mb-3">
            <label for="pedido" class="form-label">Pedido (opcional)</label>
            <input type="text" class="form-control" id="pedido" name="pedido">
        </div>

        <!-- Botões de ação -->
        <button type="submit" class="btn btn-primary">Cadastrar Fatura</button>
        <a href="faturas.php?fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

</body>
</html>
