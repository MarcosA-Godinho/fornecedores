<?php
include 'conexao.php';

// 📅 Consulta para obter o último vencimento de cada fornecedor
$sql = "
    SELECT 
        f.nome AS fornecedor_nome,
        MAX(data_vencimento) AS ultimo_vencimento
    FROM faturas ft
    JOIN fornecedores f ON f.id = ft.fornecedor_id
    GROUP BY f.id
    ORDER BY ultimo_vencimento ASC
";
$result = $con->query($sql);
?>

<!-- 🔔 Bloco de Lembretes de Vencimento -->
<div class="card p-3 bg-warning-subtle border-warning">
    <h5 class="mb-3">🔔 Lembretes de Vencimento</h5>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()):
            $nome = $row['fornecedor_nome'];
            $ultimoVenc = $row['ultimo_vencimento'];

            if ($ultimoVenc) {
                $proxVenc = date('d/m/Y', strtotime("+1 month", strtotime($ultimoVenc)));
                echo "<p>💡 <strong>$nome</strong> irá vencer dia <strong>$proxVenc</strong>!</p>";
            }
        endwhile; ?>
    <?php else: ?>
        <p>Nenhum vencimento registrado ainda.</p>
    <?php endif; ?>
</div>
