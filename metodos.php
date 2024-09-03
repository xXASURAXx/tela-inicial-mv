<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$horaAtual = date("H");
$diaSemana = date("N");
$diaMes = date("j");
$mes = date("n");

$nomeMesAtual = obterNomeMes($mes);
$saudacao = saudacao($horaAtual);
$plantao = trocaPlantao($horaAtual);
$semana = inicioSemana($diaSemana);
$inicioMes = inicioMes($diaMes);
$inicioAno = inicioAno($mes);

function atualizar() {
  header("Refresh: 1800");
}

function saudacao($hora) {
    $hora = intval($hora);
    
    if ($hora >= 6 && $hora <= 12) {
        return "Bom dia!";
    } elseif ($hora >= 13 && $hora <= 18) {
        return "Boa tarde!";
    } elseif ($hora >= 19 && $hora <= 23) {
        return "Boa noite!";
    } else {
        return "Boa madrugada!";
    }
}

function trocaPlantao($hora) {
    $hora = intval($hora);
    
    if (($hora >= 5 && $hora <= 6) || ($hora >= 17 && $hora <= 18)) {
        return "Bom final de plantão";
    } elseif (($hora >= 7 && $hora <= 8) || ($hora >= 19 && $hora <= 20)) {
        return "Bom início de plantão";
    } else {
        return "";
    }
}

function inicioSemana($diaSemana) {
    $diaSemana = intval($diaSemana);
    
    if ($diaSemana == 1) {
        return "Desejo uma ótima semana!";
    } elseif ($diaSemana == 6) {
        return "Desejo um excelente final de semana!";
    } else {
        return "";
    }
}

function inicioMes($diaMes) {
    $diaMes = intval($diaMes);
    
    if ($diaMes == 1) {
        global $nomeMesAtual;
        return "Desejo que o mês de " . $nomeMesAtual . " seja repleto de realizações!";
    } else {
        return "";
    }
}

function inicioAno($mes) {
    $mes = intval($mes);
    
    if ($mes == 1) {
        return "Desejo um feliz ano novo!";
    } else {
        return "";
    }
}

function obterNomeMes($mes) {
    $meses = array(
        1 => "Janeiro",
        2 => "Fevereiro",
        3 => "Março",
        4 => "Abril",
        5 => "Maio",
        6 => "Junho",
        7 => "Julho",
        8 => "Agosto",
        9 => "Setembro",
        10 => "Outubro",
        11 => "Novembro",
        12 => "Dezembro"
    );
    
    return $meses[$mes];
}
?>
