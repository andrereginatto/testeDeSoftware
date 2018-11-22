<?php
session_start();
require_once './Classes/DAO_AUTOMATICOS.php';
$auto = new DAO_AUTOMATICOS();

if((isset($_GET['id'])|| isset($_POST['id'])) && (isset($_GET['parcela'])|| isset($_POST['parcela'])) && isset ($_SESSION['id'])){
   if(isset($_GET['id'])){
       $contador = $auto->verificaParcelas($_GET['parcela']);
       $contador2 = $auto->aUltimaParcela($_GET['parcela'],$_GET['data']);
       $dat = $_GET['data'];
       $data2 = explode("/", "$dat"); // fatia a string $dat em pedados, usando / como referência
       $data2 = $data2[2].'/'.$data2[1].'/'.$data2[0];
       $tabela = $auto->select_automatico_parcela_data($_GET['parcela'], $data2);
       $_POST['parcela']=$_GET['parcela'];
       $_POST['id']=$_GET['id'];
       $_POST['data'] = $_GET['data'];
   }
   if(isset($_POST['id'])){
       $contador = $auto->verificaParcelas($_POST['parcela']);
   }
  
   if ($contador['num_parcela'] > 1 || $tabela['REPETIR_INDEFINIDAMENTE'] == 1 || $tabela['TABELA'] == 'AUTOMATICOS'){
      
        if(isset($_POST['save']) && $_POST['comoFazer']==1){
            if (strrpos($_SESSION['pag'], 'gasto') == null){
                echo "<script> location = ('editar_receita_auto.php?id=".$_POST['id']."&tipo=1&data=".$_POST['data']."&p=".$_POST['parcela']."');
                           </script>!";
            }else{
                echo "<script> location = ('editar_gasto_auto.php?id=".$_POST['id']."&tipo=1&data=".$_POST['data']."&p=".$_POST['parcela']."');
                           </script>!";
            } 
       }else if(isset($_POST['save']) && $_POST['comoFazer']==2){
           if (strrpos($_SESSION['pag'], 'gasto') == null){
                echo "<script> location = ('editar_receita_auto.php?id=".$_POST['id']."&tipo=2&data=".$_POST['data']."&p=".$_POST['parcela']."');
                           </script>!";
            }else{
                echo "<script> location = ('editar_gasto_auto.php?id=".$_POST['id']."&tipo=2&data=".$_POST['data']."&p=".$_POST['parcela']."');
                           </script>!";
            }
       }else if (isset($_POST['save']) && $_POST['comoFazer']==3) {
            if (strrpos($_SESSION['pag'], 'gasto') == null){
                if($tabela['TABELA']=='AUTOMATICOS'){
                    echo "<script> location = ('editar_receita_auto.php?id=".$_POST['id']."&tipo=3&data=".$_POST['data']."&p=".$_POST['parcela']."');
                           </script>!";
                }else{
                    echo "<script> location = ('adicionar_receita.php?id=".$_POST['id']."');
                           </script>!";
                }
                
            }else{
                if($tabela['TABELA']=='AUTOMATICOS'){
                    echo "<script> location = ('editar_gasto_auto.php?id=".$_POST['id']."&tipo=3&data=".$_POST['data']."&p=".$_POST['parcela']."');
                           </script>!";
                }else{
                    echo "<script> location = ('adicionar_gasto.php?id=".$_POST['id']."');
                           </script>!";
                }
            } 
       }
    }else{
            if (strrpos($_SESSION['pag'], 'gasto') == null){
                echo "<script> location = ('adicionar_receita.php?id=".$_GET['id']."');
                           </script>!";
            }
            else{
                echo "<script> location = ('adicionar_gasto.php?id=".$_GET['id']."');
                           </script>!";
            }    
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
                   <?php
                    if (isset($_SESSION['nome']) && isset($_GET['id'])) {
                    if (isset($result)){                             
                        if ($result == 1){
                            echo '<center><strong id="return-banco" style="color: green; font-size:17px; font-family: Cabin;">Categoria alterada com Sucesso!</strong></center>';
                        }
                    }
                    ?>
                    <strong style="color: red; font-family: Cabin;font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin;font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($erro) && $erro==true){
                        echo $msg_erro;
                    }
                    ?>
                    </strong> 
                        <form method="post">
                        
                            
                            <label for="parcelas" style="margin-top: 3px;">O que deseja fazer com as parcelas ligadas a está?</label>
                            <select class="form-control" name="comoFazer" style="margin-top: -7px; margin-bottom: 3px; width: 100%;"
                               maxlength="20" id="parcelas">
                                <?php
                                
                                if($tabela['TABELA']=='AUTOMATICOS'){
                                    if($tabela['REPETIR_INDEFINIDAMENTE']!=1){
                                        echo '<option value="3">Atualizar apenas esta</option>';
                                    }
                                }else{
                                    echo '<option value="3">Atualizar apenas esta</option>';
                                }
                                
                                if($contador2['num_parcela'] > 0 || $tabela['REPETIR_INDEFINIDAMENTE']==1){
                                    echo '<option value="2">Atualizar a partir desta</option>';
                                }
                                
                                if($contador['num_parcela'] > 1){
                                    echo '<option value="1">Atualizar Todas</option>';
                                }
                                
                                ?>
                            </select>
                            
                        <div class="direita" style="float: right;">
                            
                            <button type="submit" name="save" style="float: right; margin-bottom: 7px;margin-top: 5px;" class="btn btn-small btn-info">
                           <span class="glyphicon glyphicon-pencil"> </span> <strong>Editar</strong></button>
                        
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
        <script src="validacoesForm/valida_gastos_receitas/js/validaForm.js"></script>
    </body>
</html>