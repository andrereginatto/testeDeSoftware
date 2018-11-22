<?php
// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');
// CRIA UMA VARIAVEL E ARMAZENA A HORA ATUAL DO FUSO-HORÀRIO DEFINIDO (BRASÍLIA)
    $horaAtual = date('H', time());
    $diaAtual = date('Y-m-d', time());
    if($horaAtual >= 4 && $horaAtual <= 12){
        $msg='Bom dia';
    }elseif ($horaAtual >= 13 && $horaAtual <= 18) {
        $msg='Boa tarde';
    }else{
        $msg='Boa noite';
    }
    
    // VERIFICA SE O TEMPO DA SESSAO EXPIROU
    if(isset($_SESSION["sessiontime"])){
        
        if($_SESSION['pag'] != 'adicionar_conta.php' && $_SESSION['pag'] != 'alterar_usuario.php' && $_SESSION['pag'] != 'alterar_senha.php'){
            if ($_SESSION['conta']=="'-1'"){
                echo "<script> location = ('adicionar_conta.php');
                      </script>!";
            }
        }
        
        // EXECUTA A PROC QUE INSERE OS DADOS AUTOMATICOS
        require_once './Classes/DAO_AUTOMATICOS.php';
        $proc = new DAO_AUTOMATICOS();
        
        require_once 'Classes/DAO_CONTA.php';
        $con = new DAO_CONTA();
        
        $proc ->insere_automaticos($_SESSION['id'], $diaAtual);
        
        require_once 'Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $saldo = $con->select_saldo($_SESSION['id']);
        $_SESSION['saldo']=$saldo['SALDO'];
        $contas = $con->select_contas($_SESSION['id']);
        $_SESSION['conta'] = $contas['ids'];
        
        
        
        $usuario = $p->select_user($_SESSION['id']);
        $sexo = $usuario['SEXO'];
        $_SESSION['nome'] = $usuario['NOME'];
        $_SESSION['img'] = $usuario['IMG'];
        
        
        if ($_SESSION["sessiontime"] < time()) {
            $_SESSION['alert']="Sua sessão expirou!";
            echo "<script> location = ('login/logout.php');
                  </script>!";
        } else {
            $_SESSION["sessiontime"] = time() + 10480;
        }
        
        if($sexo =='F'){
            $sexo='a';
        }
        else{
            $sexo='o';
        }
    }
    
?>
<!--INICIO INCLUSAO CSS -->
    <!-- FONTE DA TABELA DE GASTOS -->
    <link href="https://fonts.googleapis.com/css?family=Cabin:700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Tajawal" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tema opcional -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css" >
    <link href="css/inicial.css" rel="stylesheet">
<!--FINAL INCLUSAO CSS -->

<!--INICIO INCLUSAO JAVASCRIPT -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <!-- Inclui todos os plugins compilados (abaixo), ou inclua arquivos separadados se necessário -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/navbar.js"></script>
<!--FINAL INCLUSAO JAVASCRIPT -->

                <nav>
                    <div class="nav-xbootstrap" id="menu" style="z-index: 100;">
                        <ul>
                            <li><a href="./">Início</a></li>
                            <li><a href="#" id="contas" onclick="navbar(this.id,document.documentElement.clientWidth)">Contas<span id="ico_contas" class="glyphicon glyphicon-chevron-down iconsize"></span></a>
                                <ul class="dropdown" id="contas1" style="z-index: 100;">
                                    <li><a href="adicionar_conta.php">Adicionar Conta</a></li>
                                    <li><a href="editar_conta.php">Editar Conta</a></li>
                                </ul>
                            </li>
                            <li><a href="#" id="categorias" onclick="navbar(this.id,document.documentElement.clientWidth)">Categorias<span id="ico_categorias"  class="glyphicon glyphicon-chevron-down iconsize"></span></a>
                                <ul class="dropdown" id="categorias1" style="z-index: 100;">
                                    <li><a href="adicionar_categoria.php">Adicionar Categoria</a></li>
                                    <li><a href="editar_categoria.php">Editar Categoria</a></li>
                                </ul>
                            </li>
                            <li><a href="#" id="gastos" onclick="navbar(this.id,document.documentElement.clientWidth)">Gastos<span id="ico_gastos" class="glyphicon glyphicon-chevron-down iconsize"></span></a>
                                <ul class="dropdown" id="gastos1" style="z-index: 100;">
                                    <li><a href="adicionar_gasto.php">Adicionar Gasto</a></li>
                                    <li><a href="editar_gasto.php">Editar Gasto</a></li>
                                </ul>
                            </li>
                            <li><a href="#" id="entradas" onclick="navbar(this.id,document.documentElement.clientWidth)">Receitas<span id="ico_entradas"  class="glyphicon glyphicon-chevron-down iconsize"></span></a>
                                <ul class="dropdown" id="entradas1" style="z-index: 100;">
                                    <li><a href="adicionar_receita.php">Adicionar Receita</a></li>
                                    <li><a href="editar_receita.php">Editar Receita</a></li>
                                </ul>
                            </li>
                            <li><a href="#" id="automatico" onclick="navbar(this.id,document.documentElement.clientWidth)">Parcelamento<span id="ico_automatico" class="glyphicon glyphicon-chevron-down iconsize"></span></a>
                                <ul class="dropdown" id="automatico1" style="z-index: 100;">
                                    <li><a href="add_gasto_auto.php">Adicionar Gasto</a></li>
                                    <li><a href="editar_gasto_auto.php">Editar Gasto Futuro</a></li>
                                    <li><a href="add_receita_auto.php">Adicionar Receita</a></li>
                                    <li><a href="editar_receita_auto.php">Editar Receita Futura</a></li>
                                </ul>
                            </li>
                            <div style="float: right;" id="logo">
                                <a href="./" id="titulo" style="text-decoration: none;color: white; font-size: 20px;">
                                    Meu Controle
                                <img style=" margin-bottom: 2px; margin-top: 2px;" class="img-fluid" src="img/bau.png" 
                                    height="50px"/></a>
                            </div>
                        </ul>
                        
                    </div>
                    <div class="nav-bg-xbootstrap" style="z-index: 1000;">
                        <div class="navbar-xbootstrap" > <span></span> <span></span> <span></span> </div>
                        <a href="./" class="title-mobile" style="text-decoration: none; color: white; position: absolute;">Meu Controle</a>
                    </div>
                </nav>
<?php
if(isset($_SESSION['nome'])){
    if($_SESSION['saldo'] >= 0) $cor='green'; else $cor='red';
    echo '<div class="container" id="dados-usuario">
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Bem-vind'.$sexo.'</strong></div>
                    <div id="usuario-on">
                            <img style="margin-bottom: 7px; margin-top: 0px;" class="img-fluid" src="'.$_SESSION['img'].'" 
                                 height="50px"/>
                               <p style="margin-top:-43px; margin-left:50px;"><strong id="nome_user">'.$msg.', '.$_SESSION['nome'].'  <a href="alterar_usuario.php" id="editar_user" title="Editar"><span class="glyphicon glyphicon-edit" style="color:#03A9F4" aria-hidden="true"></span></a> </strong><p>
                                <p style="margin-top: -10px; margin-left:50px;"><strong>Saldo Atual:</strong><strong style="color: '.$cor.';font-family: Cabin, sans-serif;font-size:14px;"> R$ '.$_SESSION['saldo'].'</strong></p>
                    </div>
                    <a href="login/logout.php" id="sair" style="margin-top: 5px; margin-bottom: 2px; margin-left:5px; float:right;" class="btn btn-danger btn-sm" type="submit"><strong>Sair</strong></a>
                        
                </div>';
}else{
    if(isset($_SESSION['email_error'])){
           echo '<div class="container" id="dados-usuario">
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Identifique-se</strong></div>
                    <div id="usuario-on">
                        <center>
                            <img style="margin-bottom: 2px; margin-top: 2px;" class="img-fluid" src="img/usuarios/desconhecido.png" 
                                 height="50px"/>
                            <strong id="nome_user">Bem-vindo(a), 
                                <a href="#" onclick="document.getElementById('."'email22'".').focus();" style="color: #03A9F4; text-decoration: none;">entre</a>
                                ou <a href="cadastro.php" style="color: #03A9F4; text-decoration: none;" id="cadastroAgora"> cadastre-se</a>.</strong>
                        </center>
                    </div>
                    <form class="form-inline form-group-sm" id="login" method="post" action="login/logar.php">
                        <center>
                            <input type="text" id="email22" style="margin-top: 2px;" class="form-control" placeholder="Email" name="email" value="'.$_SESSION['email_error'].'">
                            <input type="password" style="margin-top: 2px;" class="form-control" placeholder="Senha" name="senha">
                            <button id="btn_entrar" style="margin-top: 2px; margin-bottom: 2px;" class="btn btn-default btn-sm" type="submit">Entrar</button>
                        </center>
                    </form>
                </div>';
    }else{
           echo '<div class="container" id="dados-usuario">
                    <div class="titulo_div"><strong style="margin-left: 4px; margin-right: 4px;">Identifique-se</strong></div>
                    <div id="usuario-on">
                        <center>
                            <img style="margin-bottom: 2px; margin-top: 2px;" class="img-fluid" src="img/usuarios/desconhecido.png" 
                                 height="50px"/>
                            <strong id="nome_user">Bem-vindo(a), 
                                <a href="#" onclick="document.getElementById('."'email22'".').focus();" style="color: #03A9F4; text-decoration: none;">entre</a>
                                ou <a href="cadastro.php" style="color: #03A9F4; text-decoration: none;" id="cadastroAgora"> cadastre-se</a>.</strong>
                        </center>
                    </div>
                    <form class="form-inline form-group-sm" id="login" method="post" action="login/logar.php">
                        <center>
                            <input type="text" id="email22" style="margin-top: 2px;" class="form-control" placeholder="Email" name="email">
                            <input type="password" style="margin-top: 2px;" class="form-control" placeholder="Senha" name="senha">
                            <button id="btn_entrar" style="margin-top: 2px; margin-bottom: 2px;" class="btn btn-default btn-sm" type="submit">Entrar</button>
                        </center>
                    </form>
                </div>';
    }
}

?>