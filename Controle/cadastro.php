<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);
require_once './Classes/DAO_PESSOA.php';
$p = new DAO_PESSOA();
if(isset($_POST['save'])){
   require_once './validacoesForm/validacoes_user/validaUser.php';
   if(!$erro){
       $result=$p->insert_pessoa($_POST['nome'], $_POST['sobrenome'], $_POST['fone'], $_POST['foto'], $_POST['genero'], 
                                 $_POST['email'], $_POST['senha'], $_POST['aniver']);
       header('Location: ./index.php');
   }
}

// CASO ESTEJA LOGADO NÃO EXIBE ESTÁ PÁGINA rediciona para a página INDEX
if(isset($_SESSION['nome'])){
    echo "<script> location = ('index.php');
                     </script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- As 3 meta tags acima *devem* vir em primeiro lugar dentro do `head`; qualquer outro conteúdo deve vir *após* essas tags -->
        <title>Meu Controle - Cadastre-se</title>

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
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Cadastre-se Agora</strong></div>
                    <center>
                    <?php 
                          if (isset($result)){                             
                              if ($result == 1){
                                 echo '<strong id="return-banco" style="color: green; font-size:17px; font-family: Cabin;">Cadastro realizado com Sucesso!</strong>';
                             }
                          }
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
                    <form method="post" onsubmit="return validaFormCadastro();">
                    <div class="esquerda" style="float: left;">
                        
                        <label for="nome" style="margin-top: 3px;">Nome:</label>
                        <input type="text" class="form-control" name="nome" style="margin-top: -7px;"
                               id="nome" placeholder="Nome" maxlength="40" required=""
                               <?php if(isset($erro)){
                                        if($input_erro==null || $input_erro=='nome'){
                                            echo ' autofocus';
                                        }
                                        if($erro){ 
                                            echo ' value="'.$_POST['nome'].'"'; 
                                        }
                                      }
                                ?>>
                        <!-- maxlength="40" -->
                        <label for="sobrenome" style="margin-top: 3px;">Sobrenome:</label>
                        <input type="text" class="form-control" name="sobrenome" style="margin-top: -7px;"
                               id="sobrenome" placeholder="Sobrenome" maxlength="40"
                               <?php 
                               if(isset($erro)){
                                  if($input_erro=='sobrenome'){
                                     echo ' autofocus';
                                  }
                                  if($erro){ 
                                     echo ' value="'.$_POST['sobrenome'].'"'; 
                                  } 
                               }
                               ?>>
                        
			<label for="fone" style="margin-top: 3px;">Telefone:</label>				
                        <input type="text" class="form-control fone" name="fone" style="margin-top: -7px;"
                               id="fone" onkeydown="masktel(this.value)" placeholder="(00) 00000-0000" required=""
                               <?php 
                               if(isset($erro)){
                                  if($input_erro=='fone'){
                                     echo ' autofocus';
                                  }
                                  if($erro){ 
                                     echo ' value="'.$_POST['fone'].'"'; 
                                  } 
                               }
                               ?>>
							   
                        <label for="genero" style="margin-top: 3px;">Gênero:</label><br>
                        <div class="borda-input" style="height: 35px;">
                            <label class="radio-inline" id="Masculino" style="margin-top:6px;margin-left: 8px;">
                                
                                <input type="radio" onclick="exibe_foto(this)" id="M" name="genero" value="M"
                                   <?php 
                                   if(isset($erro,$_POST['genero'])){
                                      if($input_erro=='genero'){
                                         echo ' autofocus';
                                      }
                                      if($erro && $_POST['genero']=='M'){ 
                                         echo ' checked'; 
                                      } 
                                   }
                                   ?>>Masculino
                            </label>

                            <label class="radio-inline" id="Feminino" style="margin-top:6px;">
                                
                                <input type="radio" onclick="exibe_foto(this,true)" id="F" name="genero" value="F"
                                   <?php 
                                   if(isset($erro,$_POST['genero'])){
                                      if($erro && $_POST['genero']=='F'){ 
                                         echo ' checked'; 
                                      } 
                                   }
                                   ?>>Feminino
                            </label><br>
                        </div>
                    </div>
                    <div class="direita" style="float: right;">
                        <label for="emailform" style="margin-top: 3px;">Email:</label>
                        <input type="text" class="form-control" name="email" style="margin-top: -7px;" required=""
                               maxlength="40" id="emailform" placeholder="Email" onkeyup="getVerificaEmail();"
                               <?php 
                               if(isset($erro)){
                                  if($input_erro=='email'){
                                     echo ' autofocus';
                                  }
                                  if($erro){ 
                                     echo ' value="'.$_POST['email'].'"'; 
                                  } 
                               }
                               ?>>
                        
                        <label for="senhaform" style="margin-top: 3px;">Senha:</label>
                        <input type="password" class="form-control" name="senha" style="margin-top: -7px;"
                               maxlength="30" id="senhaform" placeholder="Senha"  required=""
                               <?php 
                               if(isset($erro)){
                                  if($input_erro=='senha'){
                                     echo ' autofocus ';
                                  }
                                  if($erro){ 
                                     echo ' value="'.$_POST['senha'].'"'; 
                                  } 
                               }
                               ?>>
                        
                        <label for="aniver" style="margin-top: 3px;">Data Nascimento:</label>
                        <input type="date" class="form-control" name="aniver" style="margin-top: -7px;"
                               id="aniver" required=""
                               <?php 
                               if(isset($erro)){
                                  if($input_erro=='aniver'){
                                     echo ' autofocus';
                                  }
                                  if($erro){ 
                                     echo ' value="'.$_POST['aniver'].'"'; 
                                  } 
                               }
                               ?>>
                        
                        <label style="margin-top: 3px;">Foto:</label><br>
                        <div id="mulher" class="borda-input" style="display: none">
                            <center>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/m1.png" onclick="exibe_foto(this.id)" class="imgUsers" id="m1"  width="48" height="48"/>
                                </label>
                                <label class="radio-inline"  style="margin-left: -20px;">
                                    <img src="img/usuarios/m2.png" onclick="exibe_foto(this.id)" class="imgUsers" id="m2"width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/m3.png" onclick="exibe_foto(this.id)" class="imgUsers" id="m3" width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/m4.png" onclick="exibe_foto(this.id)" class="imgUsers" id="m4" width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/m5.png" onclick="exibe_foto(this.id)" class="imgUsers" id="m5" width="48" height="48"/>
                                </label>
                            </center>
                        </div>
                        <div id="homem" class="borda-input" style="display: none">
                            <center>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/h1.png" onclick="exibe_foto(this.id)" class="imgUsers" id="h1" width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/h2.png" onclick="exibe_foto(this.id)" class="imgUsers" id="h2" width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/h3.png" onclick="exibe_foto(this.id)" class="imgUsers" id="h3" width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img src="img/usuarios/h4.png" onclick="exibe_foto(this.id)" class="imgUsers" id="h4" width="48" height="48"/>
                                </label>
                                <label class="radio-inline" style="margin-left: -20px;">
                                    <img  src="img/usuarios/h5.png" onclick="exibe_foto(this.id)" class="imgUsers" id="h5" width="48" height="48"/>
                                </label>
                                </center>
                        </div>
                        
                        <input type="hidden" name="foto" id="foto" value="img/usuarios/desconhecido.png" />
                        <input type="hidden" id="saitrouxa" value="<?php if(isset($erro)){ echo $_POST['foto'];}?>">
                        
                        <button name="save" type="submit" style="float: right; margin-bottom: 10px;margin-top: 10px;" class="btn btn-small btn-info">
                           <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Cadastrar</strong></button>
                    </div>
                    </form>
                    
                </div>
            </div>
        </div>
        <script src="validacoesForm/validacoes_user/js/validaUser.js"></script>
        <script src="validacoesForm/validacoes_user/js/ajax.js"></script>
    </body>
</html>
<?php

if(isset($_SESSION['email_error'])){
    unset ($_SESSION['email_error']);
}
if(isset($_SESSION['alert'])){
    unset ($_SESSION['alert']);
}

?>