<?php

class Estoque {
    private $conn;
    private $table = 'estoques';

    private $id;
    private $produto_id;
    private $variacao;
    private $quantidade;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getProdutoId() {
        return $this->produto_id;
    }
    public function setProdutoId($produto_id) {
        $this->produto_id = $produto_id;
    }
    public function getVariacao() {
        return $this->variacao;
    }
    public function setVariacao($variacao) {
        $this->variacao = $variacao;
    }
    public function getQuantidade() {
        return $this->quantidade;
    }
    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table . " (produto_id, variacao, quantidade) VALUES (:produto_id, :variacao, :quantidade)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':produto_id', $this->produto_id);
        $stmt->bindParam(':variacao', $this->variacao);
        $stmt->bindParam(':quantidade', $this->quantidade);

        return $stmt->execute();
    }

    public function listarPorProduto($produto_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE produto_id = :produto_id ORDER BY variacao";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->execute();
        return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table . " SET variacao = :variacao, quantidade = :quantidade WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':variacao', $this->variacao);
        $stmt->bindParam(':quantidade', $this->quantidade);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function deletar() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
    
    public function deletarPorProduto() {
        $query = "DELETE FROM estoques WHERE produto_id = :produto_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produto_id', $this->produto_id);
        return $stmt->execute();
    }


    public function diminuirQuantidade($estoqueId, $quantidade) {
        $query = "UPDATE " . $this->table . " SET quantidade = quantidade - :quantidade WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindParam(':id', $estoqueId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
