<?php

class Cupom {
    private $conn;
    private $table = 'cupons';

    private $id;
    private $codigo;
    private $desconto;
    private $validade;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getCodigo() {
        return $this->codigo;
    }
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function getDesconto() {
        return $this->desconto;
    }
    public function setDesconto($desconto) {
        $this->desconto = $desconto;
    }

    public function getValidade() {
        return $this->validade;
    }
    public function setValidade($validade) {
        $this->validade = $validade;
    }

    public function buscarPorCodigo($codigo) {
        $hoje = date('Y-m-d');
        $query = "SELECT * FROM " . $this->table . " WHERE codigo = :codigo AND validade >= :hoje LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':hoje', $hoje);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodos() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY validade DESC";
        return $this->conn->query($query);
    }

    public function buscarCuponsValidos() {
        $hoje = date('Y-m-d');
        $query = "SELECT * FROM " . $this->table . " WHERE validade >= :hoje ORDER BY validade ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hoje', $hoje);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar($codigo, $desconto, $validade) {
        $query = "INSERT INTO " . $this->table . " (codigo, desconto_percentual, validade) 
                  VALUES (:codigo, :desconto_percentual, :validade)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':desconto_percentual', $desconto);
        $stmt->bindParam(':validade', $validade);
        return $stmt->execute();
    }

    
    public function deletar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
