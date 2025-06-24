<?php
require_once __DIR__ . '/../model/Cupom.php';

class CupomController {
    private $cupomModel;

    public function __construct($db) {
        $this->cupomModel = new Cupom($db);
    }

    
    public function index() {
        $cupons = $this->cupomModel->listarTodos()->fetchAll(PDO::FETCH_ASSOC);
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

            
            if ($this->cupomModel->salvar($codigo, $desconto, $validade)) {
                header('Location: /DEV-GABRIEL/cupom');
                exit();
            } else {
                echo "Erro ao salvar cupom.";
            }
        }
    }

    
    public function editar($id) {
        $cupom = $this->cupomModel->buscarPorId($id);
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

            if ($this->cupomModel->atualizar($id, $codigo, $desconto, $validade)) {
                header('Location: /DEV-GABRIEL/cupom');
                exit();
            } else {
                echo "Erro ao atualizar cupom.";
            }
        }
    }

 
    public function deletar($id) {
        if ($this->cupomModel->deletar($id)) {
            header('Location: /DEV-GABRIEL/cupom');
            exit();
        } else {
            echo "Erro ao deletar cupom.";
        }
    }
}
