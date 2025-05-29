<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo ?? 'Fornecedores'); ?></title>
    
        /* Estilos básicos (pode remover se já tiver no seu CSS principal) */
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 5px; text-decoration: none; }
        .actions a.edit { color: blue; }
        .actions a.delete { color: red; }
        .button-add {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1><?php echo htmlspecialchars($titulo ?? 'Lista de Fornecedores'); ?></h1>

    <a href="/fornecedores/novo" class="button-add">Adicionar Novo Fornecedor</a>

    <?php if (!empty($fornecedores)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Contato</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $fornecedor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fornecedor['id']); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor['nome']); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor['codigo'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor['contato'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($fornecedor['telefone'] ?? ''); ?></td>
                        <td class="actions">
                            <a href="/fornecedores/editar/<?php echo $fornecedor['id']; ?>" class="edit">Editar</a>
                            <a href="/fornecedores/excluir/<?php echo $fornecedor['id']; ?>" class="delete" onclick="return confirm('Tem certeza?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum fornecedor encontrado.</p>
    <?php endif; ?>

    <br>
    <a href="/faturas">Ver Faturas</a>

</body>
</html>