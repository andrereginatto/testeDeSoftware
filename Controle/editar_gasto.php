<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);

require_once './Classes/DAO_GASTOS.php';
require_once './Classes/DAO_CONTA.php';
require_once './Classes/DAO_CATEGORIAS.php';
$p = new DAO_GASTOS();
$contass = new DAO_CONTA();
$c = new DAO_CATEGORIAS();

if (isset($_SESSION['conta'])) {
    
    $categorias = $c->select_by_id_and_full($_SESSION['id']);
    $contasUser = $contass->list_full_contas_by_id($_SESSION['id']);
    
    if (!isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
        date_default_timezone_set('America/Sao_Paulo');
        // CRIA UMA VARIAVEL E ARMAZENA A HORA ATUAL DO FUSO-HORÀRIO DEFINIDO (BRASÍLIA)
        $data_atual = mktime(0, 0, 0, date('m'), 1, date('Y'));
        if (!isset($_SESSION['mes'], $_SESSION['ano'])|| $_SESSION['pag_mes']!==$_SESSION['pag']) {
            $_SESSION['ano'] = date('Y', $data_atual);
            $_SESSION['mes'] = date('m', $data_atual);
            $_SESSION['pag_mes']=$_SESSION['pag'];
        }
        require_once './ano_mes.php';

        if($_SESSION['mes'] >= date('m', $data_atual)&&($_SESSION['ano'] == date('Y', $data_atual)||$_SESSION['ano'] > date('Y', $data_atual))){
            $_SESSION['ano'] = date('Y', $data_atual);
            $_SESSION['mes'] = date('m', $data_atual);
            $_SESSION['pag_mes'] = $_SESSION['pag'];
            unset($_POST['acao']);
            include  './ano_mes.php';
        }
        
        // verifica se precisa concatenar o 0 na frente
        if (strlen($_SESSION['mes']) < 2) {
            $_SESSION['mes'] = '0' . $_SESSION['mes'];
        }
        $gastos = $p->list_gasto($_SESSION['id'], $_SESSION['ano'] . $_SESSION['mes']);
    }else{
        $gastos = $p->lista_gastosFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'],$_POST['menor_valor'],$_POST['maior_valor'], $_POST['obs'],$_POST['categoria'],$_POST['conta']);
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
                                echo 'Gastos com Busca Personalizada ';
                            } else {
                                echo 'Controle seu dinheiro AGORA';
                            }
                       ?>
                        </strong>
                    </div>
<?php if (isset($_SESSION['conta'])) { ?>
                    <div class="esquerda-index">
                            <div class="btn-group" role="group" id="fitros_data" style="margin-top: 3px;">
                                <button type="button" class="btn btn-primary" style="margin-top: 3px;"
                                        data-toggle="modal" data-target=".bootstrap-modal">
                                    <span class="glyphicon glyphicon-search"></span> Pesquisar
                                </button>
                                        <?php
                                        if (isset($_POST['dt_inicio']) || isset($_POST['dt_fim'])) {
                                            echo '<a href="editar_gasto.php" class="btn btn-default" style="margin-top: 3px;"
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
        if($conta_anterior == null){
            $nome_conta = $contass ->select_nome_conta($linha['CONTA_ID']);
            echo '<tr class="nome_conta"><td colspan="4">'.$nome_conta['NOME_CONTA'].'</td></tr>';
        }else if($conta_anterior !== $conta_atual){
            $nome_conta = $contass ->select_nome_conta($linha['CONTA_ID']);
            echo '<tr class="nome_conta"><td colspan="4">'.$nome_conta['NOME_CONTA'].'</td></tr>';
        }
        $conta_anterior = $conta_atual;
                                    
        $linha['DATA'] = new DateTime($linha['DATA']);
        $data = $linha['DATA']->format('d/m/Y');
        ?>
                                    <tr class="gasto">
                                        <td><?php echo $data ?></td>
                                        <td class="obs"><a id="<?php echo $linha['OBS']; ?>" href="<?php echo 'update.php?id='.$linha['ID'].'&parcela='.$linha['PARCELA'].'&data='.$data;?>"><?php echo $linha['NUMERO_PARCELA'].$linha['OBS'] ?></a></td>
                                        <td><?php echo $linha['CATEGORIA'] ?></td>
                                        <td><strong style="color: <?php echo 'red'; ?>;"><?php echo '-'.$linha['VALOR'] ?></strong></td>
                                    </tr>

    <?php
    }
    ?>
                            </tbody>
                        </table>
                        <center>
                            <?php if (!isset($_POST['dt_inicio'])){ ?>
                            <div class="direita" style="float: right; margin-bottom: 3px;">
                                <form method="post" id="paginacao">
                                    <button class="btn btn-primary" onclick="paginacao(this.id)" type="button" 
                                            name="anterior" id="anterior">Mês Anterior</button>

                                    <input name="acao" id="acao" type="hidden" value="null">
                                    <?php
                                        if($_SESSION['mes'] <> date('m', $data_atual) && ($_SESSION['ano'] == date('Y', $data_atual)||$_SESSION['ano'] < date('Y', $data_atual))){
                                    ?>
                                    <button class="btn btn-primary" type="button" onclick="paginacao(this.id)" 
                                            name="proximo" id="proximo">Próximo Mês</button>
                                        <?php } ?>
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
                                        <input type="date" class="form-control" name="dt_inicio" value="<?php if (isset($_POST['dt_inicio'])){ echo $_POST['dt_inicio']; }?>"
                                               maxlength="40" id="dt_inicio" style="margin-top: -7px;">
                                        <label for="dt_fim" style="margin-top: 3px;">Data Final:</label>
                                        <input type="date" class="form-control" name="dt_fim" value="<?php if (isset($_POST['dt_fim'])){ echo $_POST['dt_fim']; }?>"
                                               maxlength="40" id="dt_fim" style="margin-top: -7px;">
                                        
                                        <label for="menor_valor" style="margin-top: 3px;">Menor Valor:</label>
                                        <input type="number" class="form-control" name="menor_valor" placeholder="0,99" 
                                               id="menor_valor" style="margin-top: -7px;" step="0.01" value="<?php if (isset($_POST['menor_valor'])){ echo $_POST['menor_valor']; }?>">
                                        <label for="maior_valor" style="margin-top: 3px;">Maior Valor:</label>
                                        <input type="number" class="form-control" name="maior_valor" placeholder="50,99"
                                               id="maior_valor" style="margin-top: -7px;" step="0.01" value="<?php if (isset($_POST['maior_valor'])){ echo $_POST['maior_valor']; }?>">
                                        <label for="obs" style="margin-top: 3px;">Descrição:</label>
                                        <input type="text" class="form-control" name="obs" placeholder="Descrição"
                                               maxlength="60" id="obs" style="margin-top: -7px;" value="<?php if (isset($_POST['obs'])){ echo $_POST['obs']; }?>">
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
                
    } else{
        if(isset($_SESSION['alert'])){
            echo '<div class="alert alert-danger" style="margin-top:3px;">
                    <strong>'.$_SESSION['alert'].'</strong>
                  </div>';
        }
        echo '<br>Comece Controlar seus gastos de onde estiver com muita facilidade. Basta cadastrar-se!';
    }
     
    if(isset($_SESSION['email_error'])){
        unset ($_SESSION['email_error']);
    }
    if(isset($_SESSION['alert'])){
        unset ($_SESSION['alert']);
    }?>
            </div>
        </div>
    </body>
</html>