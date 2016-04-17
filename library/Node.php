<?php

class Node
{
    public $valor;
    public $filhoEsquerdo;
    public $filhoDireito;

    function __construct($valor) {
        $this->valor = $valor;
        $this->filhoEsquerdo = null;
        $this->filhoDireito = null;
    }
}