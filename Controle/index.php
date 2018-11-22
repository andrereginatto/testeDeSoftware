<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);

require_once './Classes/DAO_MOVIMENTOS.php';
require_once './Classes/DAO_CONTA.php';
require_once './Classes/DAO_CATEGORIAS.php';
$c = new DAO_CATEGORIAS();
$contass = new DAO_CONTA();
$p = new DAO_MOVIMENTOS();
$teste = new DAO_MOVIMENTOS();


if (isset($_SESSION['conta'])) {

    $categorias = $c->select_by_id_and_full($_SESSION['id']);
    $contasUser = $contass->list_full_contas_by_id($_SESSION['id']);

    if (!isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
        /*
         * PAGINAÇÃO
         * DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
         */
        date_default_timezone_set('America/Sao_Paulo');
        // CRIA UMA VARIAVEL E ARMAZENA A HORA ATUAL DO FUSO-HORÀRIO DEFINIDO (BRASÍLIA)
        $data_atual = mktime(0, 0, 0, date('m'), 1, date('Y'));
        if (!isset($_SESSION['mes'], $_SESSION['ano']) || $_SESSION['pag_mes'] !== $_SESSION['pag']) {
            $_SESSION['ano'] = date('Y', $data_atual);
            $_SESSION['mes'] = date('m', $data_atual);
            $_SESSION['pag_mes'] = $_SESSION['pag'];
        }
        require_once './ano_mes.php';

        if ($_SESSION['mes'] >= date('m', $data_atual) && ($_SESSION['ano'] == date('Y', $data_atual) || $_SESSION['ano'] > date('Y', $data_atual))) {
            $_SESSION['ano'] = date('Y', $data_atual);
            $_SESSION['mes'] = date('m', $data_atual);
            $_SESSION['pag_mes'] = $_SESSION['pag'];
            unset($_POST['acao']);
            include './ano_mes.php';
        }

        // verifica se precisa concatenar o 0 na frente
        if (strlen($_SESSION['mes']) < 2) {
            $_SESSION['mes'] = '0' . $_SESSION['mes'];
        }
        $movimentos = $p->lista_movimentos($_SESSION['id'], $_SESSION['ano'] . $_SESSION['mes'], null, null);

        /* GRAFICO */

        $MovimentosDAO = new DAO_MOVIMENTOS();

        function get_inicio_fim_semana($numero_semana = "", $ano = "") {
            /* soma o número de semanas em cima do início do ano 01/01/2013 */
            $semana_atual = strtotime('+' . $numero_semana . ' weeks', strtotime($ano . '0101'));

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
            $data_inicio_semana = strtotime('-' . $dia_semana . ' days', $semana_atual);

            /* Data início semana */
            $primeiro_dia_semana = date('Y-m-d', $data_inicio_semana);

            /* Soma 6 dias */
            $ultimo_dia_semana = date('Y-m-d', strtotime('+6 days', strtotime($primeiro_dia_semana)));

            /* retorna */
            return array($primeiro_dia_semana, $ultimo_dia_semana);
        }

        date_default_timezone_set('America/Sao_Paulo');

        $data_atual2 = date($_SESSION['ano'] . '/' . $_SESSION['mes'] . '/01');
        $ultimodia = date('Y/m/t', strtotime($data_atual2));
        $data_final = date('d/m/Y');

        $arrayGastos = array();
        $arrayReceitas = array();
        $arrayTitulos = array();

        $flag = true;
        while ($flag == true) {
            $numero_semana = date('W', strtotime($data_atual2)) - 1;
            $ano_atual = date('Y', strtotime($data_atual2));

            list($data_inicio, $data_final) = get_inicio_fim_semana($numero_semana, $ano_atual);

            $data_atual2 = date('Y/m/d', strtotime('+3 days', strtotime($data_final)));
            $data_final = date('Y/m/d', strtotime($data_final));
            $data_inicio = date('Y/m/d', strtotime($data_inicio));


            $grafico = $MovimentosDAO->selectGrafico($data_inicio, $data_final, $_SESSION['id']);
            $arrayGastos[] = str_replace('-', '', $grafico['gastos']);
            $arrayReceitas[] = $grafico['entradas'];
            $date_inicio_text = date_create($data_inicio);
            $date_fim_text = date_create($data_final);

            $date_inicio_text = date_format($date_inicio_text, "d/m/Y");
            $date_fim_text = date_format($date_fim_text, "d/m/Y");

            $arrayTitulos[] = $date_inicio_text . ' á ' . $date_fim_text;

            $data1 = $data_final;
            $data2 = $ultimodia;
            $d1 = strtotime($data1);
            $d2 = strtotime($data2);
            // verifica a diferença em segundos entre as duas datas e divide pelo número de segundos que um dia possui
            $subtracao = ($d2 - $d1) / 86400;

            if ($subtracao <= 0) {
                $flag = false;
            }
        }

        /* GRAFICO */
    } else {
        if (isset($_POST['gastos'])) {
            $_POST['gastos'] = 1;
        } else {
            $_POST['gastos'] = 0;
        }
        if (isset($_POST['receitas'])) {
            $_POST['receitas'] = 1;
        } else {
            $_POST['receitas'] = 0;
        }
        $movimentos = $p->lista_movimentosFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'], $_POST['menor_valor'], $_POST['maior_valor'], $_POST['obs'], $_POST['gastos'], $_POST['receitas'], $_POST['categoria'], $_POST['conta']);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- As 3 meta tags acima *devem* vir em primeiro lugar dentro do `head`; qualquer outro conteúdo deve vir *após* essas tags -->
        <title>Meu Controle</title>

        <!-- HTML5 shim e Respond.js para suporte no IE8 de elementos HTML5 e media queries -->
        <!-- ALERTA: Respond.js não funciona se você visualizar uma página file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body id="body">

        <div id="main">
            <div class="container" style="box-shadow: none; background-color: transparent; margin-top: 0px;">

<?php include_once 'html_usuario.php'; ?>

                <div class="container">
                    <div class="titulo_div">
                        <strong style="margin-left: 4px; margin-right: 4px;">
<?php
if (isset($mes_ano)) {
    echo 'Movimentos de ' . $mes_ano;
} elseif (isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
    echo 'Movimentos com Busca Personalizada ';
} else {
    echo 'Controle seu dinheiro AGORA';
}
?>
                        </strong>
                    </div>

<?php if (isset($_SESSION['id'])) { ?>
                        <div class="esquerda-index">
                            <div class="btn-group" role="group" id="fitros_data" style="margin-top: 3px;">
                                <button type="button" class="btn btn-primary" style="margin-top: 3px;"
                                        data-toggle="modal" data-target=".bootstrap-modal">
                                    <span class="glyphicon glyphicon-search"></span> Pesquisar
                                </button>
    <?php
    if (isset($_POST['dt_inicio']) || isset($_POST['dt_fim'])) {
        echo '<a href="index.php" class="btn btn-default" style="margin-top: 3px;"
                                   >Limpar Filtros</a>';
    }
    ?>
                            </div>
                        </div>

                        <div class="direita-index">
                            <div class="btn-group" role="group" id="botoes_filtro" style="margin-top: 3px; margin-bottom: 5px;">
                                <button class="btn btn-default" id="tudo" onclick="filtra(this.id);">Todos</button>
                                <button class="btn btn-success" id="entrada" onclick="filtra(this.id);">Receitas</button>
                                <button class="btn btn-danger" id="gasto" onclick="filtra(this.id);">Gastos</button>
                            </div>
                        </div>
                    <?php if (!isset($_POST['dt_inicio'],$_POST['dt_fim'])){ ?>
                        <ul class="nav nav-tabs" style="margin-bottom: -0px">
                            <li class="active" style="margin-top: 5px;" id="tab_tabela" onclick="tab(this.id);"><a href="#">Tabela</a></li>
                            <li style="margin-top: 5px;" id="tab_grafico" onclick="tab(this.id);"><a href="#">Gráfico</a></li>
                            <li style="margin-top: 5px;" id="tab_ambos" onclick="tab(this.id);"><a href="#">Ambos</a></li>
                        </ul>

                    <table class="table" style="margin-top:5px;" id="tabela">
                    <?php } else{ ?>
                        <table class="table" style="margin-top:50px;" id="tabela">
                    <?php } ?>
                            <tr class="cabecalho-table">
                                <td >Data</td>
                                <td class="obs">Descrição</td>
                                <td >Categoria</td>
                                <td >Valor</td>
                            </tr>
                            <tbody>
    <?php
    $gasto = 0;
    $entrada = 0;
    $total_gasto = 0;
    $total_entrada = 0;
    $conta_anterior = null;
    $conta_atual = null;

    foreach ($movimentos as $linha) {
        if ($linha['ENTRADAS_ID'] == null) {
            $cor = 'red';
            $tipo = 'gasto';
            $gasto++;
            $id = $tipo . $gasto;
            $total_gasto = $total_gasto + $linha['VALOR'];
        } else {
            $cor = 'green';
            $tipo = 'entrada';
            $entrada++;
            $id = $tipo . $entrada;
            $total_entrada = $total_entrada + $linha['VALOR'];
        }
        $linha['DATA_MOVIMENTO'] = new DateTime($linha['DATA_MOVIMENTO']);
        $data = $linha['DATA_MOVIMENTO']->format('d/m/Y');
        $conta_atual = $linha['CONTA_ID'];
        if ($conta_anterior == null) {
            $nome_conta = $contass->select_nome_conta($linha['CONTA_ID']);
            if (isset($_POST['dt_inicio'])) {
                $total_movimentos = $teste->lista_movimentosTotalFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'], $_POST['menor_valor'], $_POST['maior_valor'], $_POST['obs'], $_POST['gastos'], $_POST['receitas'], $_POST['categoria'], $linha['CONTA_ID']);
                echo '<tr class="nome_conta"><td>' . $nome_conta['NOME_CONTA'] . '</td><td class="obs"></td>';
                echo '<td> Gastos: <text style="color:red;">' . $total_movimentos['GASTOS'] . '</text></td>';
                echo '<td> Receitas: <text style="color:green;">' . $total_movimentos['ENTRADAS'] . '</text></td></tr>';
            } else {
                $total_movimentos = $teste->lista_TotalMovimentos($_SESSION['ano'] . $_SESSION['mes'], $linha['CONTA_ID']);
                echo '<tr class="nome_conta"><td>' . $nome_conta['NOME_CONTA'] . '</td><td class="obs"></td>';
                echo '<td> Gastos: <text style="color:red;">' . $total_movimentos['GASTOS'] . '</text></td>';
                echo '<td> Receitas: <text style="color:green;">' . $total_movimentos['ENTRADAS'] . '</text></td></tr>';
            }
        } else if ($conta_anterior !== $conta_atual) {
            $nome_conta = $contass->select_nome_conta($linha['CONTA_ID']);
            if (isset($_POST['dt_inicio'])) {
                $total_movimentos = $teste->lista_movimentosTotalFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'], $_POST['menor_valor'], $_POST['maior_valor'], $_POST['obs'], $_POST['gastos'], $_POST['receitas'], $_POST['categoria'], $linha['CONTA_ID']);
                echo '<tr class="nome_conta"><td>' . $nome_conta['NOME_CONTA'] . '</td><td class="obs"></td>';
                echo '<td> Gastos: <text style="color:red;">' . $total_movimentos['GASTOS'] . '</text></td>';
                echo '<td> Receitas: <text style="color:green;">' . $total_movimentos['ENTRADAS'] . '</text></td></tr>';
            } else {
                $total_movimentos = $teste->lista_TotalMovimentos($_SESSION['ano'] . $_SESSION['mes'], $linha['CONTA_ID']);
                echo '<tr class="nome_conta"><td>' . $nome_conta['NOME_CONTA'] . '</td><td class="obs"></td>';
                echo '<td> Gastos: <text style="color:red;">' . $total_movimentos['GASTOS'] . '</text></td>';
                echo '<td > Receitas: <text style="color:green;">' . $total_movimentos['ENTRADAS'] . '</text></td></tr>';
            }
        }
        $conta_anterior = $conta_atual;
        ?>
                                    <tr name="<?php echo $tipo ?>" id="<?php echo $id ?>" class="<?php echo $tipo ?>">
                                        <td><?php echo $data ?></td>
                                        <td class="obs"><?php echo $linha['NUMERO_PARCELA'] . $linha['OBS'] ?></td>
                                        <td><?php echo $linha['CATEGORIA'] ?></td>
                                        <td><strong style="color: <?php echo $cor; ?>;"><?php echo $linha['VALOR']; ?></strong></td>
                                    </tr>
        <?php
    }
    $verificaPonto = strpos($total_gasto, '.');
    if (!$verificaPonto) {
        $total_gasto = $total_gasto . ".00";
    } else {
        $rest = substr($total_gasto, $verificaPonto + 1, strlen($total_gasto));
        if (strlen($rest) < 2) {
            $total_gasto = $total_gasto . '0';
        }
    }
    $verificaPonto = strpos($total_entrada, '.');
    if (!$verificaPonto) {
        $total_entrada = $total_entrada . ".00";
    } else {
        $rest = substr($total_entrada, $verificaPonto + 1, strlen($total_entrada));
        if (strlen($rest) < 2) {
            $total_entrada = $total_entrada . '0';
        }
    }
    ?>
                            </tbody>
                        </table>
                        <!-- IMPORTANDO O GRAFICO -->

                        <div id="grafico" style="min-width: 255px; height: auto; margin: 0px;display: none;"></div>

                        <!-- IMPORTANDO O GRAFICO -->
                        <center>
                            <div class="esquerda" style="float: left">
                                <text class="total_gastos">Total de Gastos do Período: </text>
                                <strong class="total_gastos" style="color:red;">R$ <?php echo $total_gasto; ?></strong><br>
                                <text class="total_entradas">Total de Receitas do Período: </text>
                                <strong class="total_entradas" style="color:green;">R$ <?php echo $total_entrada; ?></strong>
                            </div>
    <?php if (!isset($_POST['dt_inicio'])) { ?>
                                <div class="direita" style="float: right; margin-bottom: 3px;">
                                    <form method="post" id="paginacao">
                                        <button class="btn btn-primary" onclick="paginacao(this.id)" type="button" 
                                                name="anterior" id="anterior">Mês Anterior</button>

                                        <input name="acao" id="acao" type="hidden" value="null">
        <?php
        if ($_SESSION['mes'] <> date('m', $data_atual) && ($_SESSION['ano'] == date('Y', $data_atual) || $_SESSION['ano'] < date('Y', $data_atual))) {
            ?>
                                            <button class="btn btn-primary" type="button" onclick="paginacao(this.id)" 
                                                    name="proximo" id="proximo">Próximo Mês</button>
                                        <?php } ?>
                                    </form>
                                </div>
                                            <?php } ?>
                        </center>
                    </div>
                    <!-- MODAL -->
                    <div class="modal fade bootstrap-modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="myModalLabel">Pesquisar Movimentos</h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <label for="dt_inicio" style="margin-top: 3px;">Data inicial:</label>
                                        <input type="date" class="form-control" name="dt_inicio" value="<?php if (isset($_POST['dt_inicio'])) {
                                            echo $_POST['dt_inicio'];
                                        } ?>"
                                               maxlength="40" id="dt_inicio" style="margin-top: -7px;">
                                        <label for="dt_fim" style="margin-top: 3px;">Data Final:</label>
                                        <input type="date" class="form-control" name="dt_fim" value="<?php if (isset($_POST['dt_fim'])) {
                                            echo $_POST['dt_fim'];
                                        } ?>"
                                               maxlength="40" id="dt_fim" style="margin-top: -7px;">

                                        <label for="menor_valor" style="margin-top: 3px;">Menor Valor:</label>
                                        <input type="number" class="form-control" name="menor_valor" placeholder="0,99" 
                                               id="menor_valor" style="margin-top: -7px;" step="0.01" value="<?php if (isset($_POST['menor_valor'])) {
                                            echo $_POST['menor_valor'];
                                        } ?>">
                                        <label for="maior_valor" style="margin-top: 3px;">Maior Valor:</label>
                                        <input type="number" class="form-control" name="maior_valor" placeholder="50,99"
                                               id="maior_valor" style="margin-top: -7px;" step="0.01" value="<?php if (isset($_POST['maior_valor'])) {
                                            echo $_POST['maior_valor'];
                                        } ?>">
                                        <label for="obs" style="margin-top: 3px;">Descrição:</label>
                                        <input type="text" class="form-control" name="obs" placeholder="Descrição"
                                               maxlength="60" id="obs" style="margin-top: -7px;" value="<?php if (isset($_POST['obs'])) {
                                            echo $_POST['obs'];
                                        } ?>">

                                        <label for="categoria" style="margin-top: 3px;">Categoria:</label>
                                        <select class="form-control" id="categoria" name="categoria" 
                                                style="margin-top: -7px; margin-bottom: 3px;">
                                            <option value=""> Todas Categorias</option>';
    <?php
    foreach ($categorias as $cat) {
        echo '<option value="' . $cat['CATEGORIA'] . '"> ' . $cat['CATEGORIA'] . '</option>';
    }
    ?>
                                        </select>
                                        <label for="conta" style="margin-top: 3px;">Conta:</label>
                                        <select class="form-control" id="categoria" name="conta" 
                                                style="margin-top: -7px; margin-bottom: 3px;">
                                            <option value=""> Todas as Contas</option>
    <?php
    foreach ($contasUser as $con) {
        echo '<option value="' . $con['ID'] . '"> ' . $con['NOME_CONTA'] . '</option>';
    }
    ?>
                                        </select>
                                        <label style="margin-top: 3px;">Mostrar:</label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="gastos" <?php if (isset($_POST['gastos'])) {
        if ($_POST['gastos'] == 1) {
            echo 'checked';
        }
    } else echo 'checked'; ?>>Gastos
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="receitas"<?php if (isset($_POST['receitas'])) {
        if ($_POST['receitas'] == 1) {
            echo 'checked';
        }
    } else echo 'checked'; ?>>Receitas
                                        </label>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Sair</button>
                                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Pesquisar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

    <?php
} else {
    if (isset($_SESSION['alert'])) {
        echo '<div class="alert alert-danger" style="margin-top:3px;">
                <strong>' . $_SESSION['alert'] . '</strong>
              </div>';
    }
}
?>

                        <?php if (isset($_SESSION['nome'])) { ?>
                    <div class="container">
                        <div class="titulo_div">
                            <strong style="margin-left: 4px; margin-right: 4px;">
                                Exportar relatório
                            </strong>
                        </div>
                        <form action="exportar.php" method="post" target="_blank">
                            <?php
                            if (isset($_POST['dt_inicio']) && isset($_POST['dt_fim'])) {
                                echo '<input type="hidden" name="dt_inicio" value="' . $_POST['dt_inicio'] . '">';
                                echo '<input type="hidden" name="dt_fim" value="' . $_POST['dt_fim'] . '">';
                                echo '<input type="hidden" name="menor_valor" value="' . $_POST['menor_valor'] . '">';
                                echo '<input type="hidden" name="maior_valor" value="' . $_POST['maior_valor'] . '">';
                                echo '<input type="hidden" name="obs" value="' . $_POST['obs'] . '">';
                                echo '<input type="hidden" name="categoria" value="' . $_POST['categoria'] . '">';
                                echo '<input type="hidden" name="conta" value="' . $_POST['conta'] . '">';
                                echo '<input type="hidden" id="gastos_diferentes" name="gastos" value="' . $_POST['gastos'] . '">';
                                echo '<input type="hidden" id="receitas_diferentes" name="receitas" value="' . $_POST['receitas'] . '">';
                            } else {
                                echo '<input type="hidden" id="gastos_diferentes" name="gastos" value="1">';
                                echo '<input type="hidden" id="receitas_diferentes" name="receitas" value="1">';
                            }
                            ?>
                            <center><button type="submit" class="btn btn-default" style="margin-top: 3px;"><span class="glyphicon glyphicon-export"></span> Exportar Relatório de Tabela</button></center><br>
                        </form>
                    </div>
<?php
} else {
    echo 'Comece Controlar seus gastos de onde estiver com muita facilidade. Basta cadastrar-se!';
}
if (isset($_SESSION['email_error'])) {
    unset($_SESSION['email_error']);
}
if (isset($_SESSION['alert'])) {
    unset($_SESSION['alert']);
}
?>
            </div>
        </div>
    </div>
<?php if (!isset($_POST['dt_ini'], $_POST['dt_fim'])) { ?>
        <script type="text/javascript">
            Highcharts.chart("grafico", {
                chart: {
                    type: "areaspline"
                },
                title: {
                    text: "Gráfico de Gastos e Receitas do Mês"
                },
                xAxis: {
                    categories: <?php echo json_encode($arrayTitulos, JSON_UNESCAPED_UNICODE); ?>
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
                        data: <?php echo json_encode($arrayReceitas, JSON_NUMERIC_CHECK); ?>
                    }, {
                        name: "Gastos",
                        data: <?php echo json_encode($arrayGastos, JSON_NUMERIC_CHECK); ?>
                    }]
            });
        </script>
<?php } ?>
</body>
</html>