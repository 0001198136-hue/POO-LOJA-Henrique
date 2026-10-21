<?php

namespace App\Models;

use App\Exceptions\EstoqueInsuficienteException;
use InvalidArgumentException;

abstract class Produto {
    private $codigo;
    private $nome;
    private $preco;
    private $estoque;

    public function __construct($codigo, $nome, $preco, $estoque) {
        if ($preco < 0) {
            throw new InvalidArgumentException("O preço do produto não pode ser negativo.");
        }

        if ($estoque < 0) {
            throw new InvalidArgumentException("O estoque não pode ser negativo.");
        }

        $this->codigo = $codigo;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->estoque = $estoque;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function getEstoque() {
        return $this->estoque;
    }

    public function alterarPreco($novoPreco) {
        if ($novoPreco < 0) {
            throw new InvalidArgumentException("O preço do produto não pode ser negativo.");
        }

        $this->preco = $novoPreco;
    }

    public function adicionarEstoque($quantidade) {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException("Quantidade inválida para adicionar ao estoque.");
        }

        $this->estoque += $quantidade;
    }

    public function removerEstoque($quantidade) {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException("Quantidade inválida para remover do estoque.");
        }

        if ($quantidade > $this->estoque) {
            throw new EstoqueInsuficienteException("Estoque insuficiente para o produto " . $this->nome . ": solicitado " . $quantidade . ", disponível " . $this->estoque . ".");
        }

        $this->estoque -= $quantidade;
    }

    public function consultarDados() {
        echo "Código: " . $this->codigo . "\n";
        echo "Produto: " . $this->nome . "\n";
        echo "Preço: R$ " . number_format($this->preco, 2, ",", ".") . "\n";
        echo "Estoque: " . $this->estoque . "\n";
    }

    abstract public function calcularFrete();

    abstract public function calcularPrecoFinal();
}
