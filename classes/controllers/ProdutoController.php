<?php
require_once __DIR__ . '/../Service/ProdutoService.php';

class ProdutoController {
    private $service;

    public function __construct($db) {
        $this->service = new ProdutoService($db);
    }

    public function index() {
        $produtos = $this->service->listarTodos();

        // Buscar variações e estoques para cada produto
        $estoquesPorProduto = [];
        foreach ($produtos as $produto) {
            $stmt = $this->service->listarEstoquesPorProduto($produto['id']);
            $estoquesPorProduto[$produto['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $acao = $_GET['acao'] ?? '';
        $id = $_GET['id'] ?? null;

        $produtoEdit = null;
        if ($acao === 'editar' && $id) {
            $produtoEdit = $this->service->buscarProdutoComEstoqueParaEdicao($id);
        }

        include __DIR__ . '/../../views/produto/lista.php';
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $erro = $this->service->criarProdutoComEstoque(
                $_POST['nome'] ?? '',
                $_POST['preco'] ?? 0,
                $_POST['variacao'] ?? 'Padrão',
                $_POST['estoque'] ?? 0
            );

            if ($erro) {
                echo $erro;
                return;
            }
            header('Location: /DEV-GABRIEL/produto');
            exit();
        }
    }

    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $erro = $this->service->atualizarProdutoComEstoque(
                $_POST['id'] ?? 0,
                $_POST['nome'] ?? '',
                $_POST['preco'] ?? 0,
                $_POST['id_estoque'] ?? null,
                $_POST['variacao'] ?? 'Padrão',
                $_POST['estoque'] ?? 0
            );

            if ($erro) {
                echo $erro;
                return;
            }
            header('Location: /DEV-GABRIEL/produto');
            exit();
        }
    }

    public function deletar($id) {
        if ($this->service->deletarProdutoComEstoque($id)) {
            header('Location: /DEV-GABRIEL/produto');
            exit();
        } else {
            echo "Erro ao deletar produto.";
        }
    }
}
