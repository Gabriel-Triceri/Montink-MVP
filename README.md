# Sistema de Produtos e Carrinho - PHP MVC

Este é um sistema simples em PHP com padrão MVC para cadastro de produtos com variações, controle de estoque, carrinho de compras com controle de estoque e cálculo de frete, além de integração básica com a API ViaCEP para cálculo de frete baseado no CEP.

---

## Funcionalidades

- Cadastro, edição e exclusão de produtos com variações e controle de estoque.
- Listagem de produtos com seleção de variações e adição ao carrinho.
- Controle de estoque ao adicionar produtos ao carrinho.
- Visualização, atualização e remoção de itens no carrinho.
- Cálculo de frete básico com regras fixas e opção de cálculo via CEP usando API ViaCEP.
- Finalização de pedido (simples, com gravação no banco).

---

## Tecnologias

- PHP 7+
- MySQL / MariaDB
- PDO para acesso ao banco


## Como usar

### 1. Configurar o banco de dados
    Crie as tabelas do banco de dados que estão dentro da pasta BD


2. Configurar conexão com banco  
Edite o arquivo de configuração do banco (`config/database.php` ou similar) com suas credenciais.

3. Rodar o projeto localmente  
Use XAMPP, WAMP ou outro servidor local com PHP e MySQL.

Coloque a pasta `DEV-GABRIEL` dentro do diretório `htdocs`.

Acesse no navegador: [http://localhost/DEV-GABRIEL](http://localhost/DEV-GABRIEL)

---

## Uso do sistema

- **Produtos:** crie, edite e exclua produtos com variações e estoque.  
- **Carrinho:** selecione variações e quantidades para adicionar produtos ao carrinho.  
- **Frete:** informe o CEP no carrinho para cálculo via API ViaCEP.  
- **Finalizar:** conclua o pedido, que será salvo no banco.

---

## Integração ViaCEP

O sistema utiliza a API pública ViaCEP para buscar dados de endereço e validar CEP, ajudando no cálculo do frete.

---

## Sessões

O carrinho funciona usando sessões PHP para manter os dados do usuário enquanto navega.

---

## Melhorias Futuras

- Implementar autenticação de usuário.  
- Adicionar validações e sanitização completas.  
- Integração com gateways de pagamento.  
- Cálculo de frete mais avançado conforme regras comerciais.

---

## Contato

Gabriel Tricerri — Desenvolvedor Backend Júnior  
Email: seu-email@exemplo.com
