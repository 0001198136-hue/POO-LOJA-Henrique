<?php

namespace App\Pagamentos;

use App\Exceptions\PagamentoException;
use App\Interfaces\PagamentoInterface;

class PagamentoCartao implements PagamentoInterface {
    private $numero;
    private $titular;
    private $parcelas;

    public function __construct($numero, $titular, $parcelas) {
        $this->numero = $numero;
        $this->titular = $titular;
        $this->parcelas = $parcelas;
    }

    public function getNome() {
        return "Cartão de Crédito";
    }

    public function pagar($valor) {
        if ($valor <= 0) {
            throw new PagamentoException("Valor inválido para pagamento no cartão.");
        }

        if (strlen($this->numero) != 16) {
            throw new PagamentoException("Cartão inválido: o número deve ter 16 dígitos.");
        }

        if ($this->parcelas < 1 || $this->parcelas > 12) {
            throw new PagamentoException("Parcelas inválidas: escolha de 1 a 12.");
        }

        $valorParcela = $valor / $this->parcelas;

        echo "Pagamento de R$ " . number_format($valor, 2, ",", ".") . " aprovado no cartão de " . $this->titular . " em " . $this->parcelas . "x de R$ " . number_format($valorParcela, 2, ",", ".") . ".\n";

        return true;
    }
}
