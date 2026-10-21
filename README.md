# Loja POO

Projeto da atividade prática de PHP com Programação Orientada a Objetos. É uma loja virtual onde dá para montar um pedido, pagar e ver o resumo no final.

## Objetivo

Praticar POO em PHP usando encapsulamento, herança, abstração, polimorfismo, interfaces, composição, tratamento de exceções, namespaces e autoload.

## Descrição

O sistema controla produtos (físicos e digitais), clientes com endereço, pedidos com itens e três formas de pagamento (PIX, cartão e boleto). As regras de processar o pedido ficam no `PedidoService`, e o `index.php` só monta os objetos e chama os métodos.

O frete de produto físico é calculado por unidade: R$ 10,00 fixo mais R$ 8,00 por kg. Produto digital não tem frete.

## Estrutura das pastas

```
loja-poo/
├── src/
│   ├── Models/
│   │   ├── Produto.php
│   │   ├── ProdutoFisico.php
│   │   ├── ProdutoDigital.php
│   │   ├── Cliente.php
│   │   ├── Endereco.php
│   │   ├── Pedido.php
│   │   └── ItemPedido.php
│   ├── Interfaces/
│   │   └── PagamentoInterface.php
│   ├── Pagamentos/
│   │   ├── PagamentoPix.php
│   │   ├── PagamentoCartao.php
│   │   └── PagamentoBoleto.php
│   ├── Services/
│   │   └── PedidoService.php
│   └── Exceptions/
│       ├── EstoqueInsuficienteException.php
│       ├── PedidoInvalidoException.php
│       └── PagamentoException.php
├── autoload.php
├── index.php
└── README.md
```

Além da estrutura pedida, criei o `autoload.php` para carregar as classes e o `diagrama.puml` com o diagrama de classes.

## Classes implementadas

- `Produto` (abstrata), `ProdutoFisico` e `ProdutoDigital`
- `Cliente` e `Endereco`
- `Pedido` e `ItemPedido`
- `PagamentoInterface`, `PagamentoPix`, `PagamentoCartao` e `PagamentoBoleto`
- `PedidoService`
- `EstoqueInsuficienteException`, `PedidoInvalidoException` e `PagamentoException`

## Conceitos de POO utilizados

**Encapsulamento:** todos os atributos são `private` e só são acessados por getters. O preço e o estoque do `Produto` só mudam pelos métodos `alterarPreco()`, `adicionarEstoque()` e `removerEstoque()`, que validam o valor antes. O CPF do `Cliente` não tem setter, então não muda depois que o objeto é criado. O subtotal do `ItemPedido` é calculado no `getSubtotal()` e não existe jeito de informar ele na mão. O status do `Pedido` só muda pelo `alterarStatus()`, que confere se a mudança é permitida.

**Herança:** `ProdutoFisico` e `ProdutoDigital` herdam de `Produto`. As três exceções personalizadas herdam de `Exception`.

**Abstração:** `Produto` é abstrata, então não dá para fazer `new Produto()`. Ela guarda o que todo produto tem (código, nome, preço e estoque) e obriga as classes filhas a implementarem `calcularFrete()` e `calcularPrecoFinal()`, cada uma do seu jeito.

**Polimorfismo:** o `Pedido` e o `PedidoService` recebem um `PagamentoInterface`, sem saber se é PIX, cartão ou boleto. Cada forma de pagamento implementa o `pagar()` com um comportamento próprio. O mesmo vale para os produtos: o pedido chama `calcularFrete()` sem saber se o produto é físico ou digital.

**Composição:**
- `Cliente` → `Endereco`: o cliente guarda uma lista de endereços e não pode ficar sem nenhum.
- `Pedido` → `ItemPedido`: os itens são criados dentro do pedido e fazem parte dele.
- `ItemPedido` → `Produto`: o item guarda o produto que está sendo comprado. No diagrama usei o losango vazio (agregação) nessa relação, porque o produto continua existindo mesmo sem o item.

**Interface:** a `PagamentoInterface` é o contrato das formas de pagamento: toda classe que implementa ela precisa ter `pagar()` e `getNome()`. É isso que deixa trocar a forma de pagamento sem mexer no `PedidoService`.

## Regras de negócio

1. Produto não pode ter preço negativo (`Produto`)
2. Estoque não pode ser negativo (`Produto`)
3. Item precisa ter quantidade maior que zero (`Pedido` e `ItemPedido`)
4. Pedido precisa ter pelo menos um item antes de ser processado (`PedidoService`)
5. Pedido cancelado não pode ser pago (`PedidoService`)
6. Pedido concluído não recebe novos itens (`Pedido`)
7. O pagamento acontece antes da conclusão do pedido (`PedidoService`)
8. O estoque só é atualizado depois do pagamento confirmado (`PedidoService`)
9. Não vende mais do que tem em estoque (`Pedido` e `PedidoService`)
10. Produto digital não tem frete (`ProdutoDigital`)

## Como executar

Precisa do PHP instalado. Dentro da pasta `loja-poo`:

```
php index.php
```

Para salvar o resultado da execução em um arquivo:

```
php index.php > evidencias.txt
```

## Testes realizados

Resultado dos testes obrigatórios e dos extras:

```
Teste 1: comprar 8 Tiguer 7
Erro: Estoque insuficiente para o produto Tiguer 7: solicitado 8, disponível 7.

Teste 2: comprar 0 AK-47
Erro: A quantidade do item deve ser maior que zero.

Teste 3: Glock-18 com preço negativo
Erro: O preço do produto não pode ser negativo.

Teste 4: pedido sem nada
Erro: O pedido precisa ter pelo menos um item antes de ser processado.

Teste 5: adicionar Glock-17 depois de concluir o pedido
PIX de R$ 110.844,40 enviado para a chave naomandegmail@gmail.com.
Status do pedido 1005: Concluído
Erro: Não é possível adicionar itens a um pedido com status Concluído.

Teste 6: pagar pedido cancelado
Status do pedido 1006: Cancelado
Erro: Um pedido cancelado não pode ser pago.

Extra 1: cartão recusado
Erro: Cartão inválido: o número deve ter 16 dígitos.
Status do pedido 1007: Aberto
AK-47 no estoque: 14

Extra 2: cartão e boleto
Pagamento de R$ 11.942.070,00 aprovado no cartão de Seu Madruga em 12x de R$ 995.172,50.
Pedido 1008 - status: Pago
Tiguer 7 no estoque: 0
Erro: Estoque insuficiente para o produto Tiguer 7: solicitado 1, disponível 0.

Boleto de R$ 221.688,80 gerado com vencimento em 30/10/2026. Pagamento compensado.
Pedido 1010 - status: Pago
```

Em todos os casos o sistema mostra uma mensagem de erro e continua rodando, sem quebrar.