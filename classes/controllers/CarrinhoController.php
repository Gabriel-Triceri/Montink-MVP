<?php
require_once __DIR__ . '/../model/Produto.php';
require_once __DIR__ . '/../model/Estoque.php';
require_once __DIR__ . '/../model/Pedido.php';
require_once __DIR__ . '/../model/Cupom.php';

class CarrinhoController {
    private $produtoModel;
    private $estoqueModel;
    private $pedidoModel;
    private $cupomModel;
    private $db;

    public function __construct($db) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->db = $db;
        $this->produtoModel = new Produto($db);
        $this->estoqueModel = new Estoque($db);
        $this->pedidoModel = new Pedido($db);
        $this->cupomModel = new Cupom($db);

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

        $estoques = $this->estoqueModel->listarPorProduto($produtoId)->fetchAll(PDO::FETCH_ASSOC);

        $estoqueDisponivel = 0;
        $estoqueId = null;
        foreach ($estoques as $e) {
            if ($e['variacao'] === $variacao) {
                $estoqueDisponivel = (int)$e['quantidade'];
                $estoqueId = $e['id'];
                break;
            }
        }

        if ($quantidade > $estoqueDisponivel) {
            $_SESSION['erro_carrinho'] = "Quantidade solicitada maior que o estoque disponível.";
            header('Location: /DEV-GABRIEL/produto');
            exit();
        }

        $itemKey = null;
        foreach ($_SESSION['carrinho'] as $k => $i) {
            if ($i['produto_id'] === $produtoId && $i['variacao'] === $variacao) {
                $itemKey = $k;
                break;
            }
        }

        if ($itemKey !== null) {
            $novaQtd = $_SESSION['carrinho'][$itemKey]['quantidade'] + $quantidade;
            if ($novaQtd > $estoqueDisponivel) {
                $_SESSION['erro_carrinho'] = "Quantidade no carrinho ultrapassa estoque disponível.";
                header('Location: /DEV-GABRIEL/produto');
                exit();
            }
            $_SESSION['carrinho'][$itemKey]['quantidade'] = $novaQtd;
        } else {
            $produto = $this->produtoModel->buscarPorId($produtoId);
            if (!$produto) {
                $_SESSION['erro_carrinho'] = "Produto não encontrado.";
                header('Location: /DEV-GABRIEL/produto');
                exit();
            }

            $_SESSION['carrinho'][] = [
                'produto_id' => $produtoId,
                'nome'       => $produto['nome'],
                'preco'      => $produto['preco'],
                'variacao'   => $variacao,
                'quantidade' => $quantidade,
                'estoque_id' => $estoqueId,
            ];
        }

        $_SESSION['sucesso_carrinho'] = "Produto adicionado ao carrinho.";
        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function index() {
        $carrinho = $_SESSION['carrinho'] ?? [];

        $subtotal = array_reduce($carrinho, function($sum, $item) {
            return $sum + ($item['preco'] * $item['quantidade']);
        }, 0.0);

        if ($subtotal >= 52 && $subtotal <= 166.59) {
            $frete = 15;
        } elseif ($subtotal > 200) {
            $frete = 0;
        } else {
            $frete = 20;
        }

        $cuponsDisponiveis = $this->cupomModel->buscarCuponsValidos();

        $desconto = 0;
        $cupom = $_SESSION['cupom'] ?? null;
        if ($cupom) {
            $desconto = ($cupom['desconto_percentual'] / 100) * $subtotal;
            unset($_SESSION['cupom']);
        }

        $total = $subtotal - $desconto + $frete;

        include __DIR__ . '/../../views/carrinho/index.php';
    }

    public function aplicarCupom() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['cupom_codigo'] ?? '';
            $cupom = $this->cupomModel->buscarPorCodigo($codigo);

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
        if (isset($_SESSION['carrinho'][$index])) {
            unset($_SESSION['carrinho'][$index]);
            $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
            $_SESSION['sucesso_carrinho'] = "Item removido do carrinho.";
        }

        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function atualizar($index) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novaQtd = max(1, (int)($_POST['quantidade'] ?? 1));
            if (!isset($_SESSION['carrinho'][$index])) {
                $_SESSION['erro_carrinho'] = "Item inválido.";
                header('Location: /DEV-GABRIEL/carrinho');
                exit();
            }

            $item = $_SESSION['carrinho'][$index];
            $estoques = $this->estoqueModel->listarPorProduto($item['produto_id'])->fetchAll(PDO::FETCH_ASSOC);

            $estoqueDisponivel = 0;
            foreach ($estoques as $e) {
                if ($e['variacao'] === $item['variacao']) {
                    $estoqueDisponivel = (int)$e['quantidade'];
                    break;
                }
            }

            if ($novaQtd > $estoqueDisponivel) {
                $_SESSION['erro_carrinho'] = "Quantidade solicitada ultrapassa estoque disponível.";
                header('Location: /DEV-GABRIEL/carrinho');
                exit();
            }

            $_SESSION['carrinho'][$index]['quantidade'] = $novaQtd;
            $_SESSION['sucesso_carrinho'] = "Quantidade atualizada com sucesso.";
        }

        header('Location: /DEV-GABRIEL/carrinho');
        exit();
    }

    public function finalizar() {
        $carrinho = $_SESSION['carrinho'] ?? [];
        if (empty($carrinho)) {
            $_SESSION['erro_carrinho'] = "Carrinho vazio.";
            header('Location: /DEV-GABRIEL/carrinho');
            exit();
        }

        $emailCliente = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$emailCliente) {
            $_SESSION['erro_carrinho'] = "E-mail inválido.";
            header('Location: /DEV-GABRIEL/carrinho');
            exit();
        }

        $subtotal = array_reduce($carrinho, fn($sum, $i) => $sum + ($i['preco'] * $i['quantidade']), 0.0);

        if ($subtotal >= 52 && $subtotal <= 166.59) {
            $frete = 15;
        } elseif ($subtotal > 200) {
            $frete = 0;
        } else {
            $frete = 20;
        }

        $desconto = 0;
        $cupomId = null;
        if (!empty($_SESSION['cupom'])) {
            $cupomId = $_SESSION['cupom']['id'];
            $desconto = ($_SESSION['cupom']['desconto_percentual'] / 100) * $subtotal;
        }

        $pedidoId = $this->pedidoModel->criarPedido($carrinho, $subtotal, $frete, $desconto, $cupomId);

        if ($pedidoId) {
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
