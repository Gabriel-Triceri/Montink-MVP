<?php
require_once __DIR__ . '/../Service/CarrinhoService.php';

class CarrinhoController {
    private $service;

    public function __construct($db) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->service = new CarrinhoService($db);

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
    }

    public function adicionar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /DEV-GABRIEL/produto');
            exit();
        }

        $produtoId  = (int)($_POST['produto_id'] ?? 0);
        $quantidade = max(1, (int)($_POST['quantidade'] ?? 1));
        $variacao   = $_POST['variacao'] ?? null;

        $erro = $this->service->adicionarAoCarrinho($_SESSION['carrinho'], $produtoId, $quantidade, $variacao);

        if ($erro) {
            $_SESSION['erro_carrinho'] = $erro;
            header('Location: /DEV-GABRIEL/produto');
            exit();
        }

        $_SESSION['sucesso_carrinho'] = "Produto adicionado ao carrinho.";
        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function index() {
        $carrinho = $_SESSION['carrinho'];

        $subtotal = $this->service->calcularSubtotal($carrinho);
        $frete = $this->service->calcularFrete($subtotal);
        $cuponsDisponiveis = $this->service->buscarCuponsValidos();

        $desconto = 0;
        $cupom = $_SESSION['cupom'] ?? null;
        if ($cupom) {
            $desconto = $this->service->calcularDesconto($cupom, $subtotal);
            unset($_SESSION['cupom']);
        }

        $total = $subtotal - $desconto + $frete;

        include __DIR__ . '/../../views/carrinho/index.php';
    }

    public function aplicarCupom() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['cupom_codigo'] ?? '';
            $cupom = $this->service->aplicarCupomPorCodigo($codigo);

            if ($cupom) {
                $_SESSION['cupom'] = $cupom;
                $_SESSION['sucesso_carrinho'] = "Cupom '{$cupom['codigo']}' aplicado!";
            } else {
                unset($_SESSION['cupom']);
                $_SESSION['erro_carrinho'] = "Cupom inválido ou expirado.";
            }
        }

        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function remover($index) {
        if ($this->service->removerItemDoCarrinho($_SESSION['carrinho'], $index)) {
            $_SESSION['sucesso_carrinho'] = "Item removido do carrinho.";
        }
        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function atualizar($index) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novaQtd = max(1, (int)($_POST['quantidade'] ?? 1));

            $erro = $this->service->atualizarQuantidade($_SESSION['carrinho'], $index, $novaQtd);

            if ($erro) {
                $_SESSION['erro_carrinho'] = $erro;
                header('Location: /DEV-GABRIEL/carrinho');
                exit();
            }

            $_SESSION['sucesso_carrinho'] = "Quantidade atualizada com sucesso.";
        }
        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function finalizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include __DIR__ . '/../../views/carrinho/finalizar.php';
            exit();
        }
        $carrinho = $_SESSION['carrinho'] ?? [];
        if (empty($carrinho)) {
            $_SESSION['erro_carrinho'] = "Carrinho vazio.";
            header('Location: /DEV-GABRIEL/carrinho');
            exit();
        }

        $emailCliente = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$emailCliente) {
            $_SESSION['erro_carrinho'] = "E-mail inválido.";
            header('Location: /DEV-GABRIEL/carrinho/finalizar');
            exit();
        }

        $subtotal = $this->service->calcularSubtotal($carrinho);
        $frete = $this->service->calcularFrete($subtotal);

        $desconto = 0;
        $cupomId = null;
        if (!empty($_SESSION['cupom'])) {
            $cupomId = $_SESSION['cupom']['id'];
            $desconto = $this->service->calcularDesconto($_SESSION['cupom'], $subtotal);
        }

        $pedidoId = $this->service->criarPedido($carrinho, $subtotal, $frete, $desconto, $cupomId);

        if ($pedidoId) {
         
            $this->service->diminuirEstoque($carrinho);

            $assunto = "Confirmação do Pedido #$pedidoId";
            $mensagem = "Olá,\n\nSeu pedido foi finalizado com sucesso!\n\nDetalhes do pedido:\n";
            foreach ($carrinho as $item) {
                $mensagem .= "- {$item['nome']} ({$item['variacao']}): {$item['quantidade']} x R$ " . number_format($item['preco'], 2, ',', '.') . "\n";
            }
            $mensagem .= "\nSubtotal: R$ " . number_format($subtotal, 2, ',', '.') . "\n";
            $mensagem .= "Frete: R$ " . number_format($frete, 2, ',', '.') . "\n";
            $mensagem .= "Desconto: R$ " . number_format($desconto, 2, ',', '.') . "\n";
            $mensagem .= "Total: R$ " . number_format($subtotal - $desconto + $frete, 2, ',', '.') . "\n\n";
            $mensagem .= "Obrigado por comprar conosco!\n";

            $headers = "From: loja@seudominio.com.br\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            mail($emailCliente, $assunto, $mensagem, $headers);

            unset($_SESSION['carrinho'], $_SESSION['cupom']);
            $_SESSION['sucesso_carrinho'] = "Pedido finalizado com sucesso! Um e-mail de confirmação foi enviado.";
            header("Location: /DEV-GABRIEL/carrinho/sucesso/{$pedidoId}");
            exit();
        } else {
            $_SESSION['erro_carrinho'] = "Erro ao finalizar pedido.";
            header('Location: /DEV-GABRIEL/carrinho');
            exit();
        }
    }

    public function sucesso($pedidoId = null) {
        if (!$pedidoId) {
            header('Location: /DEV-GABRIEL/produto');
            exit();
        }
        include __DIR__ . '/../../views/carrinho/sucesso.php';
    }
}
