<?php
include 'conexao.php';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fornecedor_id = (int)$_POST['fornecedor_id'];
    $mes = $_POST['mes'];
    $data_emissao = $_POST['data_emissao'];
    $data_vencimento = $_POST['data_vencimento'];
    $valor = floatval($_POST['valor']);
    $detalhes = $_POST['detalhes'] ?? '';
    $filial = (int)$_POST['filial'];
    $pedido = (int)$_POST['pedido'];

    $stmt = $con->prepare("INSERT INTO faturas (fornecedor_id, mes, data_emissao, data_vencimento, valor, detalhes, filial, pedido)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssdii", $fornecedor_id, $mes, $data_emissao, $data_vencimento, $valor, $detalhes, $filial, $pedido);

    if ($stmt->execute()) {
        header("Location: index.php?msg=Fatura+lancada+com+sucesso");
        exit;
    } else {
        die("Erro ao lançar fatura: " . $con->error);
    }
}

// Buscar todos os fornecedores para o select
$fornecedores = $con->query("SELECT id, nome, codigo FROM fornecedores ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lançar Nova Fatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4 text-center">➕ Lançar Nova Fatura</h1>

    <form method="post" class="col-md-6 offset-md-3">
        <!-- Fornecedor -->
        <div class="mb-3">
            <label class="form-label">Fornecedor</label>
            <select name="fornecedor_id" class="form-select" required>
                <option value="">Selecione</option>
                <?php while ($f = $fornecedores->fetch_assoc()): ?>
                    <option value="<?= $f['id'] ?>"> 
						<?= htmlspecialchars($f['nome']) ?> (Cód: <?= htmlspecialchars($f['codigo']) ?>)
					</option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Mês -->
        <div class="mb-3">
            <label class="form-label">Mês</label>
            <select name="mes" class="form-select" required>
                <?php
                $meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
                foreach ($meses as $m) echo "<option value=\"$m\">$m</option>";
                ?>
            </select>
        </div>

        <!-- Data de Emissão -->
        <div class="mb-3">
            <label class="form-label">Data de Emissão</label>
            <input type="date" name="data_emissao" class="form-control" required>
        </div>

        <!-- Data de Vencimento -->
        <div class="mb-3">
            <label class="form-label">Data de Vencimento</label>
            <input type="date" name="data_vencimento" class="form-control" required>
        </div>

        <!-- Valor -->
        <div class="mb-3">
            <label class="form-label">Valor</label>
            <input type="number" name="valor" step="0.01" class="form-control" required>
        </div>

        <!-- Detalhes -->
        <div class="mb-3">
            <label class="form-label">Detalhes</label>
            <textarea name="detalhes" class="form-control"></textarea>
        </div>

        <!-- Filial -->
        <div class="mb-3">
            <label class="form-label">Filial</label>
            <input type="number" name="filial" class="form-control" required>
        </div>

        <!-- Pedido -->
        <div class="mb-4">
            <label class="form-label">Pedido</label>
            <input type="number" name="pedido" class="form-control">
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">💾 Lançar Fatura</button>
            <a href="index.php" class="btn btn-secondary mt-2">← Voltar</a>
        </div>
    </form>
</div>
</body>
</html>
