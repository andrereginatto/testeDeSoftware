<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);
require_once './Classes/DAO_PESSOA.php';
$p = new DAO_PESSOA();
if(isset($_SESSION['nome'])){
    if(isset($_POST['save'])){
       require_once './validacoesForm/validacoes_user/validaUser.php';
       $usuario = $p->select_user($_SESSION['id']);
       if(!$erro){
           if(strlen($_POST['novasenha'])< 8 || strlen($_POST['novasenha'])> 30){
               $msg_erro = $msg_erro.'- A senha deve ter entre 8 e 30 caracteres PHP.<br>';
               $erro=true;
           }
           elseif ($usuario && password_verify($_POST["senha"], $usuario["SENHA"])) {
              $result=$p->update_senha($_POST['novasenha'],$_SESSION['id']); 
              unset($erro);
           }else{
               $msg_erro = $msg_erro.'- Senha atual inválida!<br>';
               $erro=true;
           }
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
        <title>Meu Controle - Alterar Senha</title>

        <!-- HTML5 shim e Respond.js para suporte no IE8 de elementos HTML5 e media queries -->
        <!-- ALERTA: Respond.js não funciona se você visualizar uma página file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body id="body">

        <div id="main">
            <div class="container" style="box-shadow: none; background-color: transparent; margin-top: 0px; margin-bottom: 10px;">
                
                <?php include_once 'html_usuario.php'; ?>
                
                <div class="container">
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Alterar Senha</strong></div>
                    <center>
                    <?php 
                          if (isset($result)){                             
                              if ($result == 1){
                                 echo '<strong id="return-banco" style="color: green; font-size:17px; font-family: Cabin;">Senha alterada com Sucesso!</strong>';
                             }
                          }
                    if(isset($_SESSION['nome'])){
                    ?>
                    </center>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($erro) && $erro){
                        echo $msg_erro;
                    }
                    ?>
                    </strong>
                    <form method="POST" onsubmit="return alterarSenha();">
                    <div class="esquerda" style="float: left;">
                        <label for="senhaform" style="margin-top: 3px;">Senha Atual:</label>
                        <input type="password" class="form-control" name="senha" style="margin-top: -7px;"
                               id="senhaform" placeholder="Senha Atual" maxlength="30" required=""
                               <?php
                                if(isset($erro)){
                                    if($erro==true){
                                        echo ' value="'.$_POST['senha'].'"';
                                    }
                                }
                               ?>>
                    </div>
                    <div class="direita" style="float: right;">
                        <!-- maxlength="40" -->
                        <label for="novasenha" style="margin-top: 3px;">Nova Senha:</label>
                        <input type="password" class="form-control" name="novasenha" style="margin-top: -7px;"
                               id="novasenha" placeholder="Nova Senha" maxlength="30" required=""
                               <?php
                                if(isset($erro)){
                                    if($erro==true){
                                      echo ' value="'.$_POST['novasenha'].'"';
                                    }  
                                }
                               ?>>
                        
                        <button name="save" type="submit" style="float: right; margin-bottom: 10px;margin-top: 10px; margin-left: 2px;" class="btn btn-small btn-info">
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
        <script src="validacoesForm/validacoes_user/js/validaUser.js"></script>
    </body>
</html>