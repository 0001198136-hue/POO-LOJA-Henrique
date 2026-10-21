<?php

namespace App\Interfaces;

interface PagamentoInterface {
    public function pagar($valor);

    public function getNome();
}
