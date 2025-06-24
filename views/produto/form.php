<?php
$produto = $produto ?? null;
$isEdit = !empty($produto) && !empty($produto['id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title><?= $isEdit ? "Editar Produto" : "Cadastrar Produto" ?></title>
</head>
<body>

<div class="container">
    <h1><?= $isEdit ? "Editar Produto" : "Cadastrar Produto" ?></h1>

    <form action="<?= $isEdit ? "/DEV-GABRIEL/produto/atualizar" : "/DEV-GABRIEL/produto/salvar" ?>" method="POST">

        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($produto['id']) ?>" />
        <?php endif; ?>

        <label for="nome">Nome:</label>
        <input
            type="text"
            id="nome"
            name="nome"
            value="<?= $isEdit ? htmlspecialchars($produto['nome']) : '' ?>"
            required
        />

        <label for="preco">Preço (R$):</label>
        <input
            type="number"
            id="preco"
            name="preco"
            value="<?= $isEdit ? htmlspecialchars($produto['preco']) : '' ?>"
            step="0.01"
            min="0"
            required
        />

        <button type="submit"><?= $isEdit ? "Atualizar" : "Cadastrar" ?></button>
    </form>

    <p><a href="/DEV-GABRIEL/produto">Voltar para lista</a></p>
</div>

</body>
</html>
