<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);
require_once './Classes/DAO_CONTA.php';
$c = new DAO_CONTA();

if(isset($_POST['save'])&&isset($_GET['id'])){
    if(strlen($_POST['conta']) <= 20 && strlen($_POST['conta']) > 1){
        $result = $c->update_conta($_GET['id'], $_POST['conta']);
        if(isset($erro)){
            unset($erro);
        }
        $linha = $c ->select_by_id($_GET['id']);
    }else{
        $erro=true;
        $msg_erro = '- Por favor, a conta deve haver entre 2 e 20 caracteres.<br>';
    }
}else if(isset($_POST['delete'])){
    $result = $c->delete_conta($_GET['id']);
    header('Location: ./');
}else if(!isset($_POST['save'])&&isset($_GET['id'])){
    if(isset($_SESSION['id'])){
        $linha = $c ->select_by_id($_GET['id']);
        if (!isset($linha['ID'])){
            $_SESSION['erro'] = true;
            $_SESSION['msg_erro']='Conta não encontrada.';
            header('Location: '.$_SESSION['pag']);
        }
    }
}else if(isset ($_SESSION['id'])){
    $categorias = $c->list_full_contas_by_id($_SESSION['id']);
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
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Editar Conta</strong></div>
                    <?php if(isset($_SESSION['nome']) && !isset($_GET['id'])){
                    ?>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($_SESSION['erro']) && $_SESSION['erro']==true){
                        echo $_SESSION['msg_erro'];
                    }
                    if(isset($erro) && $erro==true){
                        echo $msg_erro;
                    }
                    
                    if(!isset($_GET['id']) && isset($_SESSION['erro'])){
                        unset($_SESSION['erro']);
                        unset($_SESSION['msg_0erro']);
                    }
                    ?>
                    </strong> 
                    <form method="get" onsubmit="return validaFormCadastro();" >
                        
                            
                            <label for="conta" style="margin-top: 3px;">Selecione a Conta:</label>
                            <select class="form-control" name="id" style="margin-top: -7px; margin-bottom: 3px; width: 100%;"
                                    maxlength="20" id="id" required="">
                            <?php
                               foreach ($categorias as $linhas){
                                   echo '<option value="'.$linhas['ID'].'">'.$linhas['NOME_CONTA'] .' </option>';
                               }
                            ?>
                            
                            </select>
                            
                        
                        <div class="direita" style="float: right;">
                            
                           <button type="submit" id="editar_conta" style="float: right; margin-bottom: 7px;margin-top: 5px;" class="btn btn-small btn-info">
                           <span class="glyphicon glyphicon-pencil"> </span> <strong>Editar</strong></button>
                        
                        </div>
                    </form>
                    <?php
                    } else if (isset($_SESSION['nome']) && isset($_GET['id'])) {
                    if (isset($result)){                             
                        if ($result == 1){
                            echo '<center><strong id="return-banco" style="color: green; font-size:17px; font-family: Cabin;">Conta alterada com Sucesso!</strong></center>';
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
                            <label for="conta" style="margin-top: 3px;">Conta:</label>
                            <input type="text" class="form-control" name="conta" style="margin-top: -7px; margin-bottom: 3px; width: 100%;"
                               maxlength="20" id="conta" placeholder="Nome da Conta" 
                                   <?php 
                                        if(isset($erro)){
                                            echo ' autofocus';
                                            if($erro){ 
                                                echo ' value="'.$_POST['conta'].'"'; 
                                            }
                                        }else{
                                            echo 'value="'.$linha['NOME_CONTA'].'"' ;
                                        }
                                    ?>>
                            
                            <button name="save" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px;" class="btn btn-small btn-info">
                           <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Salvar</strong></button>
                           
                           <button name="delete" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px; margin-right: 2px;" 
                                   class="btn btn-small btn-danger" onclick="return confirm('Você deseja realmente excluir?')" value="<?php echo $_GET['id'] ?>">
                           <span class="glyphicon glyphicon-remove"> </span> <strong>Excluir</strong></button>
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
    </body>
</html>