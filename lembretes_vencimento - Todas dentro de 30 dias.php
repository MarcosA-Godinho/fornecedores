<?php
include 'conexao.php';

$sql = "
    SELECT 
        f.nome AS fornecedor_nome,
        ft.data_vencimento
    FROM faturas ft
    JOIN fornecedores f ON f.id = ft.fornecedor_id
    WHERE ft.data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
    ORDER BY f.nome, ft.data_vencimento
";

$result = $con->query($sql);
?>

<div class="card p-3 bg-warning-subtle border-warning">
    <h5 class="mb-3">🔔 Lembretes de Vencimento</h5>
    <?php if ($result->num_rows > 0): ?>
        <?php 
        $atualFornecedor = '';
        while ($row = $result->fetch_assoc()):
            $nome = $row['fornecedor_nome'];
            $vencimento = date('d/m/Y', strtotime($row['data_vencimento']));

            if ($nome !== $atualFornecedor) {
                if ($atualFornecedor !== '') echo "<br>";
                echo "<strong>💼 $nome</strong><br>";
                $atualFornecedor = $nome;
            }

            echo "💡 Fatura vence dia <strong>$vencimento</strong><br>";
        endwhile; ?>
    <?php else: ?>
        <p>Nenhum vencimento nos próximos 30 dias.</p>
    <?php endif; ?>
</div>
