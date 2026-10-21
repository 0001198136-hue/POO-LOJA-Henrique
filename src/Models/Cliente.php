<?php

namespace App\Models;

use InvalidArgumentException;

class Cliente {
    private $codigo;
    private $nome;
    private $cpf;
    private $email;
    private $telefone;
    private $enderecos = [];

    public function __construct($codigo, $nome, $cpf, $email, $telefone, Endereco $enderecoPrincipal) {
        $this->codigo = $codigo;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->setEmail($email);
        $this->telefone = $telefone;
        $this->enderecos[] = $enderecoPrincipal;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("E-mail inválido.");
        }

        $this->email = $email;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getEnderecos() {
        return $this->enderecos;
    }

    public function adicionarEndereco(Endereco $endereco) {
        $this->enderecos[] = $endereco;
    }

    public function removerEndereco($indice) {
        if (!isset($this->enderecos[$indice])) {
            throw new InvalidArgumentException("Endereço não encontrado.");
        }

        if (count($this->enderecos) == 1) {
            throw new InvalidArgumentException("O cliente deve possuir pelo menos um endereço.");
        }

        unset($this->enderecos[$indice]);
        $this->enderecos = array_values($this->enderecos);
    }
}
