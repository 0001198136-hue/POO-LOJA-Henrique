<?php

namespace App\Models;

class ProdutoDigital extends Produto {
    private $tamanhoArquivo;
    private $formato;
    private $linkDownload;

    public function __construct($codigo, $nome, $preco, $estoque, $tamanhoArquivo, $formato, $linkDownload) {
        parent::__construct($codigo, $nome, $preco, $estoque);

        $this->tamanhoArquivo = $tamanhoArquivo;
        $this->formato = $formato;
        $this->linkDownload = $linkDownload;
    }

    public function getTamanhoArquivo() {
        return $this->tamanhoArquivo;
    }

    public function getFormato() {
        return $this->formato;
    }

    public function getLinkDownload() {
        return $this->linkDownload;
    }

    public function calcularFrete() {
        return 0;
    }

    public function calcularPrecoFinal() {
        return $this->getPreco();
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "Tamanho do arquivo: " . $this->tamanhoArquivo . " MB\n";
        echo "Formato: " . $this->formato . "\n";
        echo "Link para download: " . $this->linkDownload . "\n";
        echo "\n";
    }
}
