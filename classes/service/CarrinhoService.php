<?php
require_once __DIR__ . '/../model/Produto.php';
require_once __DIR__ . '/../model/Estoque.php';
require_once __DIR__ . '/../model/Pedido.php';
require_once __DIR__ . '/../model/Cupom.php';

class CarrinhoService {
    private $produtoModel;
    private $estoqueModel;
    private $pedidoModel;
    private $cupomModel;

    public function __construct($db) {
        $this->produtoModel = new Produto($db);
        $this->estoqueModel = new Estoque($db);
        $this->pedidoModel = new Pedido($db);
        $this->cupomModel = new Cupom($db);
    }

    public function validarEstoque($produtoId, $variacao) {
        $estoques = $this->estoqueModel->listarPorProduto($produtoId)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($estoques as $e) {
            if ($e['variacao'] === $variacao) {
                return [
                    'estoqueDisponivel' => (int)$e['quantidade'],
                    'estoqueId' => $e['id']
                ];
            }
        }
        return ['estoqueDisponivel' => 0, 'estoqueId' => null];
    }

    public function adicionarAoCarrinho(&$carrinho, $produtoId, $quantidade, $variacao) {
        $estoqueInfo = $this->validarEstoque($produtoId, $variacao);
        $estoqueDisponivel = $estoqueInfo['estoqueDisponivel'];
        $estoqueId = $estoqueInfo['estoqueId'];

        if ($quantidade > $estoqueDisponivel) {
            return "Quantidade solicitada maior que o estoque disponível.";
        }

        $itemKey = null;
        foreach ($carrinho as $k => $item) {
            if ($item['produto_id'] === $produtoId && $item['variacao'] === $variacao) {
                $itemKey = $k;
                break;
            }
        }

        if ($itemKey !== null) {
            $novaQtd = $carrinho[$itemKey]['quantidade'] + $quantidade;
            if ($novaQtd > $estoqueDisponivel) {
                return "Quantidade no carrinho ultrapassa estoque disponível.";
            }
            $carrinho[$itemKey]['quantidade'] = $novaQtd;
        } else {
            $produto = $this->produtoModel->buscarPorId($produtoId);
            if (!$produto) {
                return "Produto não encontrado.";
            }

            $carrinho[] = [
                'produto_id' => $produtoId,
                'nome'       => $produto['nome'],
                'preco'      => $produto['preco'],
                'variacao'   => $variacao,
                'quantidade' => $quantidade,
                'estoque_id' => $estoqueId,
            ];
        }

        return null; 
    }

    public function calcularSubtotal($carrinho) {
        return array_reduce($carrinho, function($sum, $item) {
            return $sum + ($item['preco'] * $item['quantidade']);
        }, 0.0);
    }

    public function calcularFrete($subtotal) {
        if ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15;
        } elseif ($subtotal > 200) {
            return 0;
        } else {
            return 20;
        }
    }

    public function buscarCuponsValidos() {
        return $this->cupomModel->buscarCuponsValidos();
    }

    public function calcularDesconto($cupom, $subtotal) {
        if (!$cupom) return 0;
        return ($cupom['desconto_percentual'] / 100) * $subtotal;
    }

    public function aplicarCupomPorCodigo($codigo) {
        return $this->cupomModel->buscarPorCodigo($codigo);
    }

    public function removerItemDoCarrinho(&$carrinho, $index) {
        if (isset($carrinho[$index])) {
            unset($carrinho[$index]);
            $carrinho = array_values($carrinho);
            return true;
        }
        return false;
    }

    public function atualizarQuantidade(&$carrinho, $index, $novaQtd) {
        if (!isset($carrinho[$index])) {
            return "Item inválido.";
        }

        $item = $carrinho[$index];
        $estoqueInfo = $this->validarEstoque($item['produto_id'], $item['variacao']);
        $estoqueDisponivel = $estoqueInfo['estoqueDisponivel'];

        if ($novaQtd > $estoqueDisponivel) {
            return "Quantidade solicitada ultrapassa estoque disponível.";
        }

        $carrinho[$index]['quantidade'] = $novaQtd;
        return null;
    }

    public function criarPedido($carrinho, $subtotal, $frete, $desconto, $cupomId) {
        return $this->pedidoModel->criarPedido($carrinho, $subtotal, $frete, $desconto, $cupomId);
    }

    public function diminuirEstoque($carrinho) {
        foreach ($carrinho as $item) {
            $this->estoqueModel->diminuirQuantidade($item['estoque_id'], $item['quantidade']);
        }
    }
}
