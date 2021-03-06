<?php

require_once('Util.php');
require_once('Arvore.php');
require_once('Node.php');

class AlgoritmosGeneticos {

    public $geracaoAtual;
    public $populacao;
    public $aptidaoPopulacao;
    // Parametros passados
    public $quantidadePopulacaoInicial; // Quantidade da população inicial.
    public $quantidadeGeracoes; // Quantidade de quantas vezes vai gerar população nova.
    public $quantidadeSelecao; // Quantidade da população que vai ser selecionada para a nova população.
    public $quantidadeCrossover; // Quantidade da população que vai fazer Crossover.
    public $quantidadeMutacao; // Quantidade da população que vai sofrer Mutação.
    public $alturaMaximaArvore; // Altura máxima que a Árvore pode ter.

    function __construct($dados) {
        $this->geracaoAtual = 1;
        $this->populacao = array();
        $this->aptidaoPopulacao = array();
        // Parametros passados
        $this->quantidadePopulacaoInicial = (int) $dados['populacao_inicial'];
        $this->quantidadeGeracoes = (int) $dados['quantidade_geracoes'];
        $this->quantidadeSelecao = (float) ($dados['quantidade_selecao'] / 100);
        $this->quantidadeCrossover = (float) ($dados['quantidade_crossover'] / 100);
        $this->quantidadeMutacao = (float) ($dados['quantidade_mutacao'] / 100);
        $this->alturaMaximaArvore = (int) $dados['altura_maxima_arvore'];
    }

    public function gerarPopulacaoInicial() {
        for ($i = 0; $i < $this->quantidadePopulacaoInicial; $i++) {
            $arvore = new Arvore($this->geracaoAtual, $this->alturaMaximaArvore);
            $arvore->montarArvore();
            $arvore->setQuantidadeNodes();
            $arvore->setFuncao();
            $arvore->calcularAptidao();
            array_push($this->populacao, $arvore);
        }
        $this->calcularAptidaoPopulacao();
    }

    public function gerarNovaPopulacao() {
        $this->geracaoAtual++;
        $this->selecaoEletista();
        $this->crossover();
        $this->mutacao();
        $this->calcularAptidaoPopulacao();
    }

    public function selecaoEletista() {
        $this->ordenarPopulacaoMelhorPior();
        $totalPopulacao = round(count($this->populacao) * $this->quantidadeSelecao);
        $populacaoEletista = array();
        for ($i = 0; $i < $totalPopulacao; $i++) {
            array_push($populacaoEletista, $this->populacao[$i]);
        }
        unset($this->populacao);
        $this->populacao = $populacaoEletista;
        unset($populacaoEletista);
    }

    public function crossover() {
        $populacaoCrossover = $this->geraPopulacaoCrossover();
        $tamanhoPopulacaoCrossover = count($populacaoCrossover);
        for ($i = 0; $i < $tamanhoPopulacaoCrossover; $i += 2) {
            $this->aplicarCrossover($populacaoCrossover[$i], $populacaoCrossover[$i+1]);
        }
        $this->populacao = array_merge($this->populacao, $populacaoCrossover);
        unset($populacaoCrossover);
    }

    public function geraPopulacaoCrossover() {
        shuffle($this->populacao);
        $quantidadeCrossover = ceil(count($this->populacao) * $this->quantidadeCrossover);
        $totalCrossover = ($quantidadeCrossover % 2 == 0) ? $quantidadeCrossover : $quantidadeCrossover - 1;
        $populacaoCrossover = array();
        for ($i = 0; $i < $totalCrossover; $i++) {
            array_push($populacaoCrossover, $this->populacao[$i]);
            unset($this->populacao[$i]);
        }
        return $populacaoCrossover;
    }

    public function aplicarCrossover($arvore1, $arvore2) {
        $arvoreFilho1 = clone $arvore1;
        $arvoreFilho1->geracao = $this->geracaoAtual;

        $arvoreFilho2 = clone $arvore2;
        $arvoreFilho2->geracao = $this->geracaoAtual;

        $nodeAleatorioArvoreFilho1 = clone $arvoreFilho1->getNodeAleatorio();
        $nodeAleatorioArvoreFilho2 = clone $arvoreFilho2->getNodeAleatorio();

        $arvoreFilho1->trocarNode($nodeAleatorioArvoreFilho2);
        $arvoreFilho2->trocarNode($nodeAleatorioArvoreFilho1);

        if ($arvoreFilho1->getAltura() <= $this->alturaMaximaArvore) {
            $arvoreFilho1->setQuantidadeNodes();
            $arvoreFilho1->setFuncao();
            $arvoreFilho1->calcularAptidao();
            array_push($this->populacao, $arvoreFilho1);
        }
        if ($arvoreFilho2->getAltura() <= $this->alturaMaximaArvore) {
            $arvoreFilho2->setQuantidadeNodes();
            $arvoreFilho2->setFuncao();
            $arvoreFilho2->calcularAptidao();
            array_push($this->populacao, $arvoreFilho2);
        }
    }

    public function mutacao() {
        shuffle($this->populacao);
        $totalMutacao = ceil(count($this->populacao) * $this->quantidadeMutacao);
        for ($i = 0; $i < $totalMutacao; $i++) {
            $this->aplicarMutacao($this->populacao[$i]);
        }
    }

    public function aplicarMutacao(&$arvore) {
        $novoNode = new Node(Util::getOperadorAleatorio());
        $novoNode->filhoEsquerdo = new Node(Util::getConstanteAleatorio());
        $novoNode->filhoDireito = new Node(Util::getConstanteAleatorio());

        $arvore->geracao = $this->geracaoAtual;
        $arvore->getNodeAleatorio();
        $arvore->trocarNode($novoNode);
        $arvore->setQuantidadeNodes();
        $arvore->setFuncao();
        $arvore->calcularAptidao();
    }

    public function getMelhorIndividuo() {
        if (count($this->populacao) > 0) {
            $this->ordenarPopulacaoMelhorPior();
            return $this->populacao[0];
        }
        return (object) array('funcao' => 'Não existe pois a população foi extinta.', 'aptidao' => 'Não existe.');
    }

    public function calcularAptidaoPopulacao() {
        $tamanhoPopulacao = count($this->populacao);
        if ($tamanhoPopulacao > 0) {
            $somaAptidao = 0;
            foreach ($this->populacao AS $arvore) {
                $somaAptidao += $arvore->aptidao;
            }
            $mediaAptidao = round(($somaAptidao / $tamanhoPopulacao), 2);
        } else {
            $mediaAptidao = 0;
        }
        $melhorIndividuo = $this->getMelhorIndividuo();
        $dados = array('tamanhoPopulacao' => $tamanhoPopulacao,
                       'mediaAptidao' => $mediaAptidao,
                       'melhorFuncao' => $melhorIndividuo->funcao,
                       'melhorAptidao' => $melhorIndividuo->aptidao);
        $this->aptidaoPopulacao[$this->geracaoAtual] = $dados;
    }

    public function ordenarPopulacaoMelhorPior() {
        if (!function_exists('ordenador')) {
            function ordenador($arvore1, $arvore2) {
                if ($arvore1->aptidao < $arvore2->aptidao) {
                    return -1;
                } elseif ($arvore1->aptidao > $arvore2->aptidao) {
                    return +1;
                }
                return 0;
            }
        }
        usort($this->populacao, 'ordenador');
    }
}