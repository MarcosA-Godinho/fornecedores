<?php
include 'conexao.php';

// Captura a pesquisa (se houver), o filtro (se houver), e a ordem (se houver)
$pesquisa = $_GET['pesquisa'] ?? ''; // Campo de pesquisa
$filtro = $_GET['filtro'] ?? ''; // Filtro (nome ou código)
$ordem = $_GET['ordem'] ?? ''; // Ordem de exibição (crescente ou decrescente)

// Prepara a consulta com filtros
$sql = "SELECT * FROM fornecedores WHERE 1"; // Consulta básica

// Adiciona os filtros de acordo com o que o usuário escolheu
if ($pesquisa) {
    if ($filtro === 'nome') {
        $sql .= " AND nome LIKE '%$pesquisa%'"; // Filtro por nome
    } elseif ($filtro === 'codigo') {
        $sql .= " AND codigo LIKE '%$pesquisa%'"; // Filtro por código
    }
}

// Adiciona a ordenação alfabética
if ($ordem) {
    if ($ordem === 'asc') {
        $sql .= " ORDER BY nome ASC"; // Ordem crescente (A-Z)
    } elseif ($ordem === 'desc') {
        $sql .= " ORDER BY nome DESC"; // Ordem decrescente (Z-A)
    }
} else {
    $sql .= " ORDER BY nome ASC"; // Caso nenhum filtro de ordem seja escolhido, padrão é crescente (A-Z)
}

// Executa a consulta filtrada
$fornecedores = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center">📋 Lista de Fornecedores</h1>

    <!-- Formulário de Pesquisa, Filtro e Ordem -->
    <form method="get" class="mb-4">
        <div class="row">
            <!-- Campo de Pesquisa -->
            <div class="col-md-6">
                <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar fornecedor..." value="<?= htmlspecialchars($pesquisa) ?>">
            </div>
            <!-- Filtro de Nome ou Código -->
            <div class="col-md-3">
                <select name="filtro" class="form-select">
                    <option value="nome" <?= $filtro === 'nome' ? 'selected' : '' ?>>Nome</option>
                    <option value="codigo" <?= $filtro === 'codigo' ? 'selected' : '' ?>>Código</option>
                </select>
            </div>
            <!-- Filtro de Ordem (Crescente ou Decrescente) -->
            <div class="col-md-2">
                <select name="ordem" class="form-select">
                    <option value="asc" <?= $ordem === 'asc' ? 'selected' : '' ?>>Ordem A-Z</option>
                    <option value="desc" <?= $ordem === 'desc' ? 'selected' : '' ?>>Ordem Z-A</option>
                </select>
            </div>
            <!-- Botões de Pesquisa e Limpar Filtro -->
            <div class="col-md-1 d-flex">
                <button type="submit" class="btn btn-primary w-100 btn-sm">🔍 Buscar</button>
                <a href="visualizar_fornecedores.php" class="btn btn-secondary w-100 ms-2 btn-sm">❌ Limpar</a>
            </div>
        </div>
    </form>

    <?php if ($fornecedores->num_rows > 0): ?>
        <div class="list-group">
            <?php while($f = $fornecedores->fetch_assoc()): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= $f['nome'] ?></strong> <span class="text-muted">(Código: <?= $f['codigo'] ?>)</span>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="faturas.php?fornecedor_id=<?= $f['id'] ?>" class="btn btn-outline-primary btn-sm">🧾 Ver Faturas</a>
                        <a href="editar_fornecedor.php?id=<?= $f['id'] ?>" class="btn btn-outline-warning btn-sm">✏️ Editar Fornecedor</a>
                        <a href="deletar_fornecedor.php?id=<?= $f['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deseja deletar o fornecedor e todos os dados?');">🗑 Deletar Fornecedor</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Nenhum fornecedor encontrado.
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="index.php" class="btn btn-secondary">← Voltar ao Início</a>
    </div>
</div>

</body>
</html>
