<?php

namespace App\Pagamentos;

use App\Exceptions\PagamentoException;
use App\Interfaces\PagamentoInterface;

class PagamentoPix implements PagamentoInterface {
    private $chave;

    public function __construct($chave) {
        $this->chave = $chave;
    }

    public function getNome() {
        return "PIX";
    }

    public function pagar($valor) {
        if ($valor <= 0) {
            throw new PagamentoException("Valor inválido para pagamento via PIX.");
        }

        if ($this->chave == "") {
            throw new PagamentoException("Chave PIX não informada.");
        }

        echo "PIX de R$ " . number_format($valor, 2, ",", ".") . " enviado para a chave " . $this->chave . ".\n";

        return true;
    }
}
