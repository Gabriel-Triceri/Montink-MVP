<?php

class Produto {
    private $conn;
    private $table = 'produtos';

    private $id;
    private $nome;
    private $preco;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getNome() {
        return $this->nome;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }
    public function getPreco() {
        return $this->preco;
    }
    public function setPreco($preco) {
        $this->preco = $preco;
    }



    public function criar() {
        $query = "INSERT INTO " . $this->table . " (nome, preco) VALUES (:nome, :preco)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':preco', $this->preco);

        return $stmt->execute();
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table . " SET nome = :nome, preco = :preco WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':preco', $this->preco);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function deletar() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
