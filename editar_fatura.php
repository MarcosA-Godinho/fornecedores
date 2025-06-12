<?php
include 'conexao.php';

// Verifica se o ID da fatura foi passado na URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID da fatura não informado.");
}

// Consulta a fatura do banco de dados
$fatura = $con->query("SELECT * FROM faturas WHERE id = $id")->fetch_assoc();
if (!$fatura) {
    die("Fatura não encontrada.");
}

// Obtém o ID do fornecedor relacionado à fatura
$fornecedor_id = $fatura['fornecedor_id'];

// Processa o formulário quando o método for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mes = $_POST['mes'];
    $data_emissao = $_POST['data_emissao'];
    $data_vencimento = $_POST['data_vencimento'];
    $valor = $_POST['valor'];
    $detalhes = $_POST['detalhes']; // Captura o valor do campo "Detalhes" do formulário
    $filial = (int)$_POST['filial']; // ✅ Captura e converte o campo "Filial" para inteiro
	$pedido = (int)$_POST['pedido']; // ✅ Captura e converte o campo "Pedido" para inteiro

    // Atualiza a fatura no banco de dados
    $sql = "UPDATE faturas SET 
        mes = '$mes', 
        data_emissao = '$data_emissao', 
        data_vencimento = '$data_vencimento', 
        valor = $valor, 
        detalhes = '$detalhes',
        filial = $filial,
		pedido = $pedido
        WHERE id = $id";

    // Executa a consulta SQL
    if ($con->query($sql)) {
        // Redireciona para a página de faturas do fornecedor
        header("Location: faturas.php?fornecedor_id=$fornecedor_id");
        exit;
    } else {
        die("Erro ao atualizar a fatura: " . $con->error);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Fatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center">✏️ Editar Fatura</h1>

    <!-- Formulário de Edição de Fatura -->
    <form method="post" class="col-md-6 offset-md-3">
        <!-- Campo Mês -->
        <div class="mb-3">
            <label class="form-label">Mês</label>
            <select name="mes" class="form-select" required>
                <?php
                // Array com os meses para o select
                $meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
                // Loop para preencher o select
                foreach ($meses as $m) {
                    $sel = ($fatura['mes'] == $m) ? 'selected' : ''; // Marca o mês selecionado
                    echo "<option value=\"$m\" $sel>$m</option>";
                }
                ?>
            </select>
        </div>

        <!-- Campo Data de Emissão -->
        <div class="mb-3">
            <label class="form-label">Data de Emissão</label>
            <input type="date" name="data_emissao" class="form-control" value="<?= $fatura['data_emissao'] ?>" required>
        </div>

        <!-- Campo Data de Vencimento -->
        <div class="mb-3">
            <label class="form-label">Data de Vencimento</label>
            <input type="date" name="data_vencimento" class="form-control" value="<?= $fatura['data_vencimento'] ?>" required>
        </div>

        <!-- Campo Valor -->
        <div class="mb-4">
            <label class="form-label">Valor</label>
            <input type="number" name="valor" step="0.01" class="form-control" value="<?= $fatura['valor'] ?>" required>
        </div>

        <!-- Campo Detalhes -->
        <div class="mb-4">
            <label class="form-label">Detalhes (opcional)</label>
            <textarea name="detalhes" class="form-control" rows="3"><?= htmlspecialchars($fatura['detalhes']) ?></textarea>
        </div>

        <!-- Campo Filial -->
        <div class="mb-4">
            <label class="form-label">Filial</label>
            <input type="number" name="filial" class="form-control" value="<?= (int)$fatura['filial'] ?>" required>
            <!-- ✅ Campo novo para editar a filial (obrigatório preencher) -->
        </div>
		
		<!-- Campo Pedido -->
        <div class="mb-4">
            <label class="form-label">Pedido</label>
			<input type="number" name="pedido" class="form-control" value="<?= $fatura['pedido'] !== null ? (int)$fatura['pedido'] : '' ?>">
            <!-- ✅ Campo novo para editar a pedido (obrigatório preencher) -->
        </div>

        <!-- Botões de Ação -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            <a href="faturas.php?fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-secondary">← Voltar</a>
        </div>
    </form>
</div>

</body>
</html>

