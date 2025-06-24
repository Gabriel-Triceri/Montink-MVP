<?php
$baseUrl = '/DEV-GABRIEL';

$isCreate = ($_GET['acao'] ?? '') === 'criar';
$isEdit = (($_GET['acao'] ?? '') === 'editar') && isset($produtoEdit);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Produtos</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/lista.css" />
</head>
<body>

<h1>Produtos Cadastrados</h1>

<p>
    <a href="<?= $baseUrl ?>/carrinho" style="padding: 8px 12px; background-color: #0188A7; color: white; text-decoration: none; border-radius: 4px;">
        Ver Carrinho
    </a>
</p>

<p><a href="<?= $baseUrl ?>/produto?acao=criar">Criar Novo Produto</a></p>

<?php if ($isCreate || $isEdit): ?>
    <h2><?= $isEdit ? "Editar Produto" : "Cadastrar Produto" ?></h2>
    <form action="<?= $baseUrl ?>/produto/<?= $isEdit ? 'atualizar' : 'salvar' ?>" method="POST">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($produtoEdit['id']) ?>" />
            <input type="hidden" name="id_estoque" value="<?= htmlspecialchars($produtoEdit['id_estoque'] ?? 0) ?>" />
        <?php endif; ?>

        <label for="nome">Nome:</label>
        <input
            type="text"
            id="nome"
            name="nome"
            value="<?= $isEdit ? htmlspecialchars($produtoEdit['nome']) : '' ?>"
            required
        />

        <label for="preco">Preço (R$):</label>
        <input
            type="number"
            id="preco"
            name="preco"
            step="0.01"
            min="0"
            value="<?= $isEdit ? htmlspecialchars($produtoEdit['preco']) : '' ?>"
            required
        />

        <label for="variacao">Variação:</label>
        <input
            type="text"
            id="variacao"
            name="variacao"
            value="<?= $isEdit ? htmlspecialchars($produtoEdit['variacao'] ?? '') : '' ?>"
            required
        />

        <label for="estoque">Estoque:</label>
        <input
            type="number"
            id="estoque"
            name="estoque"
            min="0"
            value="<?= $isEdit ? htmlspecialchars($produtoEdit['quantidade'] ?? 0) : 0 ?>"
            required
        />

        <button type="submit"><?= $isEdit ? "Atualizar" : "Cadastrar" ?></button>
    </form>

    <?php if ($isEdit): ?>
        <h3>Adicionar ao Carrinho</h3>
        <form action="<?= $baseUrl ?>/carrinho/adicionar" method="POST">
            <input type="hidden" name="produto_id" value="<?= htmlspecialchars($produtoEdit['id']) ?>" />

            <label for="carrinho_variacao">Variação:</label>
            <input
                type="text"
                id="carrinho_variacao"
                name="variacao"
                value="<?= htmlspecialchars($produtoEdit['variacao'] ?? 'Padrão') ?>"
                required
            />

            <label for="quantidade">Quantidade:</label>
            <input
                type="number"
                id="quantidade"
                name="quantidade"
                value="1"
                min="1"
                required
            />

            <button type="submit">Comprar</button>
        </form>
    <?php endif; ?>

    <p><a href="<?= $baseUrl ?>/produto">Cancelar</a></p>
<?php endif; ?>

<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço (R$)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($produtos)): ?>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= htmlspecialchars($produto['id']) ?></td>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td>
                        <a href="<?= $baseUrl ?>/produto?acao=editar&id=<?= $produto['id'] ?>">Editar</a> |
                        <a href="<?= $baseUrl ?>/produto/deletar/<?= $produto['id'] ?>" onclick="return confirm('Confirma exclusão?')">Excluir</a>

                        <hr/>

                        <form action="<?= $baseUrl ?>/carrinho/adicionar" method="POST" style="margin-top:10px;">
                            <input type="hidden" name="produto_id" value="<?= htmlspecialchars($produto['id']) ?>" />

                            <label for="variacao_<?= $produto['id'] ?>">Variação:</label>
                            <select name="variacao" id="variacao_<?= $produto['id'] ?>" required>
                                <?php
                                $variacoes = $estoquesPorProduto[$produto['id']] ?? [];

                                if (!empty($variacoes)) {
                                    foreach ($variacoes as $var) {
                                        echo '<option value="' . htmlspecialchars($var['variacao']) . '">' .
                                             htmlspecialchars($var['variacao']) .
                                             ' (Estoque: ' . intval($var['quantidade']) . ')</option>';
                                    }
                                } else {
                                    echo '<option value="Padrão">Padrão</option>';
                                }
                                ?>
                            </select>

                            <label for="quantidade_<?= $produto['id'] ?>">Qtd:</label>
                            <input
                                type="number"
                                id="quantidade_<?= $produto['id'] ?>"
                                name="quantidade"
                                value="1"
                                min="1"
                                required
                                style="width:50px;"
                            />

                            <button type="submit">Adicionar ao Carrinho</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">Nenhum produto cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
