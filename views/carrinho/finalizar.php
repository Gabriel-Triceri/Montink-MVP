<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="/Dev-Gabriel/css/finalizar.css">
</head>
<body>

<h2>Finalizar Compra</h2>

<?php if (!empty($_SESSION['erro_carrinho'])): ?>
    <p style="color:red;"><?php echo $_SESSION['erro_carrinho']; unset($_SESSION['erro_carrinho']); ?></p>
<?php endif; ?>

<form method="POST" action="/DEV-GABRIEL/carrinho/finalizar">
    <label for="email">Informe seu e-mail para receber a confirmação:</label><br>
    <input type="email" name="email" id="email" required><br><br>
    <button type="submit">Finalizar Pedido</button>
</form>

</body>
</html>
