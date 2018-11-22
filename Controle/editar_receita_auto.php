<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);

require_once './Classes/DAO_AUTOMATICOS.php';
require_once './Classes/DAO_CONTA.php';
require_once './Classes/DAO_CATEGORIAS.php';
$p = new DAO_AUTOMATICOS();
$contasss = new DAO_CONTA();
$c = new DAO_CATEGORIAS();


if (isset($_SESSION['conta'])) {
    $categorias = $c->select_by_id_and_full($_SESSION['id']);
    $contasUser = $contasss->list_full_contas_by_id($_SESSION['id']);

    if (!isset($_GET['id']) && !isset($_GET['tipo']) && !isset($_GET['data'])) {

        if (!isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
            date_default_timezone_set('America/Sao_Paulo');
            // CRIA UMA VARIAVEL E ARMAZENA A HORA ATUAL DO FUSO-HORÀRIO DEFINIDO (BRASÍLIA)
            $data_atual = mktime(0, 0, 0, date('m'), 1, date('Y'));
            if (!isset($_SESSION['mes'], $_SESSION['ano']) || $_SESSION['pag_mes'] !== $_SESSION['pag']) {
                $_SESSION['ano'] = date('Y', $data_atual);
                $_SESSION['mes'] = date('m', $data_atual);
                $_SESSION['pag_mes'] = $_SESSION['pag'];
            }

            require_once './ano_mes.php';

            if ($_SESSION['mes'] <= date('m', $data_atual) && ($_SESSION['ano'] == date('Y', $data_atual) || $_SESSION['ano'] < date('Y', $data_atual))) {
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
            $gastos = $p->list_automaticos($_SESSION['id'], $_SESSION['ano'] . $_SESSION['mes'], 'E');
        } else {
            $gastos = $p->lista_AutomaticosFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'], $_POST['menor_valor'], $_POST['maior_valor'], $_POST['obs'], $_POST['categoria'], $_POST['conta'], 'E');
        }
    } else {
        $dat = $_GET['data'];
        $data = explode("/", "$dat"); // fatia a string $dat em pedados, usando / como referência
        $_GET['data'] = $data[2] . '/' . $data[1] . '/' . $data[0];
        $parcela = $p->select_automatico_parcela_data($_GET['p'], $_GET['data']);

        if ($parcela['ID'] == '') {
            $jaguara = 1;
        }

        if ($_GET['tipo'] == 1) {
            $qtd_parcela = $p->select_quantidade_automatico_parcela_data($_GET['p'], null);
            $data_primeira_parcela = $p->select_data_primeira_parcela($_GET['p'], null);
            $parcela['DATA'] = $data_primeira_parcela['DATA'];
        } else if ($_GET['tipo'] == 2) {
            $qtd_parcela = $p->select_quantidade_automatico_parcela_data($_GET['p'], $_GET['data']);
        } else if ($_GET['tipo'] == 3) {
            $qtd_parcela = $p->select_quantidade_automatico_parcela_data($_GET['p'], $_GET['data']);
        } else {
            $jaguara = 1;
        }

        if (!isset($jaguara)) {
            $_POST['tipo'] = $_GET['tipo'];
            $_POST['data_proc'] = $_GET['data'];
            $_POST['p'] = $_GET['p'];
        }
    }

    if (isset($_POST['save'])) {
        if (isset($_POST['tipo']) && isset($_POST['data_proc']) && isset($_POST['p'])) {
            if ($_POST['tipo'] == 1 || $_POST['tipo'] == 2 || $_POST['tipo'] == 3) {
                require_once './validacoesForm/validacoes_automaticos/valida_automatico.php';
                if (!$erro) {
                    if ($_POST['tipo'] == 1 || $_POST['tipo'] == 2) {
                        if ($_POST['tipo'] == 1) {
                            $p->delete_parcelas($_POST['p'], null);
                        } else if ($_POST['tipo'] == 2) {
                            $p->delete_parcelas($_POST['p'], $_POST['data_proc']);
                        }
                        if ($_POST['repeticao'] == 1) {
                            $p->parcelas_personalizado($_POST['conta'], $_POST['valor'], $_POST['obs'], $_POST['dia_insert'], $_POST['qtd_parcelas'], 'E', 1, 'MONTH', $_POST['categoria'], FALSE);
                        } else {
                            if (isset($_POST['qtd_parcelas'])) {
                                $p->parcelas_personalizado($_POST['conta'], $_POST['valor'], $_POST['obs'], $_POST['dia_insert'], $_POST['qtd_parcelas'], 'E', $_POST['vezes_repeat'], $_POST['periodo'], $_POST['categoria'], FALSE);
                            } else {
                                $p->parcelas_personalizado($_POST['conta'], $_POST['valor'], $_POST['obs'], $_POST['dia_insert'], 0, 'E', $_POST['vezes_repeat'], $_POST['periodo'], $_POST['categoria'], TRUE);
                            }
                        }
                        header("Location: index.php");
                    } else {
                        require_once './validacoesForm/validacoes_automaticos/valida_automatico.php';
                        if (!$erro) {
                            $returnUpdate = $p->updateAutomatico($_GET['id'], $_POST['dia_insert'], $_POST['valor'], $_POST['obs'], $_POST['categoria'], $_POST['conta']);
                            if ($returnUpdate == 1) {
                                header("Location: index.php");
                            }
                        }
                    }
                }
            } else {
                $jaguara = 1;
            }
        }
    } else if (isset($_POST['delete'])) {
        if ($_POST['tipo'] == 1) {
            $p->delete_parcelas($_POST['p'], null);
        } else if ($_POST['tipo'] == 2) {
            $p->delete_parcelas($_POST['p'], $_POST['data_proc']);
        } else if ($_POST['tipo'] == 3) {
            $p->deleteAutomatico($_GET['id']);
        }
        header("Location: index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
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
                                echo 'Receitas com Busca Personalizada ';
                            } else {
                                echo 'Editar Receita Futura';
                            }
                            ?>
                        </strong>
                    </div>
                    <?php if (isset($_SESSION['conta']) && !isset($_GET['id']) && !isset($_GET['tipo']) && !isset($_GET['data'])) { ?>
                        <div class="esquerda-index">
                            <div class="btn-group" role="group" id="fitros_data" style="margin-top: 3px;">
                                <button type="button" class="btn btn-primary" style="margin-top: 3px;"
                                        data-toggle="modal" data-target=".bootstrap-modal">
                                    <span class="glyphicon glyphicon-search"></span> Pesquisar
                                </button>
                                <?php
                                if (isset($_POST['dt_inicio']) || isset($_POST['dt_fim'])) {
                                    echo '<a href="editar_receita_auto.php" class="btn btn-default" style="margin-top: 3px;"
                                   >Limpar Filtros</a>';
                                }
                                ?>
                            </div>
                        </div>
                        <table class="table" style="margin-top: 50px;">
                            <thead>
                                <tr class="cabecalho-table">
                                    <th><text style="margin-left: 27px;">Data</text></th>
                                    <th class="obs">Descrição</th>
                                    <th>Categoria</th>
                                    <th>Valor R$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conta_anterior = null;
                                $conta_atual = null;
                                foreach ($gastos as $linha) {
                                    $conta_atual = $linha['CONTA_ID'];
                                    if ($conta_anterior == null) {
                                        $nome_conta = $contasss->select_nome_conta($linha['CONTA_ID']);
                                        echo '<tr class="nome_conta"><td colspan="4">' . $nome_conta['NOME_CONTA'] . '</td></tr>';
                                    } else if ($conta_anterior !== $conta_atual) {
                                        $nome_conta = $contasss->select_nome_conta($linha['CONTA_ID']);
                                        echo '<tr class="nome_conta"><td colspan="4">' . $nome_conta['NOME_CONTA'] . '</td></tr>';
                                    }
                                    $conta_anterior = $conta_atual;

                                    $linha['DATA'] = new DateTime($linha['DATA']);
                                    $data = $linha['DATA']->format('d/m/Y');
                                    ?>
                                    <tr class="gasto">
                                        <td><?php echo $data ?></td>
                                        <td class="obs"><a href="<?php echo 'update.php?id=' . $linha['ID'] . '&parcela=' . $linha['PARCELA'] . '&data=' . $data; ?>"><?php echo $linha['NUMERO_PARCELA'] . ' ' . $linha['OBS'] ?></a></td>
                                        <td><?php echo $linha['CATEGORIA'] ?></td>
                                        <td><strong style="color: <?php echo 'green'; ?>;"><?php echo $linha['VALOR'] ?></strong></td>
                                    </tr>

                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <center>
                            <?php if (!isset($_POST['dt_inicio'])) { ?>
                                <div class="direita" style="float: right; margin-bottom: 3px;">
                                    <form method="post" id="paginacao">
                                        <?php
                                        if ($_SESSION['mes'] <> date('m', $data_atual) && ($_SESSION['ano'] == date('Y', $data_atual) || $_SESSION['ano'] > date('Y', $data_atual))) {
                                            ?>
                                            <button class="btn btn-primary" onclick="paginacao(this.id)" type="button" 
                                                    name="anterior" id="anterior">Mês Anterior</button>
                                                <?php } ?>
                                        <input name="acao" id="acao" type="hidden" value="null">

                                        <button class="btn btn-primary" type="button" onclick="paginacao(this.id)" 
                                                name="proximo" id="proximo">Próximo Mês</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </center>
                    </div>
                    <div class="modal fade bootstrap-modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="myModalLabel">Pesquisar Gastos</h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <label for="dt_inicio" style="margin-top: 3px;">Data inicial:</label>
                                        <input type="date" class="form-control" name="dt_inicio" value="<?php
                            if (isset($_POST['dt_inicio'])) {
                                echo $_POST['dt_inicio'];
                            }
                            ?>"
                                               maxlength="40" id="dt_inicio" style="margin-top: -7px;">
                                        <label for="dt_fim" style="margin-top: 3px;">Data Final:</label>
                                        <input type="date" class="form-control" name="dt_fim" value="<?php
                                               if (isset($_POST['dt_fim'])) {
                                                   echo $_POST['dt_fim'];
                                               }
                                               ?>"
                                               maxlength="40" id="dt_fim" style="margin-top: -7px;">

                                        <label for="menor_valor" style="margin-top: 3px;">Menor Valor:</label>
                                        <input type="number" class="form-control" name="menor_valor" placeholder="0,99" 
                                               id="menor_valor" style="margin-top: -7px;" step="0.01" value="<?php
                                               if (isset($_POST['menor_valor'])) {
                                                   echo $_POST['menor_valor'];
                                               }
                                               ?>">
                                        <label for="maior_valor" style="margin-top: 3px;">Maior Valor:</label>
                                        <input type="number" class="form-control" name="maior_valor" placeholder="50,99"
                                               id="maior_valor" style="margin-top: -7px;" step="0.01" value="<?php
                                               if (isset($_POST['maior_valor'])) {
                                                   echo $_POST['maior_valor'];
                                               }
                                               ?>">
                                        <label for="obs" style="margin-top: 3px;">Descrição:</label>
                                        <input type="text" class="form-control" name="obs" placeholder="Descrição"
                                               maxlength="60" id="obs" style="margin-top: -7px;" value="<?php
                                            if (isset($_POST['obs'])) {
                                                echo $_POST['obs'];
                                            }
                                            ?>">
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
                    if (isset($_GET['id']) && isset($_GET['tipo']) && isset($_GET['data']) && isset($_GET['p'])) {
                        if ($_GET['id'] !== '' && $_GET['tipo'] !== '' && $_GET['data'] !== '' && $_GET['p'] !== '') {
                            if (!isset($jaguara)) {
                                ?>
                                <form method="post" onsubmit="return validaFormCadastroAutomaticos();">
                                    <div class="esquerda" style="float: left;">
                                        <input type="hidden" value="<?php
                                        if (isset($_POST['repeticao'])) {
                                            if (isset($_POST['check_repetir'])) {
                                                echo 'TRUE-';
                                            } else {
                                                echo 'FALSE-';
                                            }
                                            if ($_POST['repeticao'] == 1) {
                                                echo '1-MONTH';
                                            } else if ($_POST['repeticao'] == 2) {
                                                echo $_POST['vezes_repeat'] . '-' . $_POST['periodo'];
                                            }
                                        } else {
                                            if ($parcela['REPETIR_INDEFINIDAMENTE'] == TRUE) {
                                                echo 'TRUE-';
                                            } else {
                                                echo 'FALSE-';
                                            }
                                            echo $parcela['VEZES_REPEAT'] . '-' . $parcela['TIPO_REPEAT'];
                                        }
                                        ?>" id="actions"/>
                <?php if ($_GET['tipo'] != 3) { ?>
                                            <label style="margin-top: 3px;">Tipo do Gasto:</label>
                                            <div class="radio" style="margin-top: -7px; margin-bottom: 3px; position: initial">
                                                <label>
                                                    <input style="position: initial" type="radio" name="repeticao" id="mensal" value="1"
                                                           onclick="automatico(this.value)" checked=""> 
                                                    Parcelamento(Mensal)
                                                </label>

                                            </div>
                                            <div class="radio" style="margin-top: -7px; margin-bottom: 3px; position: initial">
                                                <label>
                                                    <input style="position: initial;" type="radio" name="repeticao" id="avancado" value="2"
                                                           onclick="automatico(this.value)" > 
                                                    Personalizado
                                                </label>
                                            </div>

                                            <div class="checkbox" id="repetir" style="margin-top: -7px; margin-bottom: 3px; position: initial; display: none;">
                                                <label><input type="checkbox" id="check_repetir" value="3" name="check_repetir" disabled=""
                                                              onclick="automatico(this.value)">Repetir indefinidamente</label>
                                            </div>                       
                <?php } ?>                           
                                        <label for="valor" style="margin-top: 3px;">Valor:</label>
                                        <div class="input-group" id="numero_negativo" style="margin-bottom: 3px;">
                                            <span class="input-group-addon" id="basic-addon1">
                                                <span class="glyphicon glyphicon-minus"> </span></span>
                                            <input type="number" class="form-control" name="valor" max="999999999999.99" min="0.01"
                                                   id="valor" placeholder="50,99" step="0.01" aria-describedby="basic-addon1" 
                                                   value="<?php if (isset($_POST['valor'])) {
                    echo $_POST['valor'];
                } else {
                    echo $parcela['VALOR'];
                } ?>">
                                        </div>

                                        <label for="obs" style="margin-top: 3px; margin-top: -7px;">Descrição:</label>
                                        <input type="text" class="form-control" name="obs" style="margin-top: -7px; margin-bottom: 3px;"
                                               maxlength="60" id="obs" placeholder="Descrição" value="<?php if (isset($_POST['obs'])) {
                                echo $_POST['obs'];
                            } else {
                                echo $parcela['OBS'];
                            } ?>">

                                        <label for="categoria">Categoria: </label>
                                        <select class="form-control" id="categoria" name="categoria" style="margin-top: -7px; margin-bottom: 3px;">
                <?php
                if (isset($_POST['categoria'])) {
                    $parcela['CATEGORIA'] = $_POST['categoria'];
                }

                foreach ($categorias as $cat) {
                    if ($parcela['CATEGORIA'] == $cat['CATEGORIA']) {
                        echo '<option selected value="' . $cat['CATEGORIA'] . '"> ' . $cat['CATEGORIA'] . '</option>';
                    } else {
                        echo '<option value="' . $cat['CATEGORIA'] . '"> ' . $cat['CATEGORIA'] . '</option>';
                    }
                }
                ?>
                                        </select>

                                    </div>
                                    <div class="direita" style="float: right;">

                                        <label for="conta">Conta: </label>
                                        <select class="form-control" id="conta" name="conta" style="margin-top: -7px; margin-bottom: 3px;">
                                            <?php
                                            if (isset($_POST['conta'])) {
                                                $parcela['CONTA_ID'] = $_POST['conta'];
                                            }

                                            foreach ($contasUser as $linha_conta) {
                                                if ($parcela['CONTA_ID'] == $linha_conta['ID']) {
                                                    echo '<option selected value="' . $linha_conta['ID'] . '"> ' . $linha_conta['NOME_CONTA'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $linha_conta['ID'] . '"> ' . $linha_conta['NOME_CONTA'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <label for="dia_insert" id="label_dia_insert" style="margin-top: 3px;">
                                        <?php
                                        if ($_GET['tipo'] == 3) {
                                            echo 'Data da Parcela:';
                                        } else {
                                            echo 'Data da primeira Parcela:';
                                        }
                                        ?>
                                        </label>
                                        <input type="date"  class="form-control" name="dia_insert" style="margin-top: -7px; margin-bottom: 3px;" id="dia_insert" 
                                               value="<?php if (isset($_POST['dia_insert'])) {
                            echo $_POST['dia_insert'];
                        } else {
                            echo $parcela['DATA'];
                        } ?>">

                <?php if ($_GET['tipo'] != 3) { ?>
                                            <div id="quantidade-de-parcelas">
                                                <label for="qtd_parcelas" style="margin-top: 3px;">Número de Parcelas:</label>
                                                <input type="number" class="form-control" name="qtd_parcelas" style="margin-top: -7px; margin-bottom: 3px;" id="qtd_parcelas" max="100000" min="1"
                                                       value="<?php if (isset($_POST['qtd_parcelas'])) {
                        echo $_POST['qtd_parcelas'];
                    } else {
                        echo $qtd_parcela['NUMERO_PARCELAS'];
                    } ?>">
                                            </div>
                    <?php
                    if (isset($_POST['vezes_repeat'])) {
                        $parcela['VEZES_REPEAT'] = $_POST['vezes_repeat'];
                    }
                    if (isset($_POST['periodo'])) {
                        $parcela['TIPO_REPEAT'] = $_POST['periodo'];
                    }
                    ?>
                                            <div class="esquerda-interna" id="qtd_repeat" style="display: none">
                                                <label for="vezes_repeat">Incluir a cada: </label>
                                                <select class="form-control" id="vezes_repeat" name="vezes_repeat" style="margin-top: -7px; margin-bottom: 3px;">
                                                    <option value="1" <?php if ($parcela['VEZES_REPEAT'] == 1) echo 'selected'; ?>>1</option>
                                                    <option value="2" <?php if ($parcela['VEZES_REPEAT'] == 2) echo 'selected'; ?>>2</option>
                                                    <option value="3" <?php if ($parcela['VEZES_REPEAT'] == 3) echo 'selected'; ?>>3</option>
                                                    <option value="4" <?php if ($parcela['VEZES_REPEAT'] == 4) echo 'selected'; ?>>4</option>
                                                    <option value="5" <?php if ($parcela['VEZES_REPEAT'] == 5) echo 'selected'; ?>>5</option>
                                                    <option value="6" <?php if ($parcela['VEZES_REPEAT'] == 6) echo 'selected'; ?>>6</option>
                                                    <option value="7" <?php if ($parcela['VEZES_REPEAT'] == 7) echo 'selected'; ?>>7</option>
                                                    <option value="8" <?php if ($parcela['VEZES_REPEAT'] == 8) echo 'selected'; ?>>8</option>
                                                    <option value="9" <?php if ($parcela['VEZES_REPEAT'] == 9) echo 'selected'; ?>>9</option>
                                                    <option value="10" <?php if ($parcela['VEZES_REPEAT'] == 10) echo 'selected'; ?>>10</option>
                                                </select>

                                            </div>
                                            <div class="direita-interna" id="select_repeat" style="display: none">
                                                <label style="color: white">.</label>
                                                <select class="form-control" id="periodo" name="periodo" style="margin-top: -7px; margin-bottom: 3px;">
                                                    <option value="DAY" <?php if ($parcela['TIPO_REPEAT'] == 'DAY') echo 'selected'; ?>>Dia</option>
                                                    <option value="WEEK" <?php if ($parcela['TIPO_REPEAT'] == 'WEEK') echo 'selected'; ?>>Semana</option>
                                                    <option value="MONTH" <?php if ($parcela['TIPO_REPEAT'] == 'MONTH') echo 'selected'; ?>>Mês</option>
                                                    <option value="YEAR" <?php if ($parcela['TIPO_REPEAT'] == 'YEAR') echo 'selected'; ?>>Ano</option>
                                                </select>
                                            </div>
                <?php } ?>
                                        <button name="save" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px;" class="btn btn-small btn-info">
                                            <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Salvar</strong></button>
                                        <button name="delete" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px; margin-right: 2px;" 
                                                class="btn btn-small btn-danger" onclick="return confirm('Você deseja realmente excluir?')" value="<?php echo $_GET['id'] ?>">
                                            <span class="glyphicon glyphicon-remove"> </span> <strong>Excluir</strong></button>
                                    </div>
                                </form>    
                <?php
            }
        }
    }
}
if (isset($_SESSION['email_error'])) {
    unset($_SESSION['email_error']);
}
if (isset($_SESSION['alert'])) {
    unset($_SESSION['alert']);
}

if (!isset($_SESSION['nome'])) {
    echo '<br>Comece Controlar seus gastos de onde estiver com muita facilidade. Basta cadastrar-se!';
}
?>
            </div>
        </div>
        <script type="text/javascript">
            window.onload = function() {
                if (document.getElementById("actions")) {
                    var string = document.getElementById("actions").value;
                    var resultado = string.split("-");
                    if (resultado[0] == 'FALSE' && resultado[1] == '1' && resultado[2] == 'MONTH') {
                        document.getElementById("mensal").click();
                    } else {
                        document.getElementById("avancado").click();
                        if (resultado[0] == 'TRUE') {
                            document.getElementById("check_repetir").click();
                        }
                    }
                }
            }
        </script>
        <script src="validacoesForm/validacoes_automaticos/js/validaForm.js"></script>
    </body>
</html>