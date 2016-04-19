<?php

class Log {

  public static function mostraTempoExecucao($inicioExecucao, $fimExecucao) {

        $dateTime = new DateTime($inicioExecucao);
        $diferenca = $dateTime->diff(new DateTime($fimExecucao));

        $texto = "";
        $texto .= $diferenca->h > 0 ? $diferenca->h . ' hora(s), ' : '';
        $texto .= $diferenca->i > 0 ? $diferenca->i . ' minuto(s) e ' : '';
        $texto .= $diferenca->s . ' segundo(s)';

        return $texto;
    }

    public static function escreveArquivoUltimo($inicioExecucao, $fimExecucao, $aptidaoPopulacao, $dados) {
        $log = fopen("../log-ultimo.txt", "w");
        $texto  = "Execução: ".self::formatoDataHoraPadrao($inicioExecucao, true)." - ".self::formatoDataHoraPadrao($fimExecucao, true)." (Duração: ".self::mostraTempoExecucao($inicioExecucao, $fimExecucao).") \n";
        $texto .= "Parâmetros: \n";
        $texto .= "População inicial: ".$dados['populacao_inicial']."\n";
        $texto .= "Quantidade de Gerações: ".$dados['quantidade_geracoes']."\n";
        $texto .= "Altura máxima da Árvore: ".$dados['altura_maxima_arvore']."\n";
        $texto .= "Quantidade selecionada para a nova população: ".$dados['quantidade_selecao']."%\n";
        $texto .= "Quantidade da população que vai fazer Crossover: ".$dados['quantidade_crossover']."%\n";
        $texto .= "Quantidade da população que vai sofrer Mutação: ".$dados['quantidade_mutacao']."%\n\n";
        foreach ($aptidaoPopulacao AS $geracao => $dados) {
            $texto .= "Geração {$geracao}: \n";
            $texto .= "Tamanho População: ".$dados['tamanhoPopulacao']."\n";
            $texto .= "Média Aptidão: ".$dados['mediaAptidao']."\n";
            $texto .= "Melhor Função: ".$dados['melhorFuncao']." ";
            $texto .= "Melhor Aptidão: ".$dados['melhorAptidao']."\n\n";
        }
        fwrite($log, $texto);
        fclose($log);
    }

    public static function escreveArquivoHistorico($inicioExecucao, $fimExecucao, $melhorIndividuo, $dados) {
        $log = fopen("../log-historico.txt", "a");
        $texto  = "Execução: ".self::formatoDataHoraPadrao($inicioExecucao, true)." - ".self::formatoDataHoraPadrao($fimExecucao, true)." (Duração: ".self::mostraTempoExecucao($inicioExecucao, $fimExecucao).") \n";
        $texto .= "Parâmetros: \n";
        $texto .= "População inicial: ".$dados['populacao_inicial']."\n";
        $texto .= "Quantidade de Gerações: ".$dados['quantidade_geracoes']."\n";
        $texto .= "Altura máxima da Árvore: ".$dados['altura_maxima_arvore']."\n";
        $texto .= "Quantidade selecionada para a nova população: ".$dados['quantidade_selecao']."%\n";
        $texto .= "Quantidade da população que vai fazer Crossover: ".$dados['quantidade_crossover']."%\n";
        $texto .= "Quantidade da população que vai sofrer Mutação: ".$dados['quantidade_mutacao']."%\n";
        $texto .= "Melhor Função: ".$melhorIndividuo->funcao." ";
        $texto .= "Aptidão: ".$melhorIndividuo->aptidao."\n\n";
        fwrite($log, $texto);
        fclose($log);
    }

    public static function formatoDataHoraPadrao($dataHora,$exibirHora=true) {
        $novaDataHora = explode(' ', $dataHora);
        $novaData = explode('-', $novaDataHora[0]);
        $novaHora = $exibirHora === true ? $novaDataHora[1] : "";
        return $novaData[2]."/".$novaData[1]."/".$novaData[0]." ".$novaHora;
    }
}