<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Início - Sistema de Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 🔧 Estilo do quadro de lembretes */
        #lembretes-box {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 500px;
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        #lembretes-box h5 {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light">

    <!-- 🟡 Quadro flutuante de lembretes no canto superior esquerdo -->
    <div id="lembretes-box">
        <?php include 'lembretes_vencimento.php'; ?>
    </div>

    <!-- 🔵 Conteúdo principal centralizado -->
    <div class="container py-5 text-center">
        <h1 class="mb-4">📁 Sistema de Fornecedores</h1>

        <div class="d-grid gap-3 col-6 mx-auto">
            <a href="lancar_fatura.php" class="btn btn-success btn-lg">➕ Lançar Fatura</a>
            <a href="novo_fornecedor.php" class="btn btn-warning btn-lg">➕ Cadastrar Novo Fornecedor</a>
            <a href="visualizar_fornecedores.php" class="btn btn-primary btn-lg">👁 Visualizar Fornecedores</a>
        </div>
    </div>

</body>
</html>
