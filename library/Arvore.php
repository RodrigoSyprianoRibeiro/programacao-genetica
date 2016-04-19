<?php

class Arvore
{
    public $geracao;

    public $raiz;
    public $quantidadeNodes;
    public $alturaMaxima;

    public $funcao;
    public $aptidao;

    public $listaNodes;
    public $nodeAleatorio;
    public $efetuadaTroca;

    function __construct($geracao, $alturaMaxima) {
        $this->geracao = $geracao;

        $this->quantidadeNodes = 0;
        $this->alturaMaxima = $alturaMaxima;

        $this->funcao = "";
        $this->aptidao = 0;

        $this->listaNodes = null;
        $this->nodeAleatorio = null;
    }

    public function montarArvore() {
        $this->raiz = new Node(Util::getOperadorAleatorio());
        $this->inserir($this->raiz);
    }

    public function inserir(Node $node) {
        if (Util::isOperador($node->valor)) {
            $valorFilhoEsquerdo = $this->getAltura() < $this->alturaMaxima-1 ?  Util::getOperadorOuConstanteAleatorio() : Util::getConstanteAleatorio();
            $node->filhoEsquerdo = new Node($valorFilhoEsquerdo);
            $this->inserir($node->filhoEsquerdo);
            $valorFilhoDireito = $this->getAltura() < $this->alturaMaxima ?  Util::getOperadorOuConstanteAleatorio() : Util::getConstanteAleatorio();
            $node->filhoDireito = new Node($valorFilhoDireito);
            $this->inserir($node->filhoDireito);
        }
    }

    public function setFuncao() {
        $this->funcao = $this->montarFuncao($this->raiz);
    }

    public function montarFuncao($node){
        if (Util::isOperador($node->valor)) {
            return '('.$this->montarFuncao($node->filhoEsquerdo).$node->valor.$this->montarFuncao($node->filhoDireito).')';
        }
        if (is_numeric($node->valor)) {
            return ($node->valor) < 0 ? "(".$node->valor.")" : $node->valor;
        } else {
            return $node->valor;
        }
    }

    public function calcularAptidao() {
        foreach (Util::$ENTRADAS_SAIDAS AS $entrada => $saida) {
            $funcao = str_replace("x", $entrada, $this->funcao);
            if(@eval('$valor = '.$funcao.';') === false){
                $valor = 100;
            }
            $this->aptidao += pow(($saida - $valor), 2);
        }
        $this->aptidao = round(sqrt($this->aptidao), 2);
    }

    public function getAltura() {
        return $this->altura($this->raiz);
    }

    public function altura($node) {
        if ($node == null) {
            return -1;
        }
        $alturaEsquerdo = $this->altura($node->filhoEsquerdo);
        $alturaDireito = $this->altura($node->filhoDireito);
        return (($alturaEsquerdo > $alturaDireito) ? $alturaEsquerdo : $alturaDireito) + 1;
    }

    public function setQuantidadeNodes() {
        $this->quantidadeNodes = 0;
        $this->quantidadeNodes = $this->quantidadeNodes($this->raiz);
    }

    public function quantidadeNodes($node) {
        if ($node == null) {
            return 0;
        }
        return $this->quantidadeNodes($node->filhoEsquerdo) + $this->quantidadeNodes($node->filhoDireito) + 1;
    }

    public function getNodeAleatorio() {
        $this->ordemSimetrica($this->raiz);
        shuffle($this->listaNodes);
        $this->nodeAleatorio = $this->listaNodes[0];
        unset($this->listaNodes);
        return $this->nodeAleatorio;
    }

    public function ordemSimetrica($node) {
        if ($node == null) {
            return;
        }
        if ($node !== $this->raiz) {
            $this->listaNodes[] = $node;
        }
        $this->ordemSimetrica($node->filhoEsquerdo);
        $this->ordemSimetrica($node->filhoDireito);
    }

    public function trocarNode($novoNode) {
        $this->efetuadaTroca = false;
        $this->encontrarNodeTroca($this->raiz, $novoNode);
    }

    public function encontrarNodeTroca(&$node, $novoNode) {
        if ($node == null) {
            return;
        }
        if ($node === $this->nodeAleatorio && $this->efetuadaTroca === false) {
            $node = clone $novoNode;
            $this->efetuadaTroca = true;
        }
        if ($this->efetuadaTroca === false) {
            $this->encontrarNodeTroca($node->filhoEsquerdo, $novoNode);
        }
        if ($this->efetuadaTroca === false) {
            $this->encontrarNodeTroca($node->filhoDireito, $novoNode);
        }
    }
}