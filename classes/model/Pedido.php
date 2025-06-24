<?php

class Pedido {
    private $conn;
    private $table = 'pedidos';

    private $id;
    private $data_pedido;
    private $total;
    private $cupom_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getDataPedido() {
        return $this->data_pedido;
    }
    public function setDataPedido($data) {
        $this->data_pedido = $data;
    }

    public function getTotal() {
        return $this->total;
    }
    public function setTotal($total) {
        $this->total = $total;
    }

    public function getCupomId() {
        return $this->cupom_id;
    }
    public function setCupomId($cupom_id) {
        $this->cupom_id = $cupom_id;
    }

    public function criarPedido($itens, $subtotal, $frete) {
        try {
            $this->conn->beginTransaction();

            $queryPedido = "INSERT INTO " . $this->table . " (data_pedido, total, cupom_id) 
                            VALUES (NOW(), :total, NULL)";
            $stmt = $this->conn->prepare($queryPedido);
            $total = $subtotal + $frete;
            $stmt->bindParam(':total', $total);
            $stmt->execute();

            $pedidoId = $this->conn->lastInsertId();

            $queryItem = "INSERT INTO pedido_itens 
                          (pedido_id, produto_id, variacao, quantidade, preco_unitario) 
                          VALUES (:pedido_id, :produto_id, :variacao, :quantidade, :preco_unitario)";
            $stmtItem = $this->conn->prepare($queryItem);

            foreach ($itens as $item) {
                $stmtItem->bindParam(':pedido_id', $pedidoId);
                $stmtItem->bindParam(':produto_id', $item['produto_id']);
                $stmtItem->bindParam(':variacao', $item['variacao']);
                $stmtItem->bindParam(':quantidade', $item['quantidade']);
                $stmtItem->bindParam(':preco_unitario', $item['preco']);
                $stmtItem->execute();

                $queryEstoque = "UPDATE estoques 
                                 SET quantidade = quantidade - :qtd 
                                 WHERE id = :estoque_id";
                $stmtEstoque = $this->conn->prepare($queryEstoque);
                $stmtEstoque->bindParam(':qtd', $item['quantidade']);
                $stmtEstoque->bindParam(':estoque_id', $item['estoque_id']);
                $stmtEstoque->execute();
            }

            $this->conn->commit();
            return $pedidoId;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro ao criar pedido: " . $e->getMessage());
            return false;
        }
    }
}
