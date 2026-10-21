<?php

namespace App\Models;

class Endereco {
    private $logradouro;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $estado;
    private $cep;

    public function __construct($logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep) {
        $this->logradouro = $logradouro;
        $this->numero = $numero;
        $this->complemento = $complemento;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->cep = $cep;
    }

    public function getLogradouro() {
        return $this->logradouro;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getComplemento() {
        return $this->complemento;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getCep() {
        return $this->cep;
    }

    public function exibirEndereco() {
        echo $this->logradouro . ", " . $this->numero;

        if ($this->complemento != "") {
            echo " - " . $this->complemento;
        }

        echo " - " . $this->bairro . ", " . $this->cidade . "/" . $this->estado . " - CEP " . $this->cep . "\n";
    }
}
