<?php
class Conexao {
    private $host = "localhost";
    private $db_name = "gabriel-dev";
    private $username = "root";
    private $password = "";
    private $conn;

    public function conectar() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port=3307;dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }
        return $this->conn;
    }
}
