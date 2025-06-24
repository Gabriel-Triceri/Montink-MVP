# Documentação Completa do Sistema PHP MVC

## Sumário
- [Visão Geral](#visão-geral)
- [Classes e Modelos](#classes-e-modelos)
  - [Estoque](#estoque)
  - [Pedido](#pedido)
  - [Produto](#produto)
  - [Cupom](#cupom)
- [Services](#services)
  - [CarrinhoService](#carrinhoservice)
  - [CupomService](#cupomservice)
  - [ProdutoService](#produtoservice)
- [Views (Templates HTML)](#views-templates-html)
- [Front ](#front)
- [Rotas e Controladores](#rotas-e-controladores)
- [Observações](#observações)

---

## Visão Geral
Este projeto é um sistema básico de e-commerce desenvolvido em PHP com arquitetura MVC, contendo funcionalidades para gerenciamento de produtos, estoque, pedidos, cupons de desconto e carrinho de compras.

O código está organizado em Models para acesso ao banco, Services para a lógica de negócio, e Views para a interface HTML.

## Como rodar a aplicação
Baixar o XAMPP e dar start no APache e no Mysql

Colocar o projeto dentro de:
C:\xampp\htdocs

Inserir as tabelas no banco de dados que esta na pasta BD

E testar as rotas atraves do chome

## Rotas e Endpoints

### 📦 Produtos

| Método | Rota                            | Ação                                 | Descrição                                 |
|--------|----------------------------------|--------------------------------------|-------------------------------------------|
| GET    | `/produto`                      | `ProdutoController@index`           | Lista todos os produtos cadastrados       |
| GET    | `/produto?acao=criar`           | `ProdutoController@formCreate`      | Exibe formulário de criação de produto    |
| GET    | `/produto?acao=editar&id={id}`  | `ProdutoController@formEdit`        | Exibe formulário de edição de produto     |
| POST   | `/produto/salvar`               | `ProdutoController@store`           | Salva novo produto com estoque            |
| POST   | `/produto/atualizar`            | `ProdutoController@update`          | Atualiza produto e estoque                |
| GET    | `/produto/deletar/{id}`         | `ProdutoController@delete`          | Deleta produto e seu estoque              |

---

### 🛒 Carrinho

| Método | Rota                               | Ação                                      | Descrição                                      |
|--------|-------------------------------------|-------------------------------------------|------------------------------------------------|
| GET    | `/carrinho`                        | `CarrinhoController@index`               | Exibe itens do carrinho                        |
| POST   | `/carrinho/adicionar`              | `CarrinhoController@add`                 | Adiciona item ao carrinho                      |
| POST   | `/carrinho/atualizar/{index}`      | `CarrinhoController@updateQuantidade`    | Atualiza quantidade de item no carrinho       |
| GET    | `/carrinho/remover/{index}`        | `CarrinhoController@remove`              | Remove item do carrinho                        |
| POST   | `/carrinho/aplicarCupom`           | `CarrinhoController@aplicarCupom`        | Aplica cupom ao carrinho                       |
| POST   | `/carrinho/finalizar`              | `CarrinhoController@finalizarPedido`     | Finaliza pedido e envia email de confirmação  |

---

### 🎟️ Cupons

| Método | Rota                              | Ação                             | Descrição                               |
|--------|------------------------------------|----------------------------------|-------------------------------------------|
| GET    | `/cupom`                          | `CupomController@index`         | Lista todos os cupons cadastrados         |
| GET    | `/cupom/criar`                    | `CupomController@formCreate`    | Exibe formulário de criação de cupom      |
| GET    | `/cupom/editar?id={id}`           | `CupomController@formEdit`      | Exibe formulário de edição de cupom       |
| POST   | `/cupom/salvar`                   | `CupomController@store`         | Salva um novo cupom                        |
| POST   | `/cupom/atualizar`                | `CupomController@update`        | Atualiza um cupom existente                |
| GET    | `/cupom/deletar/{id}`             | `CupomController@delete`        | Exclui o cupom pelo ID                     |

---

### 📧 Confirmação de Pedido

| Método | Rota                        | Ação                            | Descrição                                 |
|--------|------------------------------|---------------------------------|---------------------------------------------|
| GET    | `/pedido/finalizado`       | `PedidoController@confirmacao` | Tela de agradecimento após finalizar pedido |

---

> Obs: as rotas com `{id}` ou `{index}` representam valores dinâmicos enviados pela URL (por exemplo: `/produto/deletar/3` ou `/carrinho/remover/0`).


## Classes e Modelos

### Estoque
Classe que representa o estoque dos produtos com variações.
- Propriedades: `id`, `produto_id`, `variacao`, `quantidade`
- Métodos:
  - CRUD básico (`criar()`, `atualizar()`, `deletar()`, `deletarPorProduto()`)
  - Listagem por produto: `listarPorProduto($produto_id)`
  - Diminuir quantidade no estoque: `diminuirQuantidade($estoqueId, $quantidade)`

### Pedido
Classe para gerenciar pedidos realizados.
- Propriedades: `id`, `data_pedido`, `total`, `cupom_id`
- Método principal:
  - `criarPedido($itens, $subtotal, $frete)` - cria pedido e insere itens, atualiza estoque dentro de transação para garantir atomicidade.

### Produto
Gerencia os produtos do sistema.
- Propriedades: `id`, `nome`, `preco`
- Métodos:
  - CRUD básico (`criar()`, `listar()`, `atualizar()`, `deletar()`)
  - Buscar por ID: `buscarPorId($id)`

### Cupom
Classe para gerenciar cupons de desconto (não foi enviado código completo, mas possui métodos para listar, salvar, atualizar e deletar).

## Services

### CarrinhoService
Responsável pela lógica do carrinho de compras.
- Validação de estoque ao adicionar produto
- Adicionar, atualizar e remover itens do carrinho
- Cálculo de subtotal, frete e desconto
- Aplicação de cupons
- Criação de pedido chamando o model Pedido
- Atualização do estoque via Estoque model

### CupomService
Camada de serviço para interagir com o model Cupom.
- Métodos para listar, buscar por ID, salvar, atualizar e deletar cupons

### ProdutoService
Serve para gerenciar produtos e seus estoques juntos.
- Listar produtos
- Listar produtos com estoque maior que zero
- Buscar produto com estoque para edição
- Criar produto com estoque
- Atualizar produto com estoque
- Deletar produto e seu estoque

## Views (Templates HTML)

- Tela de finalizar compra (formulário para informar email e finalizar pedido)
- Listagem do carrinho com opções para atualizar quantidade, remover itens, aplicar cupom e finalizar pedido
- Tela de pedido finalizado (confirmação)
- Formulários para criar e editar cupons
- Lista de cupons cadastrados
- Formulário para criar/editar produtos
- Listagem de produtos com opções para editar, excluir e adicionar ao carrinho

## Front 

Arquivo `index.php` principal, que:
- Configura conexão com banco
- Recebe a URL da requisição
- Passa para o Router responsável por direcionar para os controllers adequados

**Fim da documentação**
