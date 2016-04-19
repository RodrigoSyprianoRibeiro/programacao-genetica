<?php
require_once('Log.php');
require_once('AlgoritmosGeneticos.php');

if ($_POST) {
    $inicioExecucao = date('Y-m-d H:i:s');

    $algoritimoGenetico = new AlgoritmosGeneticos($_POST);
    $algoritimoGenetico->gerarPopulacaoInicial();

    $geracoes = $algoritimoGenetico->quantidadeGeracoes;
    while ($geracoes > 1 && count($algoritimoGenetico->populacao) > 0) {
        $algoritimoGenetico->gerarNovaPopulacao();
        $geracoes--;
    }

    $melhorIndividuo = $algoritimoGenetico->getMelhorIndividuo();

    $fimExecucao = date('Y-m-d H:i:s');
    Log::escreveArquivoUltimo($inicioExecucao, $fimExecucao, $algoritimoGenetico->aptidaoPopulacao, $_POST);
    Log::escreveArquivoHistorico($inicioExecucao, $fimExecucao, $melhorIndividuo, $_POST);

    echo json_encode($melhorIndividuo);
}