<?php
include 'conexao.php';

// Verifica o fornecedor_id passado pela URL
$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

// Consulta o fornecedor para exibir o nome
$fornecedor = $con->query("SELECT * FROM fornecedores WHERE id = $fornecedor_id")->fetch_assoc();

// Captura os filtros enviados pelo formulário
$mes_fatura = $_GET['mes_fatura'] ?? '';
$data_emissao_inicio = $_GET['data_emissao_inicio'] ?? '';
$data_emissao_fim = $_GET['data_emissao_fim'] ?? '';
$data_vencimento_inicio = $_GET['data_vencimento_inicio'] ?? '';
$data_vencimento_fim = $_GET['data_vencimento_fim'] ?? '';
$valor_min = $_GET['valor_min'] ?? '';
$valor_max = $_GET['valor_max'] ?? '';
$ordenacao_mes = $_GET['ordenacao_mes'] ?? 'asc'; // Ordenação padrão (ascendente)

// Construção da consulta com base nos filtros
$sql = "SELECT * FROM faturas WHERE fornecedor_id = $fornecedor_id";

// Adiciona os filtros à consulta SQL
if ($mes_fatura) {
    $sql .= " AND mes = '$mes_fatura'";
}
if ($data_emissao_inicio) {
    $sql .= " AND data_emissao >= '$data_emissao_inicio'";
}
if ($data_emissao_fim) {
    $sql .= " AND data_emissao <= '$data_emissao_fim'";
}
if ($data_vencimento_inicio) {
    $sql .= " AND data_vencimento >= '$data_vencimento_inicio'";
}
if ($data_vencimento_fim) {
    $sql .= " AND data_vencimento <= '$data_vencimento_fim'";
}
if ($valor_min) {
    $sql .= " AND valor >= $valor_min";
}
if ($valor_max) {
    $sql .= " AND valor <= $valor_max";
}

// Adiciona a ordenação pelo mês (crescente ou decrescente)
$sql .= " ORDER BY FIELD(mes, 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro') $ordenacao_mes";

// Executa a consulta com os filtros aplicados
$faturas = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Faturas - <?= $fornecedor['nome'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <!-- ✅ Mostra mensagem de sucesso ao excluir uma fatura -->
    <?php if (isset($_GET['excluido']) && $_GET['excluido'] == '1'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Fatura excluída com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <h1 class="mb-4 text-center">📑 Faturas de <?= $fornecedor['nome'] ?></h1>

    <!-- Formulário de Filtros -->
    <form method="get" class="mb-4">
        <input type="hidden" name="fornecedor_id" value="<?= $fornecedor_id ?>">

        <div class="row">
            <div class="col-md-2">
                <label for="mes_fatura" class="form-label">Mês da Fatura</label>
                <input type="text" name="mes_fatura" class="form-control" placeholder="Mês" value="<?= htmlspecialchars($mes_fatura) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_emissao_inicio" class="form-label">Data Emissão (Início)</label>
                <input type="date" name="data_emissao_inicio" class="form-control" value="<?= htmlspecialchars($data_emissao_inicio) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_emissao_fim" class="form-label">Data Emissão (Fim)</label>
                <input type="date" name="data_emissao_fim" class="form-control" value="<?= htmlspecialchars($data_emissao_fim) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_vencimento_inicio" class="form-label">Data Vencimento (Início)</label>
                <input type="date" name="data_vencimento_inicio" class="form-control" value="<?= htmlspecialchars($data_vencimento_inicio) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_vencimento_fim" class="form-label">Data Vencimento (Fim)</label>
                <input type="date" name="data_vencimento_fim" class="form-control" value="<?= htmlspecialchars($data_vencimento_fim) ?>">
            </div>
            <div class="col-md-2">
                <label for="valor_min" class="form-label">Valor Mínimo</label>
                <input type="number" name="valor_min" class="form-control" placeholder="Valor Min" value="<?= htmlspecialchars($valor_min) ?>" step="0.01">
            </div>
            <div class="col-md-2">
                <label for="valor_max" class="form-label">Valor Máximo</label>
                <input type="number" name="valor_max" class="form-control" placeholder="Valor Max" value="<?= htmlspecialchars($valor_max) ?>" step="0.01">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                <a href="faturas.php?fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-secondary">❌ Limpar Filtros</a>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-between mb-4">
        <div>
            <a href="exportar_pdf.php?fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-danger btn-lg"><i class="fas fa-file-pdf"></i> Exportar para PDF</a>
            <a href="exportar_excel.php?fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-success btn-lg"><i class="fas fa-file-excel"></i> Exportar para Excel</a>
        </div>
        <a href="cadastrar_fatura.php?fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-primary btn-lg">➕ Cadastrar Nova Fatura</a>
    </div>

    <?php if ($faturas->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mês 
                        <a href="faturas.php?fornecedor_id=<?= $fornecedor_id ?>&ordenacao_mes=asc" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sort-up"></i></a>
                        <a href="faturas.php?fornecedor_id=<?= $fornecedor_id ?>&ordenacao_mes=desc" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sort-down"></i></a>
                    </th>
                    <th>Data de Emissão</th>
                    <th>Data de Vencimento</th>
                    <th>Valor</th>
                    <th>Detalhes</th>
                    <th>Filial</th>
                    <th>Pedido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($f = $faturas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $f['mes'] ?></td>
                        <td><?= date('d/m/Y', strtotime($f['data_emissao'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($f['data_vencimento'])) ?></td>
                        <td>R$ <?= number_format($f['valor'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($f['detalhes'] ?? '') ?></td>
                        <td><?= (int)($f['filial'] ?? '') ?></td>
                        <td><?= !empty($f['pedido']) ? (int)$f['pedido'] : '' ?></td>
                        <td>
                            <a href="editar_fatura.php?id=<?= $f['id'] ?>&fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>

                            <!-- ✅ Confirmação antes de excluir -->
                            <a href="excluir_fatura.php?id=<?= $f['id'] ?>&fornecedor_id=<?= $fornecedor_id ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tem certeza que deseja excluir esta fatura? Esta ação não poderá ser desfeita.');">
                                <i class="fas fa-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Não há faturas cadastradas para este fornecedor.
        </div>
    <?php endif; ?>
    
    <div class="mt-4 text-center">
        <a href="visualizar_fornecedores.php" class="btn btn-secondary">← Lista de Fornecedores</a>
    </div>
    <div class="mt-4 text-center">
        <a href="index.php" class="btn btn-secondary">← Voltar ao Início</a>
    </div>

</div>

<!-- ✅ Scripts do Bootstrap para funcionamento do botão fechar -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
