<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);
require_once './Classes/DAO_CONTA.php';
$c = new DAO_CONTA();

if(isset($_POST['save']) && isset($_POST['conta'])){
    if(strlen($_POST['conta']) <= 20 && strlen($_POST['conta']) > 1){
        $result= $c->insert_conta($_SESSION['id'],$_SESSION['nome'], $_POST['conta']);
        if(isset($erro)){
            unset($erro);
        }
        header('Location: ./editar_conta.php');
    }else{
        $erro=true;
        $msg_erro = '- Por favor, a conta deve haver entre 2 e 20 caracteres.<br>';
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
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Adicionar Conta</strong></div>
                    <?php if(isset($_SESSION['nome']) && !isset($_GET['id'])){
                    ?>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($erro) && $erro==true){
                        echo $msg_erro;
                    }
                    ?>
                    </strong> 
                    <?php 
                    if ($_SESSION['conta']=="'-1'"){?>
                    <strong style="color: #03A9F4; font-family: Cabin; font-size: 16px;">
                            Crie agora uma conta para poder começar a controlar seus gastos!
                    </strong><br><br>
                    <?php } ?>
                    
                    <form method="post">
                                                   
                            <label for="conta" style="margin-top: 3px;">Nome da Conta:</label>
                            <input type="text" class="form-control" name="conta" style="margin-top: -7px; margin-bottom: 3px;"
                                   maxlength="20" id="conta" placeholder="Nome da Conta" required="">

                           <button name="save" type="submit" style="float: right; margin-bottom: 7px;margin-top: 5px;" class="btn btn-small btn-info">
                           <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Salvar</strong></button>

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
                    }?>
                </div>
            </div>
        </div>
    </body>
</html>