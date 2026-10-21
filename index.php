<?php

require __DIR__ . "/autoload.php";

use App\Exceptions\EstoqueInsuficienteException;
use App\Exceptions\PagamentoException;
use App\Exceptions\PedidoInvalidoException;
use App\Models\Cliente;
use App\Models\Endereco;
use App\Models\Pedido;
use App\Models\ProdutoDigital;
use App\Models\ProdutoFisico;
use App\Pagamentos\PagamentoBoleto;
use App\Pagamentos\PagamentoCartao;
use App\Pagamentos\PagamentoPix;
use App\Services\PedidoService;

$service = new PedidoService();

$endereco = new Endereco("Rua das Flores", 100, "", "Centro", "São Paulo", "SP", "01000-000");
$cliente = new Cliente(1, "Carlos Silva", "123.456.789-00", "carlos@email.com", "(11) 99999-0000", $endereco);

$teclado = new ProdutoFisico(1, "Teclado Mecânico", 350.00, 10, 1.2, 45, 4, 15);
$curso = new ProdutoDigital(2, "Curso PHP Orientado a Objetos", 199.90, 100, 2048, "MP4", "https://loja.com/downloads/curso-php-poo");

$glock = new ProdutoFisico(3, "Glock-17", 11200.00, 13, 0.7, 20, 14, 3);
$tiguer = new ProdutoFisico(4, "Tiguer 7", 1250000.00, 7, 57000, 700, 300, 1000);
$ak = new ProdutoFisico(5, "AK-47", 110800.00, 15, 4.3, 87, 20, 6);

try {
    $pedido = new Pedido(1001, $cliente);
    $pedido->adicionarItem($teclado, 2);
    $pedido->adicionarItem($curso, 1);

    $service->processar($pedido, new PagamentoPix("naomandegmail@gmail.com"));

    echo "\n";
    $pedido->exibirResumo();
    echo "\n";
    echo "Sobrou no estoque: " . $teclado->getEstoque() . " teclados e " . $curso->getEstoque() . " cursos.\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 1: comprar 8 Tiguer 7\n";
try {
    $pedido1 = new Pedido(1002, $cliente);
    $pedido1->adicionarItem($tiguer, 8);
} catch (EstoqueInsuficienteException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 2: comprar 0 AK-47\n";
try {
    $pedido2 = new Pedido(1003, $cliente);
    $pedido2->adicionarItem($ak, 0);
} catch (PedidoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 3: Glock-18 com preço negativo\n";
try {
    $glock18 = new ProdutoFisico(6, "Glock-18", -500.00, 5, 0.7, 20, 14, 3);
} catch (InvalidArgumentException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 4: pedido sem nada\n";
try {
    $pedido4 = new Pedido(1004, $cliente);
    $service->processar($pedido4, new PagamentoPix("naomandegmail@gmail.com"));
} catch (PedidoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 5: adicionar Glock-17 depois de concluir o pedido\n";
try {
    $pedido5 = new Pedido(1005, $cliente);
    $pedido5->adicionarItem($ak, 1);
    $service->processar($pedido5, new PagamentoPix("naomandegmail@gmail.com"));
    $service->finalizarPedido($pedido5);
    echo "Status do pedido 1005: " . $pedido5->getStatus() . "\n";

    $pedido5->adicionarItem($glock, 1);
} catch (PedidoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 6: pagar pedido cancelado\n";
try {
    $pedido6 = new Pedido(1006, $cliente);
    $pedido6->adicionarItem($glock, 1);
    $service->cancelarPedido($pedido6);
    echo "Status do pedido 1006: " . $pedido6->getStatus() . "\n";

    $service->processar($pedido6, new PagamentoPix("naomandegmail@gmail.com"));
} catch (PedidoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Extra 1: cartão recusado\n";
try {
    $pedido7 = new Pedido(1007, $cliente);
    $pedido7->adicionarItem($ak, 1);
    $service->processar($pedido7, new PagamentoCartao("1234", "Carlos Silva", 1));
} catch (PagamentoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Status do pedido 1007: " . $pedido7->getStatus() . "\n";
    echo "AK-47 no estoque: " . $ak->getEstoque() . "\n";
}

echo "\n";

echo "Extra 2: cartão e boleto\n";
try {
    $cliente2 = new Cliente(2, "Seu Madruga", "000.000.000-00", "naomandegmail@gmail.com", "(11) 00000-0000", $endereco);

    $pedido8 = new Pedido(1008, $cliente2);
    $pedido8->adicionarItem($tiguer, 7);
    $service->processar($pedido8, new PagamentoCartao("1234567890123456", "Seu Madruga", 12));
    echo "Pedido 1008 - status: " . $pedido8->getStatus() . "\n";
    echo "Tiguer 7 no estoque: " . $tiguer->getEstoque() . "\n";

    $pedido9 = new Pedido(1009, $cliente);
    $pedido9->adicionarItem($tiguer, 1);
} catch (EstoqueInsuficienteException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

try {
    $pedido10 = new Pedido(1010, $cliente);
    $pedido10->adicionarItem($ak, 2);
    $service->processar($pedido10, new PagamentoBoleto("30/10/2026"));
    echo "Pedido 1010 - status: " . $pedido10->getStatus() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
