<?php
$baseUrl = '/DEV-GABRIEL';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Cupons</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/cupom.css">
</head>
<body>

<h1>Cupons Cadastrados</h1>

<p><a href="<?= $baseUrl ?>/cupom/criar">Criar Novo Cupom</a></p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Desconto (%)</th>
            <th>Validade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($cupons)): ?>
            <?php foreach ($cupons as $cupom): ?>
                <tr>
                    <td><?= htmlspecialchars($cupom['id']) ?></td>
                    <td><?= htmlspecialchars($cupom['codigo']) ?></td>
                    <td><?= htmlspecialchars($cupom['desconto_percentual']) ?>%</td>
                    <td><?= htmlspecialchars($cupom['validade']) ?></td>
                    <td>
                        <a href="<?= $baseUrl ?>/cupom/editar?id=<?= $cupom['id'] ?>">Editar</a> |
                        <a href="<?= $baseUrl ?>/cupom/deletar/<?= $cupom['id'] ?>" onclick="return confirm('Deseja excluir este cupom?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Nenhum cupom cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
