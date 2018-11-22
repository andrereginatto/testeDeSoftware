<?php

$valida_conta = new DAO_CONTA();
$valida_conta = $valida_conta->list_full_contas_by_id($_SESSION['id']);

if (isset($_POST['save'])) {
    $erro = false;
    $input_erro = null;
    $msg_erro = null;

    if (empty($_POST['valor'])) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'valor';
        }
        $msg_erro = $msg_erro . '- Por favor, informe um valor.<br>';
    } else if (!is_numeric($_POST['valor'])) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'valor';
        }
        $msg_erro = $msg_erro . '- Por favor, o valor deve ser um número.<br>';
    } else if (($_POST['valor']) > 999999999999.99) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'valor';
        }
        $msg_erro = $msg_erro . '- O valor não pode ser maior que 999999999999,99.<br>';
    } else {
        if ($_POST['valor'] < 0.1) {
            $erro = true;
            if ($input_erro == null) {
                $input_erro = 'valor';
            }
            $msg_erro = $msg_erro . '- Por favor, o valor deve ser maior que 0.<br>';
        }
    }

    if (empty($_POST['obs'])) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'obs';
        }
        $msg_erro = $msg_erro . '- Por favor, informe uma descrição.<br>';
    } else if (strlen($_POST['obs']) > 60) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'obs';
        }
        $msg_erro = $msg_erro . '- Por favor, a descrição não pode haver mais que 60 caracteres.<br>';
    }

    if (empty($_POST['dia_insert'])) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'dia_insert';
        }
        $msg_erro = $msg_erro . '- Por favor, informe a data da primeira parcela.<br>';
    } else if (strlen($_POST['dia_insert']) != 10) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'dia_insert';
        }
        $msg_erro = $msg_erro . '- Por favor, informe uma data válida.<br>';
    } else {
        $dat = $_POST['dia_insert'];
        $data = explode("-", "$dat"); // fatia a string $dat em pedados, usando / como referência
        $y = $data[0];
        $m = $data[1];
        $d = $data[2];

        if ($y < 1902) {
            $erro = true;
            if ($input_erro == null) {
                $input_erro = 'data';
            }
            $msg_erro = $msg_erro . '- O ano deve ser no mínimo 1902.<br>';
        } else {
            $res = checkdate($m, $d, $y);
            if ($res != 1) {
                $erro = true;
                if ($input_erro == null) {
                    $input_erro = 'dia_insert';
                }
                $msg_erro = $msg_erro . '- Por favor, informe uma data válida.<br>';
            }
        }
    }
    if(isset($_POST['repeticao'])){
        if ($_POST['repeticao'] == 1) {
            if (empty($_POST['qtd_parcelas'])) {
                $erro = true;
                if ($input_erro == null) {
                    $input_erro = 'qtd_parcelas';
                }
                $msg_erro = $msg_erro . '- Por favor, informe uma quantidade.<br>';
            } else if (!is_numeric($_POST['qtd_parcelas'])) {
                $erro = true;
                if ($input_erro == null) {
                    $input_erro = 'qtd_parcelas';
                }
                $msg_erro = $msg_erro . '- Por favor, a quantidade deve ser um número.<br>';
            } else if (($_POST['qtd_parcelas']) > 100000) {
                $erro = true;
                if ($input_erro == null) {
                    $input_erro = 'qtd_parcelas';
                }
                $msg_erro = $msg_erro . '- A quantidade não pode ser maior que 100.000.<br>';
            } else {
                if ($_POST['qtd_parcelas'] < 1) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'qtd_parcelas';
                    }
                    $msg_erro = $msg_erro . '- Por favor, a quantidade deve ser maior que 0.<br>';
                }
            }
        } else if ($_POST['repeticao'] == 2) {
            if (!isset($_POST['check_repetir'])) {
                if (empty($_POST['qtd_parcelas'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'qtd_parcelas';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe uma quantidade de parcelas.<br>';
                } else if (!is_numeric($_POST['qtd_parcelas'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'qtd_parcelas';
                    }
                    $msg_erro = $msg_erro . '- Por favor, a quantidade de parcelas deve ser um número.<br>';
                } else if (($_POST['qtd_parcelas']) > 100000) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'qtd_parcelas';
                    }
                    $msg_erro = $msg_erro . '- A quantidade de parcelas não pode ser maior que 100000.<br>';
                } else {
                    if ($_POST['qtd_parcelas'] < 1) {
                        $erro = true;
                        if ($input_erro == null) {
                            $input_erro = 'qtd_parcelas';
                        }
                        $msg_erro = $msg_erro . '- Por favor, a quantidade de parcelas deve ser maior que 0.<br>';
                    }
                }
            }

            if (!($_POST['vezes_repeat'] > 0 && $_POST['vezes_repeat'] <= 10)) {
                $erro = true;
                if ($input_erro == null) {
                    $input_erro = 'qtd_parcelas';
                }
                $msg_erro = $msg_erro . '- Por favor, não altere o código.<br>';
            }
            $flag = false;
            if ($_POST['periodo'] == "DAY") {
                $flag = true;
            } else if ($_POST['periodo'] == "WEEK") {
                $flag = true;
            } else if ($_POST['periodo'] == "MONTH") {
                $flag = true;
            } else if ($_POST['periodo'] == "YEAR") {
                $flag = true;
            }

            if ($flag == false) {
                $erro = true;
                if ($input_erro == null) {
                    $input_erro = 'periodo';
                }
                $msg_erro = $msg_erro . '- Por favor, não altere o código.<br>';
            }
        }
    }

    $flag_cat = false;
    foreach ($categorias as $cat) {
        if ($cat['CATEGORIA'] == $_POST['categoria']) {
            $flag_cat = true;
        }
    }
    if (empty($_POST['categoria'])) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'categoria';
        }
        $msg_erro = $msg_erro . '- Por favor, informe uma categoria.<br>';
    } else if ($flag_cat == false) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'categoria';
        }
        $msg_erro = $msg_erro . '- Por favor, informe uma categoria.<br>';
    }

    $flag_conta = false;
    foreach ($valida_conta as $linhas_contas) {
        if ($linhas_contas['ID'] == $_POST['conta']) {
            $flag_conta = true;
        }
    }
    if (empty($_POST['conta'])) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'conta';
        }
        $msg_erro = $msg_erro . '- Por favor, informe uma conta.<br>';
    } else if ($flag_conta == false) {
        $erro = true;
        if ($input_erro == null) {
            $input_erro = 'conta';
        }
        $msg_erro = $msg_erro . '- Por favor, informe uma conta.<br>';
    }
    
}