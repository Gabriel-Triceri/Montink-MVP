<?php
$cupom = $cupom ?? null;
$isEdit = !empty($cupom);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? "Editar Cupom" : "Criar Novo Cupom" ?></title>
    
</head>
<body>

<h1><?= $isEdit ? "Editar Cupom" : "Criar Novo Cupom" ?></h1>

<form action="<?= $isEdit ? '/DEV-GABRIEL/cupom/atualizar' : '/DEV-GABRIEL/cupom/salvar' ?>" method="POST">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($cupom['id']) ?>">
    <?php endif; ?>

    <label for="codigo">Código do Cupom:</label>
    <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($cupom['codigo'] ?? '') ?>" required><br><br>

    <label for="desconto">Desconto (% ou valor):</label>
    <input type="number" id="desconto" name="desconto" step="0.01" min="0" value="<?= htmlspecialchars($cupom['desconto'] ?? '') ?>" required><br><br>

    <label for="validade">Validade:</label>
    <input type="date" id="validade" name="validade" value="<?= htmlspecialchars($cupom['validade'] ?? '') ?>" required><br><br>

    <button type="submit"><?= $isEdit ? "Atualizar" : "Salvar" ?></button>
</form>

<p><a href="/DEV-GABRIEL/cupom">Voltar à lista</a></p>

</body>
</html>
