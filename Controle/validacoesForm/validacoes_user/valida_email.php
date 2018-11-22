<?php
// Verifica se existe a variável emailform
if (isset($_GET["emailform"])) {
    session_start();
    require_once '../../Classes/DAO_PESSOA.php';
    $email = $_GET["emailform"];
    $p = new DAO_PESSOA();
    if($_SESSION['pag']=='cadastro.php'){
        $result = $p ->verifica_email($email);
        echo $result["cont"];
    }else{
        if(isset($_SESSION['nome'])){
            $result = $p ->verifica_alterar_email($email, $_SESSION['email']);
            echo $result["cont"];
        }
    }
}
?>