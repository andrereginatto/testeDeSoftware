<?php
session_start();
$_SESSION['pag'] = basename($_SERVER['PHP_SELF']);
require_once './Classes/DAO_CATEGORIAS.php';
require_once './Classes/DAO_CONTA.php';
require_once './Classes/DAO_ENTRADAS.php';
$co = new DAO_CONTA();
$c = new DAO_CATEGORIAS();
$p = new DAO_ENTRADAS();

if(isset($_SESSION['id'])){
    $categorias = $c ->select_by_id_and_full($_SESSION['id']);
    $contasss = $co -> list_full_contas_by_id($_SESSION['id']);
}

date_default_timezone_set('America/Sao_Paulo');
$diadehoje = date('Y-m-d', time());

if (isset($_POST['save']) && !isset($_GET['id'])) {
    require_once './validacoesForm/valida_gastos_receitas/valida_form.php';
    if (!$erro){
        if($diadehoje < $_POST['data']){
            require_once './Classes/DAO_AUTOMATICOS.php';
            $automa = new DAO_AUTOMATICOS();
            $parcela= $automa->returnProximaParcela();
            $result = $automa->insertIntoAutomatico($_POST['conta'], $_POST['valor'], $_POST['data'], 'E', $parcela['PARCELA'], $_POST['obs'], $_POST['categoria']);
            unset($erro);
            header('Location: ./');
        }else{
            $result = $p->insert_entrada($_POST['valor'], $_POST['obs'], $_POST['conta'], $_POST['data'], $_POST['categoria']);
            header('Location: ./');
            unset($erro);
        }
    }
} else if (isset($_POST['save']) && isset($_GET['id'])) {
    require_once './validacoesForm/valida_gastos_receitas/valida_form.php';
    if (!$erro){
        require_once './Classes/DAO_AUTOMATICOS.php';
        $t = new DAO_AUTOMATICOS();
        /*$linha = $p->select_entrada_by_id($_GET['id']);
        $t->update_to_null($linha['PARCELA']);*/
        if($diadehoje < $_POST['data']){
            require_once './Classes/DAO_AUTOMATICOS.php';
            $automa = new DAO_AUTOMATICOS();
            $p->delete_entrada_by_id($_GET['id']);
            $parcela= $automa->returnProximaParcela();
            $result = $automa->insertIntoAutomatico($_POST['conta'], $_POST['valor'], $_POST['data'], 'E', $parcela['PARCELA'], $_POST['obs'], $_POST['categoria']);
            unset($erro);
            header('Location: ./');
        }else{
            $result = $p->update_entrada($_POST['valor'], $_POST['obs'], $_POST['conta'], $_POST['data'], $_GET['id'], $_POST['categoria']);
            unset($erro);
            header('Location: ./');
        }
        
    }
    
    if(!isset($linha['VALOR']) && !isset($erro)){
        $erro=true;
        $msg_erro = 'Receita não encontrada.';
    }
} else if (isset($_POST['delete'])) {
    $result = $p->delete_entrada_by_id($_POST['delete']);
    header('Location: ./');
} else if (!isset($_POST['save']) && isset($_GET['id'])) {
    $linha = $p->select_entrada_by_id($_GET['id']);
    if($linha['PARCELA']!== null){
        require_once './Classes/DAO_AUTOMATICOS.php';
        $t= new DAO_AUTOMATICOS();
        $qtd_parcelas_futuras = $t ->verifica_parcelas_futuras($linha['PARCELA']);
    }
    
    if(!isset($linha['VALOR']) && !isset($erro)){
        $erro=true;
        $msg_erro = 'Receita não encontrada.';
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
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">
                    <?php 
                    if(isset($_GET['id'])){
                        echo 'Editar ';  
                    }else{
                        echo 'Adicionar ';
                    }
                    ?>
                            Receita</strong></div>
                    <?php if (isset($_SESSION['nome']) && !isset($_GET['id'])){
                    ?>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($erro) && $erro==true){
                        echo $msg_erro;
                    }
                    ?>
                    </strong> 
                    <form method="post" onsubmit="return validaFormCadastro();">
                            <div class="esquerda" style="float: left;">
                                <label for="valor" style="margin-top: 3px;">Valor:</label>
                                <input type="number" class="form-control" name="valor" style="margin-top: -7px;"
                                       id="valor" placeholder="50,99" step="0.01" required="" max="999999999999.99" min="0.01"
                                <?php 
                                        if(isset($erro)){
                                            if($input_erro=='valor'){
                                                echo ' autofocus';
                                            }
                                            if($erro){ 
                                                echo ' value="'.$_POST['valor'].'"'; 
                                            }
                                        }?>>
                                
                                <label for="obs" style="margin-top: 3px;">Descrição:</label>
                                <input type="text" class="form-control" name="obs" style="margin-top: -7px;"
                                       maxlength="60" id="obs" placeholder="Descrição" required=""
                                <?php 
                                        if(isset($erro)){
                                            if($input_erro=='obs'){
                                                echo ' autofocus';
                                            }
                                            if($erro){ 
                                                echo ' value="'.$_POST['obs'].'"'; 
                                            }
                                        }?>>
                                
                                <label for="categoria">Categoria: </label>
                                <select class="form-control" id="categoria" name="categoria" 
                                        style="margin-top: -7px; margin-bottom: 3px;" required=""
                                <?php
                                    if(isset($erro)){
                                        if($input_erro=='categoria'){
                                            echo ' autofocus';
                                        }
                                    }
                                ?>>
                                <?php
                                    if(isset($erro)){
                                    
                                        foreach ($categorias as $cat){
                                            if(isset($erro)){
                                                if($erro){ 
                                                    if($_POST['categoria'] == $cat['CATEGORIA'] ){
                                                        echo '<option value="'.$cat['CATEGORIA'].'" selected > '.$cat['CATEGORIA'].'</option>';
                                                    }else{
                                                        echo '<option value="'.$cat['CATEGORIA'].'"> '.$cat['CATEGORIA'].'</option>';
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        foreach ($categorias as $cat){
                                            echo '<option value="'.$cat['CATEGORIA'].'"> '.$cat['CATEGORIA'].'</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="direita" style="float: right;">
                                
                                <label for="data" style="margin-top: 3px;">Data:</label>
                                <input type="date" class="form-control" name="data" style="margin-top: -7px; margin-bottom: 3px;" required=""
                                       id="data" 
                                    <?php 
                                        if(isset($erro)){
                                            if($input_erro=='data'){
                                                echo ' autofocus';
                                            }
                                            if($erro){ 
                                                echo ' value="'.$_POST['data'].'"'; 
                                            }
                                        }
                                    ?>>
                                
                                <label for="conta">Conta: </label>
                                <select class="form-control" id="conta" name="conta"  required=""
                                        style="margin-top: -7px; margin-bottom: 3px;" 
                                     <?php if(isset($erro)){
                                        if($input_erro=='conta'){
                                            echo ' autofocus';
                                        }
                                }?>>
                                <?php
                                    if(isset($erro)){                                  
                                        foreach ($contasss as $linha_conta){
                                            if(isset($erro)){
                                                if($erro){ 
                                                    if($_POST['conta'] == $linha_conta['ID'] ){
                                                        echo '<option value="'.$linha_conta['ID'].'" selected > '.$linha_conta['NOME_CONTA'].'</option>';
                                                    }else{
                                                        echo '<option value="'.$linha_conta['ID'].'"> '.$linha_conta['NOME_CONTA'].'</option>';
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        foreach ($contasss as $linha_conta){
                                            echo '<option value="'.$linha_conta['ID'].'"> '.$linha_conta['NOME_CONTA'].'</option>';
                                        }
                                    }
                                ?>
                                </select>
                                
                                <button name="save" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px;" class="btn btn-small btn-info">
                                    <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Salvar</strong></button>
                            </div>
                        </form>
                    <?php
                    } else if (isset($_SESSION['nome']) && isset($_GET['id'])) {
                        if(isset($linha)){
                            $linha['DATA'] = date("Y-m-d", strtotime($linha['DATA']));
                        }
                        if (isset($result)){                             
                            if ($result == 1){
                                echo '<center><strong id="return-banco" style="color: green; font-size:17px; font-family: Cabin;">Receita alterada com Sucesso!</strong></center>';
                            }
                        }
                    ?>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="mensagem_erro"></strong>
                    <strong style="color: red; font-family: Cabin; font-size: 14px;" id="erro_php">
                    <?php
                    if(isset($erro) && $erro==true){
                        echo $msg_erro;
                    }
                    ?>
                    </strong> 
                            <?php if(isset($linha['VALOR']) || isset($_POST['valor'])){?>
                            <form method="post" onsubmit="return validaFormCadastro();">
                                <div class="esquerda" style="float: left;">
                                    <label for="valor" style="margin-top: 3px;">Valor:</label>
                                    <input type="number" class="form-control" name="valor" style="margin-top: -7px; margin-bottom: 3px;"
                                           id="valor" placeholder="50,99" step="0.01" required="" max="999999999999.99" min="0.01"
                                                   <?php 
                                                    if(isset($erro)){
                                                        if($input_erro==null || $input_erro=='valor'){
                                                            echo ' autofocus';
                                                        }
                                                        if($erro){ 
                                                            echo ' value="'.$_POST['valor'].'"'; 
                                                        }
                                                    }else{
                                                        echo 'value="'.$linha['VALOR'].'"' ;
                                                    }
                                                   ?>>
                                    
                                    <label for="obs" style="margin-top: 3px;">Descrição:</label>
                                    <input type="text" class="form-control" name="obs" style="margin-top: -7px; margin-bottom: 3px;"
                                           maxlength="60" id="obs" placeholder="Descrição" required=""
                                           <?php 
                                                    if(isset($erro)){
                                                        if($input_erro=='obs'){
                                                            echo ' autofocus';
                                                        }
                                                        if($erro){ 
                                                            echo ' value="'.$_POST['obs'].'"'; 
                                                        }
                                                    }else{
                                                        echo 'value="'.$linha['OBS'].'"' ;
                                                    }
                                                   ?>>
                                    
                                    <label for="categoria">Categoria: </label>
                                    <select class="form-control" id="categoria" name="categoria" style="margin-top: -7px; margin-bottom: 3px;" required=""
                                            <?php 
                                                if(isset($erro)){
                                                    if($input_erro=='categoria'){
                                                        echo ' autofocus';
                                                    }
                                                }?>>
                                            <?php
                                                foreach ($categorias as $cat){
                                                    if(isset($erro)){
                                                        if($erro){ 
                                                            if($_POST['categoria'] == $cat['CATEGORIA'] ){
                                                                echo '<option value="'.$cat['CATEGORIA'].'" selected > '.$cat['CATEGORIA'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$cat['CATEGORIA'].'"> '.$cat['CATEGORIA'].'</option>';
                                                            }
                                                        }
                                                    }else{
                                                       if($linha['CATEGORIA'] == $cat['CATEGORIA'] ){
                                                                echo '<option value="'.$cat['CATEGORIA'].'" selected > '.$cat['CATEGORIA'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$cat['CATEGORIA'].'"> '.$cat['CATEGORIA'].'</option>';
                                                        }
                                                    }
                                                    
                                                }
                                            ?>
                                    </select>
                                    
                                </div>
                                <div class="direita" style="float: right;">
                                    <label for="data" style="margin-top: 3px;">Data:</label>
                                    <input type="date" class="form-control" name="data" style="margin-top: -7px;margin-bottom: 3px;" id="data" required=""
                                           <?php 
                                                    if(isset($erro)){
                                                        if($input_erro=='data'){
                                                            echo ' autofocus';
                                                        }
                                                        if($erro){ 
                                                            echo ' value="'.$_POST['data'].'"'; 
                                                        }
                                                    }else{
                                                        echo 'value="'.$linha['DATA'].'"' ;
                                                    }
                                                   ?>>
                                    <label for="conta">Conta: </label>
                                    <select class="form-control" id="conta" name="conta" style="margin-top: -7px; margin-bottom: 3px;" required=""
                                            <?php 
                                                if(isset($erro)){
                                                    if($input_erro=='conta'){
                                                        echo ' autofocus';
                                                    }
                                                }?>>
                                            <?php
                                                foreach ($contasss as $linha_conta){
                                                    if(isset($erro)){
                                                        if($erro){ 
                                                            if($_POST['conta'] == $linha_conta['ID'] ){
                                                                echo '<option value="'.$linha_conta['ID'].'" selected > '.$linha_conta['NOME_CONTA'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$linha_conta['ID'].'"> '.$linha_conta['NOME_CONTA'].'</option>';
                                                            }
                                                        }
                                                    }else{
                                                       if($linha['CONTA_ID'] == $linha_conta['ID'] ){
                                                                echo '<option value="'.$linha_conta['ID'].'" selected > '.$linha_conta['NOME_CONTA'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$linha_conta['ID'].'"> '.$linha_conta['NOME_CONTA'].'</option>';
                                                        }
                                                    }
                                                    
                                                }
                                            ?>
                                    </select>

                                    <button name="save" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px;" class="btn btn-small btn-info">
                                        <span class="glyphicon glyphicon-floppy-save"> </span> <strong>Salvar</strong></button>

                                    <button name="delete" type="submit" style="float: right; margin-bottom: 7px;margin-top: 13px; margin-right: 2px;" 
                                            class="btn btn-small btn-danger" onclick="return confirm('Você deseja realmente excluir?')" value="<?php echo $_GET['id'] ?>">
                                        <span class="glyphicon glyphicon-remove"> </span> <strong>Excluir</strong></button>
                                </div>
                            </form>
                            <?php 
                             }
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
        <script src="validacoesForm/valida_gastos_receitas/js/validaForm.js"></script>
    </body>
</html>