<?php
$totalItens = 0;
foreach ($carrinho as $item) {
    $totalItens += $item['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="/DEV-GABRIEL/css/lista.css" />
</head>
<body>

<h1>Seu Carrinho</h1>

<?php if (empty($carrinho)): ?>
    <p>Seu carrinho está vazio.</p>
    <p><a href="/DEV-GABRIEL/produto">Voltar para produtos</a></p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>Produto</th>
            <th>Variação</th>
            <th>Quantidade</th>
            <th>Preço Unitário</th>
            <th>Subtotal</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($carrinho as $index => $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nome']) ?></td>
                <td><?= htmlspecialchars($item['variacao']) ?></td>
                <td>
                    <form action="/DEV-GABRIEL/carrinho/atualizar/<?= $index ?>" method="POST" style="display:inline;">
                        <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" style="width: 60px;" />
                        <button type="submit">Atualizar</button>
                    </form>
                </td>
                <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                <td>
                    <a href="/DEV-GABRIEL/carrinho/remover/<?= $index ?>" onclick="return confirm('Remover este item do carrinho?')">Remover</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><strong>Subtotal:</strong> R$ <?= number_format($subtotal, 2, ',', '.') ?></p>

<?php if (!empty($desconto)): ?>
    <p><strong>Desconto:</strong> - R$ <?= number_format($desconto, 2, ',', '.') ?></p>
<?php endif; ?>

<p><strong>Frete:</strong> R$ <?= number_format($frete, 2, ',', '.') ?></p>
<p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>


<?php if (!empty($cuponsDisponiveis)): ?>
    <form action="/DEV-GABRIEL/carrinho/aplicarCupom" method="POST" style="margin-top: 20px;">
        <label for="cupom_codigo"><strong>Aplicar Cupom:</strong></label>
        <select name="cupom_codigo" id="cupom_codigo" required>
            <option value="">Selecione um cupom</option>
            <?php foreach ($cuponsDisponiveis as $cupom): ?>
                <option value="<?= htmlspecialchars($cupom['codigo']) ?>">
                    <?= htmlspecialchars($cupom['codigo']) ?> - 
                    <?= number_format($cupom['desconto_percentual'], 0) ?>% (até <?= date('d/m/Y', strtotime($cupom['validade'])) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Aplicar</button>
    </form>
<?php else: ?>
    <p style="color: gray; margin-top: 10px;">Nenhum cupom disponível no momento.</p>
<?php endif; ?>


<?php if (isset($_SESSION['cupom'])): ?>
    <p style="color: green; margin-top: 10px;">
        Cupom <strong><?= htmlspecialchars($_SESSION['cupom']['codigo']) ?></strong> aplicado com sucesso!
    </p>
<?php endif; ?>


<form action="/DEV-GABRIEL/carrinho/finalizar" method="POST">
    <button type="submit">Finalizar Pedido</button>
</form>

<p><a href="/DEV-GABRIEL/produto">Continuar comprando</a></p>

<?php endif; ?>

</body>
</html>
