<?php
require_once __DIR__ . '/../Service/CupomService.php';

class CupomController {
    private $service;

    public function __construct($db) {
        $this->service = new CupomService($db);
    }

    public function index() {
        $cupons = $this->service->listarTodos();
        include __DIR__ . '/../../views/cupom/index.php';
    }

    public function criar() {
        $cupom = null; 
        include __DIR__ . '/../../views/cupom/form.php';
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'] ?? '';
            $desconto = floatval($_POST['desconto'] ?? 0);
            $validade = $_POST['validade'] ?? '';

            if ($this->service->salvar($codigo, $desconto, $validade)) {
                header('Location: /DEV-GABRIEL/cupom');
                exit();
            } else {
                echo "Erro ao salvar cupom.";
            }
        }
    }

    public function editar($id) {
        $cupom = $this->service->buscarPorId($id);
        if (!$cupom) {
            header('Location: /DEV-GABRIEL/cupom');
            exit();
        }
        include __DIR__ . '/../../views/cupom/form.php';
    }

    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $codigo = $_POST['codigo'] ?? '';
            $desconto = floatval($_POST['desconto'] ?? 0);
            $validade = $_POST['validade'] ?? '';

            if ($this->service->atualizar($id, $codigo, $desconto, $validade)) {
                header('Location: /DEV-GABRIEL/cupom');
                exit();
            } else {
                echo "Erro ao atualizar cupom.";
            }
        }
    }

    public function deletar($id) {
        if ($this->service->deletar($id)) {
            header('Location: /DEV-GABRIEL/cupom');
            exit();
        } else {
            echo "Erro ao deletar cupom.";
        }
    }
}
