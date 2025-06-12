<?php
include 'conexao.php';

// Verifica o fornecedor_id passado pela URL
$fornecedor_id = $_GET['fornecedor_id'] ?? null;
if (!$fornecedor_id) {
    die("Fornecedor não encontrado.");
}

// ---------------------------------------------------------------------------------
// MELHORIA DE SEGURANÇA E LÓGICA DE FILTROS COM PREPARED STATEMENTS
// ---------------------------------------------------------------------------------

// Consulta o fornecedor de forma segura
$stmt_fornecedor = $con->prepare("SELECT nome FROM fornecedores WHERE id = ?");
$stmt_fornecedor->bind_param("i", $fornecedor_id);
$stmt_fornecedor->execute();
$fornecedor = $stmt_fornecedor->get_result()->fetch_assoc();
if (!$fornecedor) {
    die("ID de fornecedor inválido.");
}

// Captura os filtros e a ordenação da URL
$ano_filtro = $_GET['ano_filtro'] ?? ''; // <-- MELHORIA: Filtro de ano
$mes_fatura = $_GET['mes_fatura'] ?? '';
$data_emissao_inicio = $_GET['data_emissao_inicio'] ?? '';
$data_emissao_fim = $_GET['data_emissao_fim'] ?? '';
$data_vencimento_inicio = $_GET['data_vencimento_inicio'] ?? '';
$data_vencimento_fim = $_GET['data_vencimento_fim'] ?? '';
$valor_min = $_GET['valor_min'] ?? '';
$valor_max = $_GET['valor_max'] ?? '';
$ordenar_por = $_GET['ordenar_por'] ?? 'mes'; // <-- MELHORIA: Ordenar por 'mes' ou 'ano'
$ordem = $_GET['ordem'] ?? 'asc';

// Base da consulta SQL segura
// ALTERAÇÃO: Adicionamos YEAR(data_emissao) as ano
$sql = "SELECT *, YEAR(data_emissao) as ano FROM faturas";
$where_clauses = ["fornecedor_id = ?"];
$params = [$fornecedor_id];
$types = "i";

// Adiciona os filtros à consulta dinamicamente e de forma segura
if ($ano_filtro) {
    $where_clauses[] = "YEAR(data_emissao) = ?";
    $params[] = $ano_filtro;
    $types .= 'i';
}
if ($mes_fatura) {
    $where_clauses[] = "mes = ?";
    $params[] = $mes_fatura;
    $types .= 's';
}
if ($data_emissao_inicio) {
    $where_clauses[] = "data_emissao >= ?";
    $params[] = $data_emissao_inicio;
    $types .= 's';
}
if ($data_emissao_fim) {
    $where_clauses[] = "data_emissao <= ?";
    $params[] = $data_emissao_fim;
    $types .= 's';
}
if ($data_vencimento_inicio) {
    $where_clauses[] = "data_vencimento >= ?";
    $params[] = $data_vencimento_inicio;
    $types .= 's';
}
if ($data_vencimento_fim) {
    $where_clauses[] = "data_vencimento <= ?";
    $params[] = $data_vencimento_fim;
    $types .= 's';
}
if ($valor_min) {
    $where_clauses[] = "valor >= ?";
    $params[] = $valor_min;
    $types .= 'd'; // 'd' for double/decimal
}
if ($valor_max) {
    $where_clauses[] = "valor <= ?";
    $params[] = $valor_max;
    $types .= 'd';
}

// Junta todas as cláusulas WHERE
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Adiciona a ordenação de forma segura
$ordem_sql = strtoupper($ordem) === 'DESC' ? 'DESC' : 'ASC'; // Garante que a ordem é 'ASC' ou 'DESC'
if ($ordenar_por === 'ano') {
    $sql .= " ORDER BY ano $ordem_sql, FIELD(mes, 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro')";
} else { // Padrão é ordenar por mês
    $sql .= " ORDER BY FIELD(mes, 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro') $ordem_sql, ano ASC";
}

// Executa a consulta com os filtros aplicados
$stmt = $con->prepare($sql);
if ($stmt && count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$faturas = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Faturas - <?= htmlspecialchars($fornecedor['nome']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <?php if (isset($_GET['excluido']) && $_GET['excluido'] == '1'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Fatura excluída com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <h1 class="mb-4 text-center">📑 Faturas de <?= htmlspecialchars($fornecedor['nome']) ?></h1>

    <form method="get" class="mb-4 p-4 border rounded bg-white">
        <input type="hidden" name="fornecedor_id" value="<?= $fornecedor_id ?>">
        <div class="row g-3">
            <div class="col-md-2">
                <label for="ano_filtro" class="form-label">Ano</label>
                <input type="number" name="ano_filtro" id="ano_filtro" class="form-control" placeholder="Ano" value="<?= htmlspecialchars($ano_filtro) ?>">
            </div>
            <div class="col-md-2">
                <label for="mes_fatura" class="form-label">Mês da Fatura</label>
                <input type="text" name="mes_fatura" id="mes_fatura" class="form-control" placeholder="Mês" value="<?= htmlspecialchars($mes_fatura) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_emissao_inicio" class="form-label">Emissão (Início)</label>
                <input type="date" name="data_emissao_inicio" id="data_emissao_inicio" class="form-control" value="<?= htmlspecialchars($data_emissao_inicio) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_emissao_fim" class="form-label">Emissão (Fim)</label>
                <input type="date" name="data_emissao_fim" id="data_emissao_fim" class="form-control" value="<?= htmlspecialchars($data_emissao_fim) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_vencimento_inicio" class="form-label">Vencimento (Início)</label>
                <input type="date" name="data_vencimento_inicio" id="data_vencimento_inicio" class="form-control" value="<?= htmlspecialchars($data_vencimento_inicio) ?>">
            </div>
            <div class="col-md-2">
                <label for="data_vencimento_fim" class="form-label">Vencimento (Fim)</label>
                <input type="date" name="data_vencimento_fim" id="data_vencimento_fim" class="form-control" value="<?= htmlspecialchars($data_vencimento_fim) ?>">
            </div>
            <div class="col-md-2">
                <label for="valor_min" class="form-label">Valor Mínimo</label>
                <input type="number" name="valor_min" id="valor_min" class="form-control" placeholder="Valor Mín." value="<?= htmlspecialchars($valor_min) ?>" step="0.01">
            </div>
            <div class="col-md-2">
                <label for="valor_max" class="form-label">Valor Máximo</label>
                <input type="number" name="valor_max" id="valor_max" class="form-control" placeholder="Valor Máx." value="<?= htmlspecialchars($valor_max) ?>" step="0.01">
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
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Mês
                        <a href="?fornecedor_id=<?= $fornecedor_id ?>&ordenar_por=mes&ordem=asc" class="text-white ms-1"><i class="fas fa-sort-up"></i></a>
                        <a href="?fornecedor_id=<?= $fornecedor_id ?>&ordenar_por=mes&ordem=desc" class="text-white"><i class="fas fa-sort-down"></i></a>
                    </th>
                    <th>Ano
                        <a href="?fornecedor_id=<?= $fornecedor_id ?>&ordenar_por=ano&ordem=asc" class="text-white ms-1"><i class="fas fa-sort-up"></i></a>
                        <a href="?fornecedor_id=<?= $fornecedor_id ?>&ordenar_por=ano&ordem=desc" class="text-white"><i class="fas fa-sort-down"></i></a>
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
                        <td><?= htmlspecialchars($f['mes']) ?></td>
                        <td><?= htmlspecialchars($f['ano']) ?></td>
                        <td><?= date('d/m/Y', strtotime($f['data_emissao'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($f['data_vencimento'])) ?></td>
                        <td>R$ <?= number_format($f['valor'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($f['detalhes'] ?? '') ?></td>
                        <td><?= (int)($f['filial'] ?? '') ?></td>
                        <td><?= !empty($f['pedido']) ? (int)$f['pedido'] : '' ?></td>
                        <td>
                            <a href="editar_fatura.php?id=<?= $f['id'] ?>&fornecedor_id=<?= $fornecedor_id ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="excluir_fatura.php?id=<?= $f['id'] ?>&fornecedor_id=<?= $fornecedor_id ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tem certeza que deseja excluir esta fatura? Esta ação não poderá ser desfeita.');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Não há faturas cadastradas para este fornecedor (ou que correspondam aos filtros aplicados).
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="visualizar_fornecedores.php" class="btn btn-secondary">← Lista de Fornecedores</a>
        <a href="index.php" class="btn btn-secondary ms-2">🏠 Voltar ao Início</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>