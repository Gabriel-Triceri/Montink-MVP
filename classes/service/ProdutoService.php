<?php
require_once __DIR__ . '/../model/Produto.php';
require_once __DIR__ . '/../model/Estoque.php';

class ProdutoService {
    private $produtoModel;
    private $estoqueModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->produtoModel = new Produto($db);
        $this->estoqueModel = new Estoque($db);
    }

    public function listarTodos() {
        return $this->produtoModel->listar()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProdutosComEstoque() {
        $query = "
            SELECT p.*, COALESCE(SUM(e.quantidade), 0) AS estoque_total
            FROM produtos p
            LEFT JOIN estoques e ON e.produto_id = p.id
            GROUP BY p.id
            HAVING estoque_total > 0
            ORDER BY p.nome
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarEstoquesPorProduto($produtoId) {
        return $this->estoqueModel->listarPorProduto($produtoId);
    }

    public function buscarProdutoComEstoqueParaEdicao($id) {
        $produtos = $this->listarTodos();
        foreach ($produtos as $p) {
            if ($p['id'] == $id) {
                $estoques = $this->estoqueModel->listarPorProduto($id)->fetchAll(PDO::FETCH_ASSOC);

                if (count($estoques) > 0) {
                    $p['variacao'] = $estoques[0]['variacao'];
                    $p['quantidade'] = $estoques[0]['quantidade'];
                    $p['id_estoque'] = $estoques[0]['id'];
                } else {
                    $p['variacao'] = '';
                    $p['quantidade'] = 0;
                    $p['id_estoque'] = 0;
                }
                return $p;
            }
        }
        return null;
    }

    public function criarProdutoComEstoque($nome, $preco, $variacao, $quantidade) {
        $this->produtoModel->setNome($nome);
        $this->produtoModel->setPreco($preco);

        if ($this->produtoModel->criar()) {
            $idProduto = $this->db->lastInsertId();

            $this->estoqueModel->setProdutoId($idProduto);
            $this->estoqueModel->setVariacao($variacao);
            $this->estoqueModel->setQuantidade($quantidade);

            if (!$this->estoqueModel->criar()) {
                return "Erro ao criar estoque.";
            }
            return null; // sem erro
        } else {
            return "Erro ao criar produto.";
        }
    }

    public function atualizarProdutoComEstoque($id, $nome, $preco, $idEstoque, $variacao, $quantidade) {
        $this->produtoModel->setId($id);
        $this->produtoModel->setNome($nome);
        $this->produtoModel->setPreco($preco);

        if ($this->produtoModel->atualizar()) {
            if (!empty($idEstoque)) {
                $this->estoqueModel->setId($idEstoque);
                $this->estoqueModel->setVariacao($variacao);
                $this->estoqueModel->setQuantidade($quantidade);

                if (!$this->estoqueModel->atualizar()) {
                    return "Erro ao atualizar estoque.";
                }
            } else {
                $this->estoqueModel->setProdutoId($id);
                $this->estoqueModel->setVariacao($variacao);
                $this->estoqueModel->setQuantidade($quantidade);

                if (!$this->estoqueModel->criar()) {
                    return "Erro ao criar estoque.";
                }
            }
            return null;
        } else {
            return "Erro ao atualizar produto.";
        }
    }

    public function deletarProdutoComEstoque($id) {
        $this->estoqueModel->setProdutoId($id);
        $this->estoqueModel->deletarPorProduto();

        $this->produtoModel->setId($id);

        return $this->produtoModel->deletar();
    }
}
