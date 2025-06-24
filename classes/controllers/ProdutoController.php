<?php
require_once __DIR__ . '/../model/Produto.php';
require_once __DIR__ . '/../model/Estoque.php';

class ProdutoController {
    private $produtoModel;
    private $estoqueModel;
    private $db; 

    public function __construct($db) {
        $this->db = $db;
        $this->produtoModel = new Produto($db);
        $this->estoqueModel = new Estoque($db);
    }

    public function index() {
        global $db;
        $produtos = $this->produtoModel->listar()->fetchAll(PDO::FETCH_ASSOC);

        $acao = $_GET['acao'] ?? '';
        $id = $_GET['id'] ?? null;

        $produtoEdit = null;
        if ($acao === 'editar' && $id) {
            foreach ($produtos as $p) {
                if ($p['id'] == $id) {
                    $produtoEdit = $p;

        
                    $estoques = $this->estoqueModel->listarPorProduto($id)->fetchAll(PDO::FETCH_ASSOC);

                    if (count($estoques) > 0) {
                        $produtoEdit['variacao'] = $estoques[0]['variacao'];
                        $produtoEdit['quantidade'] = $estoques[0]['quantidade'];
                        $produtoEdit['id_estoque'] = $estoques[0]['id'];
                    } else {
                        $produtoEdit['variacao'] = '';
                        $produtoEdit['quantidade'] = 0;
                        $produtoEdit['id_estoque'] = 0;
                    }
                    break;
                }
            }
        }

        include __DIR__ . '/../../views/produto/lista.php';
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->produtoModel->setNome($_POST['nome'] ?? '');
            $this->produtoModel->setPreco($_POST['preco'] ?? 0);

            if ($this->produtoModel->criar()) {
                $idProduto = $this->db->lastInsertId();

                $this->estoqueModel->setProdutoId($idProduto);
                $this->estoqueModel->setVariacao($_POST['variacao'] ?? 'Padrão');
                $this->estoqueModel->setQuantidade($_POST['estoque'] ?? 0);

                if (!$this->estoqueModel->criar()) {
                    echo "Erro ao criar estoque.";
                    return;
                }

                header('Location: /DEV-GABRIEL/produto');
                exit();
            } else {
                echo "Erro ao criar produto.";
            }
        }
    }

    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->produtoModel->setId($_POST['id'] ?? 0);
            $this->produtoModel->setNome($_POST['nome'] ?? '');
            $this->produtoModel->setPreco($_POST['preco'] ?? 0);

            if ($this->produtoModel->atualizar()) {
                if (!empty($_POST['id_estoque'])) {
                    $this->estoqueModel->setId($_POST['id_estoque']);
                    $this->estoqueModel->setVariacao($_POST['variacao'] ?? 'Padrão');
                    $this->estoqueModel->setQuantidade($_POST['estoque'] ?? 0);

                    if (!$this->estoqueModel->atualizar()) {
                        echo "Erro ao atualizar estoque.";
                        return;
                    }
                } else {
                    $this->estoqueModel->setProdutoId($_POST['id']);
                    $this->estoqueModel->setVariacao($_POST['variacao'] ?? 'Padrão');
                    $this->estoqueModel->setQuantidade($_POST['estoque'] ?? 0);

                    if (!$this->estoqueModel->criar()) {
                        echo "Erro ao criar estoque.";
                        return;
                    }
                }

                header('Location: /DEV-GABRIEL/produto');
                exit();
            } else {
                echo "Erro ao atualizar produto.";
            }
        }
    }

    public function deletar($id) {
        $this->estoqueModel->setProdutoId($id);
        $this->estoqueModel->deletarPorProduto();

       
        $this->produtoModel->setId($id);

        if ($this->produtoModel->deletar()) {
            header('Location: /DEV-GABRIEL/produto');
            exit();
        } else {
            echo "Erro ao deletar produto.";
        }
    }
}
