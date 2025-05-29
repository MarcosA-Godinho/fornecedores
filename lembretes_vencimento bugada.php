<?php
include 'conexao.php';

// Buscar todas as faturas futuras (próximos 30 dias), ordenadas pelo vencimento mais próximo
$sql = "
    SELECT 
        f.nome AS fornecedor_nome,
        ft.data_vencimento
    FROM faturas ft
    JOIN fornecedores f ON f.id = ft.fornecedor_id
    WHERE ft.data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
    ORDER BY ft.data_vencimento ASC
";

$result = $con->query($sql);
?>

<!-- Estilo opcional para melhorar visual -->
<style>
    .lembrete-lista {
        max-height: 600px;
        overflow-y: auto;
    }

    .fornecedor-titulo {
        font-weight: bold;
        margin-top: 10px;
    }

    .oculto {
        display: none;
    }

    .btn-toggle {
        cursor: pointer;
        color: #0d6efd;
        border: none;
        background: none;
        padding: 0;
        font-size: 0.9rem;
    }

    .btn-toggle:hover {
        text-decoration: underline;
    }
</style>

<!-- Painel de Lembretes -->
<div class="card p-3 bg-warning-subtle border-warning">
    <h5 class="mb-3">🔔 Lembretes de Vencimento</h5>

    <?php if ($result->num_rows > 0): ?>
        <div class="lembrete-lista">
            <?php
            $atualFornecedor = '';
            $contador = 0;
            $html = '';
            while ($row = $result->fetch_assoc()):
                $nome = $row['fornecedor_nome'];
                $vencimento = date('d/m/Y', strtotime($row['data_vencimento']));

                if ($nome !== $atualFornecedor) {
                    if ($atualFornecedor !== '') {
                        $html .= "</ul>";
                    }

                    $html .= "<div class='fornecedor-titulo'>💼 $nome</div><ul>";
                    $atualFornecedor = $nome;
                    $contador = 0;
                }

                $classeExtra = $contador >= 6 ? "oculto" : "";
                $html .= "<li class='$classeExtra'>💡 Fatura vence em <strong>$vencimento</strong></li>";
                $contador++;

            endwhile;

            $html .= "</ul>";
            echo $html;
            ?>
        </div>

        <!-- Botões para mostrar/esconder -->
        <div class="mt-2">
            <button class="btn-toggle" onclick="mostrarMais()">🔽 Ver mais...</button>
            <button class="btn-toggle oculto" onclick="mostrarMenos()">🔼 Ver menos</button>
        </div>

        <!-- JS para alternar visibilidade -->
        <script>
            function mostrarMais() {
                document.querySelectorAll('.lembrete-lista li.oculto').forEach(el => el.style.display = 'list-item');
                document.querySelector('.btn-toggle[onclick="mostrarMais()"]').classList.add('oculto');
                document.querySelector('.btn-toggle[onclick="mostrarMenos()"]').classList.remove('oculto');
            }

            function mostrarMenos() {
                document.querySelectorAll('.lembrete-lista li.oculto').forEach(el => el.style.display = 'none');
                document.querySelector('.btn-toggle[onclick="mostrarMais()"]').classList.remove('oculto');
                document.querySelector('.btn-toggle[onclick="mostrarMenos()"]').classList.add('oculto');
            }

            window.addEventListener('DOMContentLoaded', () => {
                mostrarMenos();
            });
        </script>
    <?php else: ?>
        <p>Nenhum vencimento nos próximos 30 dias.</p>
    <?php endif; ?>
</div>
