<?php
session_start();
    unset($_SESSION["nome"]);
    unset($_SESSION["img"]);
    unset($_SESSION["id"]);
    unset($_SESSION["conta"]);
    unset($_SESSION["email"]);
    unset($_SESSION["saldo"]);
    unset($_SESSION["sessiontime"]);
    unset($_SESSION['mes']);
    unset($_SESSION['ano']);
    unset($_SESSION['pag_mes']);
    echo "<script> location = ('../".$_SESSION['pag']."');
                     </script>!";
?>