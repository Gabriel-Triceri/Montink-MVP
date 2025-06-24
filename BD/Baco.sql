CREATE TABLE     (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL
);

CREATE TABLE estoques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao VARCHAR(100), 
    quantidade INT DEFAULT 0,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);


CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    cupom_id INT,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id)
);

CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    desconto_percentual DECIMAL(5, 2), 
    validade DATE
);
