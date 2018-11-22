<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);
require_once './Classes/DAO_CATEGORIAS.php';
require_once './Classes/DAO_CONTA.php';
$c = new DAO_CATEGORIAS();
$co = new DAO_CONTA();
if(isset($_SESSION['id'])){
    $categorias = $c ->select_by_id_and_full($_SESSION['id']);
    $contasss = $co->list_full_contas_by_id($_SESSION['id']);
}

require_once './Classes/DAO_AUTOMATICOS.php';
$p = new DAO_AUTOMATICOS();
if(isset($_POST['save']) && !isset($_GET['id'])){
    require_once './validacoesForm/validacoes_automaticos/valida_automatico.php';
    if(!$erro){
        if($_POST['repeticao']==1){
            $p ->parcelas_personalizado($_POST['conta'], $_POST['valor'], $_POST['obs'], $_POST['dia_insert'], $_POST['qtd_parcelas'], 'E', 1, 'MONTH',$_POST['categoria'],FALSE);
        }else{
            if(isset($_POST['qtd_parcelas'])){
                $p ->parcelas_personalizado($_POST['conta'], $_POST['valor'], $_POST['obs'], $_POST['dia_insert'], $_POST['qtd_parcelas'], 'E', $_POST['vezes_repeat'], $_POST['periodo'],$_POST['categoria'],FALSE);
            }else{
                $p ->parcelas_personalizado($_POST['conta'], $_POST['valor'], $_POST['obs'], $_POST['dia_insert'], 0, 'E', $_POST['vezes_repeat'], $_POST['periodo'],$_POST['categoria'],TRUE);
            }
        }
        header("Location: index.php");
    }
    
}else if(!isset($_POST['save']) && isset($_GET['id'])){
    $automatico= $p->select_by_id($_GET['id']);
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
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Adicionar Receita</strong></div>
                    <?php if (isset($_SESSION['nome'])) { ?>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($erro) && $erro==true){
                        echo $msg_erro;
                    }
                    ?>
                    </strong> 
                    <form method="post" onsubmit="return validaFormCadastroAutomaticos();">
                        <input type="hidden" value="<?php 
if(isset($_POST['repeticao'])){
    if(isset($_POST['check_repetir'])){
        echo 'TRUE-';
    }else{
        echo 'FALSE-';
    }
    if($_POST['repeticao']==1){
        echo '1-MONTH';
    }else if ($_POST['repeticao']==2){
        echo $_POST['vezes_repeat'].'-'.$_POST['periodo'];
    }
}
?>" id="actions"/>
                            <div class="esquerda" style="float: left;">
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
                                               onclick="automatico(this.value)"> 
                                        Personalizado
                                    </label>
                                </div>
                                <div class="checkbox" id="repetir" style="margin-top: -7px; margin-bottom: 3px; position: initial; display: none;">
                                    <label><input type="checkbox" id="check_repetir" value="3" name="check_repetir" disabled=""
                                                  onclick="automatico(this.value)">Repetir indefinidamente</label>
                                </div>
                                
                                <label for="valor" style="margin-top: 3px;">Valor:</label>
                                <input type="number" class="form-control" name="valor" max="999999999999.99" min="0.01" id="valor" placeholder="50,99" step="0.01"
                                       aria-describedby="basic-addon1" value="<?php if(isset($_POST['valor'])){echo $_POST['valor'];} ?>">
                                
                                
                                <label for="obs" style="margin-top: -7px; margin-bottom: 3px;">Descrição:</label>
                                <input type="text" class="form-control" name="obs" style="margin-top: -7px; margin-bottom: 3px;"
                                       maxlength="60" id="obs" placeholder="Descrição" value="<?php if(isset($_POST['obs'])){echo $_POST['obs'];} ?>">
                                
                                <label for="categoria">Categoria: </label>
                                <select class="form-control" id="categoria" name="categoria" style="margin-top: -7px; margin-bottom: 3px;">
                                    <?php
                                        foreach ($categorias as $cat){
                                            if(isset($_POST['categoria'])){
                                                if($cat['CATEGORIA']==$_POST['categoria']){
                                                    echo '<option value="'.$cat['CATEGORIA'].'" selected> '.$cat['CATEGORIA'].'</option>';
                                                }else{
                                                    echo '<option value="'.$cat['CATEGORIA'].'"> '.$cat['CATEGORIA'].'</option>';
                                                }
                                            }else{
                                                echo '<option value="'.$cat['CATEGORIA'].'"> '.$cat['CATEGORIA'].'</option>';
                                            }
                                        }
                                    ?>
                                </select>

                            </div>
                            <div class="direita" style="float: right;">
                                
                                <label for="conta">Conta: </label>
                                <select class="form-control" id="conta" name="conta" style="margin-top: -7px; margin-bottom: 3px;">
                                    <?php
                                        foreach ($contasss as $linha_conta){
                                            if(isset($_POST['conta'])){
                                                if($linha_conta['ID']==$_POST['conta']){
                                                    echo '<option value="'.$linha_conta['ID'].'" selected> '.$linha_conta['NOME_CONTA'].'</option>';
                                                }else{
                                                    echo '<option value="'.$linha_conta['ID'].'"> '.$linha_conta['NOME_CONTA'].'</option>';
                                                }
                                            }else{
                                                echo '<option value="'.$linha_conta['ID'].'"> '.$linha_conta['NOME_CONTA'].'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                                
                                <label for="dia_insert" id="label_dia_insert" style="margin-top: 3px;">
                                    Data da primeira Parcela:
                                </label>
                                <input type="date"  class="form-control" name="dia_insert" value="<?php if(isset($_POST['dia_insert'])){echo $_POST['dia_insert'];} ?>"
                                       style="margin-top: -7px; margin-bottom: 3px;" id="dia_insert">
                                <div id="quantidade-de-parcelas">
                                <label for="qtd_parcelas" style="margin-top: 3px;">Número de Parcelas:</label>
                                <input type="number" class="form-control" name="qtd_parcelas" min="1" max="10"
                                       style="margin-top: -7px; margin-bottom: 3px;" id="qtd_parcelas" value="<?php if(isset($_POST['qtd_parcelas'])){echo $_POST['qtd_parcelas'];} ?>">
                                </div>
                                
                                <div class="esquerda-interna" id="qtd_repeat" style="display: none">
                                    <label for="vezes_repeat">Incluir a cada: </label>
                                    <select class="form-control" id="vezes_repeat" name="vezes_repeat" style="margin-top: -7px; margin-bottom: 3px;">
                                        <?php
                                    $i = 1;
                                    if(isset($_POST['vezes_repeat'])){
                                        while ($i <=10){
                                            if($i == $_POST['vezes_repeat']){
                                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                                            }else{
                                                echo '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                           $i++;
                                        }
                                    }else{
                                        while ($i <=10){
                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                            $i++;
                                        }
                                    }
                                    ?>
                                    </select>

                                </div>
                                <div class="direita-interna" id="select_repeat" style="display: none">
                                    <label style="color: white">.</label>
                                    <select class="form-control" id="periodo" name="periodo" style="margin-top: -7px; margin-bottom: 3px;">
                                        <option value="DAY"<?php if(isset($_POST['periodo'])){if($_POST['periodo']=='DAY'){echo ' selected';}} ?>>Dia</option>
                                        <option value="WEEK"<?php if(isset($_POST['periodo'])){if($_POST['periodo']=='WEEK'){echo ' selected';}} ?>>Semana</option>
                                        <option value="MONTH"<?php if(isset($_POST['periodo'])){if($_POST['periodo']=='MONTH'){echo ' selected';}}else echo ' selected'; ?>>Mês</option>
                                        <option value="YEAR"<?php if(isset($_POST['periodo'])){if($_POST['periodo']=='YEAR'){echo ' selected';}} ?>>Ano</option>
                                    </select>
                                </div>
                                
                                <button name="save" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px;" class="btn btn-small btn-info">
                                    <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Salvar</strong></button>
                            </div>
                        </form>
    <?php 
}else{ 
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
}
?>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            window.onload = function(){
            if(document.getElementById("actions")){
                var string = document.getElementById("actions").value;
                if (string !== ""){
                    var resultado = string.split("-");
                    if(resultado[0] == 'FALSE' && resultado[1]== '1' && resultado[2] == 'MONTH'){
                        document.getElementById("mensal").click();
                    }else{
                        document.getElementById("avancado").click();
                        if(resultado[0]=='TRUE'){
                            document.getElementById("check_repetir").click();
                        }
                    }
                }
            }
        }
        </script>
        <script src="validacoesForm/validacoes_automaticos/js/validaForm.js"></script>
    </body>
</html>