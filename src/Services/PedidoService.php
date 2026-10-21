<?php

namespace App\Services;

use App\Exceptions\EstoqueInsuficienteException;
use App\Exceptions\PagamentoException;
use App\Exceptions\PedidoInvalidoException;
use App\Interfaces\PagamentoInterface;
use App\Models\Pedido;

class PedidoService {
    public function validarPedido(Pedido $pedido) {
        if ($pedido->getStatus() == Pedido::CANCELADO) {
            throw new PedidoInvalidoException("Um pedido cancelado não pode ser pago.");
        }

        if ($pedido->getStatus() != Pedido::ABERTO) {
            throw new PedidoInvalidoException("O pedido com status " . $pedido->getStatus() . " não pode ser processado.");
        }

        if ($pedido->estaVazio()) {
            throw new PedidoInvalidoException("O pedido precisa ter pelo menos um item antes de ser processado.");
        }
    }

    public function verificarEstoque(Pedido $pedido) {
        foreach ($pedido->getItens() as $item) {
            $produto = $item->getProduto();

            if ($item->getQuantidade() > $produto->getEstoque()) {
                throw new EstoqueInsuficienteException("Estoque insuficiente para o produto " . $produto->getNome() . ": solicitado " . $item->getQuantidade() . ", disponível " . $produto->getEstoque() . ".");
            }
        }
    }

    public function processarPagamento(Pedido $pedido, PagamentoInterface $pagamento) {
        $pedido->definirPagamento($pagamento);
        $this->alterarStatus($pedido, Pedido::AGUARDANDO_PAGAMENTO);

        try {
            $pagamento->pagar($pedido->calcularValorTotal());
        } catch (PagamentoException $e) {
            $this->alterarStatus($pedido, Pedido::ABERTO);
            throw $e;
        }

        $this->alterarStatus($pedido, Pedido::PAGO);
    }

    public function atualizarEstoque(Pedido $pedido) {
        foreach ($pedido->getItens() as $item) {
            $item->getProduto()->removerEstoque($item->getQuantidade());
        }
    }

    public function alterarStatus(Pedido $pedido, $novoStatus) {
        $pedido->alterarStatus($novoStatus);
    }

    public function cancelarPedido(Pedido $pedido) {
        $this->alterarStatus($pedido, Pedido::CANCELADO);
    }

    public function processar(Pedido $pedido, PagamentoInterface $pagamento) {
        $this->validarPedido($pedido);
        $this->verificarEstoque($pedido);
        $this->processarPagamento($pedido, $pagamento);
        $this->atualizarEstoque($pedido);
    }

    public function finalizarPedido(Pedido $pedido) {
        if ($pedido->getStatus() != Pedido::PAGO && $pedido->getStatus() != Pedido::ENVIADO) {
            throw new PedidoInvalidoException("O pagamento deve ocorrer antes da conclusão do pedido.");
        }

        $this->alterarStatus($pedido, Pedido::CONCLUIDO);
    }
}
