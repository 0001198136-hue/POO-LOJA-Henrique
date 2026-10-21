<?php

namespace App\Models;

use InvalidArgumentException;

class ProdutoFisico extends Produto {
    private $peso;
    private $largura;
    private $altura;
    private $profundidade;

    public function __construct($codigo, $nome, $preco, $estoque, $peso, $largura, $altura, $profundidade) {
        parent::__construct($codigo, $nome, $preco, $estoque);

        if ($peso <= 0) {
            throw new InvalidArgumentException("O peso do produto deve ser maior que zero.");
        }

        $this->peso = $peso;
        $this->largura = $largura;
        $this->altura = $altura;
        $this->profundidade = $profundidade;
    }

    public function getPeso() {
        return $this->peso;
    }

    public function getLargura() {
        return $this->largura;
    }

    public function getAltura() {
        return $this->altura;
    }

    public function getProfundidade() {
        return $this->profundidade;
    }
 public function calcularFrete() {
        return round(10 + ($this->peso * 8), 2);
    }

    public function calcularPrecoFinal() {
        return $this->getPreco() + $this->calcularFrete();
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "Peso: " . $this->peso . " kg\n";
        echo "Dimensões (L x A x P): " . $this->largura . " x " . $this->altura . " x " . $this->profundidade . " cm\n";
        echo "\n";
    }
}
