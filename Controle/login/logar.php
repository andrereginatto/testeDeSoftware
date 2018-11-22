<?php
session_start();
if (!isset($_SESSION['nome'])) {
    if (isset($_POST['email'])) {
        if (!empty($_POST['email']) && !empty($_POST['senha'])) {
            require_once '../Classes/DAO_PESSOA.php';
            require_once '../Classes/DAO_CONTA.php';
            $p = new DAO_PESSOA();
            $con = new DAO_CONTA();
            $usuario = $p->login($_POST['email']);
            if ($usuario && password_verify($_POST["senha"], $usuario["SENHA"])) {
                if(isset($_SESSION['email_error'])){
                    unset ($_SESSION['email_error']);
                }
                if(isset($_SESSION['alert'])){
                    unset($_SESSION['alert']);
                }
                $conta = $con->select_contas($usuario['ID']);
                $saldo = $con->select_saldo($usuario['ID']);
                
                $_SESSION['nome'] = $usuario['NOME'];
                $_SESSION['img'] = $usuario['IMG'];
                $_SESSION['id'] = $usuario['ID'];
                $_SESSION['email'] = $usuario['EMAIL'];
                $_SESSION['conta'] = $conta['ids'];
                
                $_SESSION['saldo'] = $saldo['SALDO'];
                $_SESSION['pag_mes']= null;
                $_SESSION["sessiontime"] = time() + 480;
                echo "<script> location = ('../".$_SESSION['pag']."');
                     </script>";
            } else {
                $_SESSION['email_error'] = $_POST['email'];
                $_SESSION['alert']="Usuário ou senha inválidos!";
                echo "<script> location = ('../".$_SESSION['pag']."');
                     </script>";
            }
        }else {
            $_SESSION['alert']="Informe um usuário e uma senha!";
            echo "<script> location = ('../".$_SESSION['pag']."');
                     </script>";
        }
    }
}
?>