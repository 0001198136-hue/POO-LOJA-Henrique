<?php

namespace App\Models;

use App\Exceptions\PedidoInvalidoException;

class ItemPedido {
    private $produto;
    private $quantidade;
    private $precoUnitario;

    public function __construct(Produto $produto, $quantidade) {
        if ($quantidade <= 0) {
            throw new PedidoInvalidoException("A quantidade do item deve ser maior que zero.");
        }

        $this->produto = $produto;
        $this->quantidade = $quantidade;
        $this->precoUnitario = $produto->getPreco();
    }

    public function getProduto() {
        return $this->produto;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function getPrecoUnitario() {
        return $this->precoUnitario;
    }

    public function getSubtotal() {
        return round($this->precoUnitario * $this->quantidade, 2);
    }

    public function getFrete() {
        return round($this->produto->calcularFrete() * $this->quantidade, 2);
    }

    public function somarQuantidade($quantidade) {
        if ($quantidade <= 0) {
            throw new PedidoInvalidoException("A quantidade do item deve ser maior que zero.");
        }

        $this->quantidade += $quantidade;
    }
}
