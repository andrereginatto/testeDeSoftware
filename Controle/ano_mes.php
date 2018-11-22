<?php

if (isset($_POST['acao'])) {
    if ($_POST['acao'] == 'anterior') {
        if ($_SESSION['mes'] == 01) {
            $_SESSION['mes'] = 12;
            $_SESSION['ano'] = $_SESSION['ano'] - 1;
        } else {
            $_SESSION['mes'] = $_SESSION['mes'] - 1;
        }
    }

    if ($_POST['acao'] == 'proximo') {
        if ($_SESSION['mes'] == 12) {
            $_SESSION['mes'] = 01;
            $_SESSION['ano'] = $_SESSION['ano'] + 1;
        } else {
            $_SESSION['mes'] = $_SESSION['mes'] + 1;
        }
    }
} else {
    $_SESSION['ano'] = date('Y', $data_atual);
    $_SESSION['mes'] = date('m', $data_atual);
}

switch ($_SESSION['mes']) {
    case '01':
        $mes_ano = 'Janeiro de ' . $_SESSION['ano'];
        break;
    case '02':
        $mes_ano = 'Fevereiro de ' . $_SESSION['ano'];
        break;
    case '03':
        $mes_ano = 'Março de ' . $_SESSION['ano'];
        break;
    case '04':
        $mes_ano = 'Abril de ' . $_SESSION['ano'];
        break;
    case '05':
        $mes_ano = 'Maio de ' . $_SESSION['ano'];
        break;
    case '06':
        $mes_ano = 'Junho de ' . $_SESSION['ano'];
        break;
    case '07':
        $mes_ano = 'Julho de ' . $_SESSION['ano'];
        break;
    case '08':
        $mes_ano = 'Agosto de ' . $_SESSION['ano'];
        break;
    case '09':
        $mes_ano = 'Outubro de ' . $_SESSION['ano'];
        break;
    case '10':
        $mes_ano = 'Setembro de ' . $_SESSION['ano'];
        break;
    case '11':
        $mes_ano = 'Novembro de ' . $_SESSION['ano'];
        break;
    case '12':
        $mes_ano = 'Dezembro de ' . $_SESSION['ano'];
        break;
}
?>