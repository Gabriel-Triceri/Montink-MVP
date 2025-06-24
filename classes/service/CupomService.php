<?php
require_once __DIR__ . '/../model/Cupom.php';

class CupomService {
    private $cupomModel;

    public function __construct($db) {
        $this->cupomModel = new Cupom($db);
    }

    public function listarTodos() {
        return $this->cupomModel->listarTodos()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        return $this->cupomModel->buscarPorId($id);
    }

    public function salvar($codigo, $desconto, $validade) {
        return $this->cupomModel->salvar($codigo, $desconto, $validade);
    }

    public function atualizar($id, $codigo, $desconto, $validade) {
        return $this->cupomModel->atualizar($id, $codigo, $desconto, $validade);
    }

    public function deletar($id) {
        return $this->cupomModel->deletar($id);
    }
}
