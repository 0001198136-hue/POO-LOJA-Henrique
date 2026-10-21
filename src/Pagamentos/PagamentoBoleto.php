<?php

namespace App\Pagamentos;

use App\Exceptions\PagamentoException;
use App\Interfaces\PagamentoInterface;

class PagamentoBoleto implements PagamentoInterface {
    private $vencimento;

    public function __construct($vencimento) {
        $this->vencimento = $vencimento;
    }

    public function getNome() {
        return "Boleto";
    }

    public function pagar($valor) {
        if ($valor <= 0) {
            throw new PagamentoException("Valor inválido para gerar o boleto.");
        }

        if ($this->vencimento == "") {
            throw new PagamentoException("Data de vencimento do boleto não informada.");
        }

        echo "Boleto de R$ " . number_format($valor, 2, ",", ".") . " gerado com vencimento em " . $this->vencimento . ". Pagamento compensado.\n";

        return true;
    }
}
