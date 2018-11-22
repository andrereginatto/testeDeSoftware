<?php
session_start();
require_once './Classes/DAO_MOVIMENTOS.php';
$MovimentosDAO = new DAO_MOVIMENTOS();


function get_inicio_fim_semana($numero_semana = "", $ano = "")
{
/* soma o número de semanas em cima do início do ano 01/01/2013 */
$semana_atual = strtotime('+'.$numero_semana.' weeks', strtotime($ano.'0101'));
 
/*
pega o número do dia da semana
0 - Domingo
...
6 - Sábado
*/
$dia_semana = date('w', $semana_atual);
 
/*
diminui o dia da semana sobre o dia da semana atual
ex.: $semana_atual: 10/09/2013 terça-feira
$dia_semana: 2 (terça-feira)
$data_inicio_semana: 08/09/2013
*/
$data_inicio_semana = strtotime('-'.$dia_semana.' days', $semana_atual);
 
/* Data início semana */
$primeiro_dia_semana = date('Y-m-d', $data_inicio_semana);
 
/* Soma 6 dias */
$ultimo_dia_semana = date('Y-m-d', strtotime('+6 days', strtotime($primeiro_dia_semana)));
 
/* retorna */
return array($primeiro_dia_semana, $ultimo_dia_semana);
}
date_default_timezone_set('America/Sao_Paulo');

$data_atual =  date('Y/m/01');
$ultimodia = date('Y/m/t',strtotime($data_atual));
$data_final = date('d/m/Y');

$arrayGastos = array();
$arrayReceitas = array();
$arrayTitulos = array();

$flag = true;
while ($flag == true){
    $numero_semana = date('W',strtotime($data_atual))-1;    
    $ano_atual = date('Y',strtotime($data_atual));

    list($data_inicio, $data_final) = get_inicio_fim_semana($numero_semana, $ano_atual);
    
    $data_atual = date('Y/m/d', strtotime('+3 days', strtotime($data_final)));
    $data_final = date('Y/m/d', strtotime($data_final));
    $data_inicio = date('Y/m/d', strtotime($data_inicio));
    
    
    $grafico = $MovimentosDAO->selectGrafico($data_inicio, $data_final, $_SESSION['id']);
    $arrayGastos[]= str_replace('-','',$grafico['gastos']);
    $arrayReceitas[]= $grafico['entradas'];
    $date_inicio_text=date_create($data_inicio);
    $date_fim_text=date_create($data_final);
    
    $date_inicio_text =  date_format($date_inicio_text,"d/m/Y");
    $date_fim_text =  date_format($date_fim_text,"d/m/Y");
    
    $arrayTitulos[]= $date_inicio_text.' á '.$date_fim_text;
    
    $data1 = $data_final;
    $data2 = $ultimodia;
    $d1 = strtotime($data1); 
    $d2 = strtotime($data2);
    // verifica a diferença em segundos entre as duas datas e divide pelo número de segundos que um dia possui
    $subtracao = ($d2 - $d1) /86400;
    
    if($subtracao <= 0){
        $flag=false;
    }
}

?>
<html>
<head>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/css">
    /**
 * (c) 2010-2018 Torstein Honsi
 *
 * License: www.highcharts.com/license
 *
 * Grid-light theme for Highcharts JS
 * @author Torstein Honsi
 */

'use strict';
import Highcharts from '../parts/Globals.js';
/* global document */
// Load the fonts
Highcharts.createElement('link', {
    href: 'https://fonts.googleapis.com/css?family=Dosis:400,600',
    rel: 'stylesheet',
    type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
    colors: ['#7cb5ec', '#f7a35c', '#90ee7e', '#7798BF', '#aaeeee', '#ff0066',
        '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
    chart: {
        backgroundColor: null,
        style: {
            fontFamily: 'Dosis, sans-serif'
        }
    },
    title: {
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            textTransform: 'uppercase'
        }
    },
    tooltip: {
        borderWidth: 0,
        backgroundColor: 'rgba(219,219,216,0.8)',
        shadow: false
    },
    legend: {
        itemStyle: {
            fontWeight: 'bold',
            fontSize: '13px'
        }
    },
    xAxis: {
        gridLineWidth: 1,
        labels: {
            style: {
                fontSize: '12px'
            }
        }
    },
    yAxis: {
        minorTickInterval: 'auto',
        title: {
            style: {
                textTransform: 'uppercase'
            }
        },
        labels: {
            style: {
                fontSize: '12px'
            }
        }
    },
    plotOptions: {
        candlestick: {
            lineColor: '#404048'
        }
    },


    // General
    background2: '#F0F0EA'

};
Highcharts.setOptions(Highcharts.theme);
    </script>
</head>
<body>
    <div id="grafico" style="max-width: 310px; height: auto; margin: 0px"></div>
</body>
</html>
<script type="text/javascript">

Highcharts.chart("grafico", {
  chart: {
    type: "areaspline"
  },
  title: {
    text: "Gráfico de Gastos e Receitas do Mês"
  },
  xAxis: {
    categories: <?php echo json_encode($arrayTitulos,JSON_UNESCAPED_UNICODE); ?>
  },
  yAxis: {
    title: {
      text: "R$"
    }
  },
  plotOptions: {
    line: {
      dataLabels: {
        enabled: true
      },
      enableMouseTracking: false
    }
  },
  series: [{
    name: "Receitas",
    data: <?php echo json_encode($arrayReceitas,JSON_NUMERIC_CHECK); ?>
  }, {
    name: "Gastos",
    data: <?php echo json_encode($arrayGastos,JSON_NUMERIC_CHECK); ?>
  }]
}).setOptions(Highcharts.theme);
</script>