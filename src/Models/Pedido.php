<?php

namespace App\Models;

use App\Exceptions\EstoqueInsuficienteException;
use App\Exceptions\PedidoInvalidoException;
use App\Interfaces\PagamentoInterface;

class Pedido {
    const ABERTO = "Aberto";
    const AGUARDANDO_PAGAMENTO = "Aguardando Pagamento";
    const PAGO = "Pago";
    const ENVIADO = "Enviado";
    const CONCLUIDO = "Concluído";
    const CANCELADO = "Cancelado";

    private $numero;
    private $cliente;
    private $data;
    private $itens = [];
    private $status;
    private $pagamento = null;

    public function __construct($numero, Cliente $cliente) {
        $this->numero = $numero;
        $this->cliente = $cliente;
        $this->data = date("d/m/Y");
        $this->status = self::ABERTO;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getCliente() {
        return $this->cliente;
    }

    public function getData() {
        return $this->data;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getPagamento() {
        return $this->pagamento;
    }

    public function definirPagamento(PagamentoInterface $pagamento) {
        $this->pagamento = $pagamento;
    }

    public function getItens() {
        return $this->itens;
    }

    public function estaVazio() {
        return count($this->itens) == 0;
    }

    private function buscarItem($codigoProduto) {
        foreach ($this->itens as $item) {
            if ($item->getProduto()->getCodigo() == $codigoProduto) {
                return $item;
            }
        }

        return null;
    }

    public function adicionarItem(Produto $produto, $quantidade) {
        if ($this->status != self::ABERTO) {
            throw new PedidoInvalidoException("Não é possível adicionar itens a um pedido com status " . $this->status . ".");
        }

        if ($quantidade <= 0) {
            throw new PedidoInvalidoException("A quantidade do item deve ser maior que zero.");
        }

        $itemExistente = $this->buscarItem($produto->getCodigo());
        $quantidadeNoPedido = 0;

        if ($itemExistente != null) {
            $quantidadeNoPedido = $itemExistente->getQuantidade();
        }

        if ($quantidadeNoPedido + $quantidade > $produto->getEstoque()) {
            throw new EstoqueInsuficienteException("Estoque insuficiente para o produto " . $produto->getNome() . ": solicitado " . ($quantidadeNoPedido + $quantidade) . ", disponível " . $produto->getEstoque() . ".");
        }

        if ($itemExistente != null) {
            $itemExistente->somarQuantidade($quantidade);
        } else {
            $this->itens[] = new ItemPedido($produto, $quantidade);
        }
    }

    public function removerItem($codigoProduto) {
        if ($this->status != self::ABERTO) {
            throw new PedidoInvalidoException("Não é possível remover itens de um pedido com status " . $this->status . ".");
        }

        foreach ($this->itens as $indice => $item) {
            if ($item->getProduto()->getCodigo() == $codigoProduto) {
                unset($this->itens[$indice]);
                $this->itens = array_values($this->itens);
                return;
            }
        }

        throw new PedidoInvalidoException("O produto informado não está no pedido.");
    }

    public function calcularSubtotal() {
        $total = 0;

        foreach ($this->itens as $item) {
            $total += $item->getSubtotal();
        }

        return round($total, 2);
    }

    public function calcularFrete() {
        $total = 0;

        foreach ($this->itens as $item) {
            $total += $item->getFrete();
        }

        return round($total, 2);
    }

    public function calcularValorTotal() {
        return round($this->calcularSubtotal() + $this->calcularFrete(), 2);
    }

    public function alterarStatus($novoStatus) {
        $permitidos = [
            self::ABERTO => [self::AGUARDANDO_PAGAMENTO, self::CANCELADO],
            self::AGUARDANDO_PAGAMENTO => [self::PAGO, self::ABERTO, self::CANCELADO],
            self::PAGO => [self::ENVIADO, self::CONCLUIDO],
            self::ENVIADO => [self::CONCLUIDO],
            self::CONCLUIDO => [],
            self::CANCELADO => []
        ];

        if (!in_array($novoStatus, $permitidos[$this->status])) {
            throw new PedidoInvalidoException("Não é possível mudar o status de " . $this->status . " para " . $novoStatus . ".");
        }

        $this->status = $novoStatus;
    }

    public function listarItens() {
        foreach ($this->itens as $item) {
            echo "Produto: " . $item->getProduto()->getNome() . "\n";
            echo "Quantidade: " . $item->getQuantidade() . "\n";
            echo "Valor unitário: R$ " . number_format($item->getPrecoUnitario(), 2, ",", ".") . "\n";
            echo "Subtotal: R$ " . number_format($item->getSubtotal(), 2, ",", ".") . "\n";
            echo "\n";
        }
    }

    public function exibirResumo() {
        echo "PEDIDO Nº " . $this->numero . "\n";
        echo "\n";
        echo "Cliente: " . $this->cliente->getNome() . "\n";
        echo "\n";
        echo "ITENS\n";
        echo "\n";

        $this->listarItens();

        echo "\n";
        echo "Frete: R$ " . number_format($this->calcularFrete(), 2, ",", ".") . "\n";
        echo "Valor Total: R$ " . number_format($this->calcularValorTotal(), 2, ",", ".") . "\n";

        if ($this->pagamento != null) {
            echo "Forma de pagamento: " . $this->pagamento->getNome() . "\n";
        } else {
            echo "Forma de pagamento: não definida\n";
        }

        echo "Status: " . $this->status . "\n";

        if ($this->pagamento != null && ($this->status == self::PAGO || $this->status == self::ENVIADO || $this->status == self::CONCLUIDO)) {
            echo "Pagamento realizado com sucesso.\n";
        }

        echo "\n";
    }
}
