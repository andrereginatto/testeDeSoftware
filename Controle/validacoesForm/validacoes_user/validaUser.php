<?php

if (isset($_POST['save'])) {
        $erro = false;
        $input_erro = null;
        $msg_erro = null;
        if($_SESSION['pag']!=='alterar_senha.php'){
            if(isset($_POST['nome']) && isset($_POST['sobrenome']) && isset($_POST['fone']) && isset($_POST['genero']) && isset($_POST['email'])){
                if (empty($_POST['nome'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'nome';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe um nome.<br>';
                } else if (strlen($_POST['nome']) > 40) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'nome';
                    }
                    $msg_erro = $msg_erro . '- O nome não pode haver mais que 40 caracteres.<br>';
                }

                if (strlen($_POST['sobrenome']) > 40) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'sobrenome';
                    }
                    $msg_erro = $msg_erro . '- O sobrenome não pode haver mais que 40 caracteres.<br>';
                }

                if (empty($_POST['fone'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'fone';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe um telefone.<br>';
                } else if (strlen($_POST['fone']) > 15 || strlen($_POST['fone']) < 14) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'fone';
                    }
                    $msg_erro = $msg_erro . '- O número de telefone deve haver 8 ou 9 caracteres.<br>';
                }

                if (empty($_POST['genero'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'genero';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe seu gênero.<br>';
                } else if ($_POST['genero'] != 'M' && $_POST['genero'] != 'F') {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'genero';
                    }
                    $msg_erro = $msg_erro . '- O gênero deve ser Masculino ou Femenino.<br>';
                }

                if (empty($_POST['email'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'email';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe um email.<br>';
                } else if (strlen($_POST['email']) > 40) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'email';
                    }
                    $msg_erro = $msg_erro . '- O email não pode haver mais que 40 caracteres.<br>';
                } else {
                    $email = $_POST['email'];
                    $flag = true;
                    $expresao = "/^[_A-Za-z0-9\\-+]+(\\.[_A-Za-z0-9\\-]+)*@[A-Za-z0-9\\-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$/";

                    if(!preg_match($expresao, $email)) {
                        $flag = false;
                    }else {
                        $flag = true;
                    }

                    if ($flag == false) {
                        $erro = true;
                        if ($input_erro == null) {
                            $input_erro = 'email';
                        }
                        $msg_erro = $msg_erro . '- Por favor insira um email válido.<br>';
                    } else {
                        require_once './Classes/DAO_PESSOA.php';
                        $p = new DAO_PESSOA();
                        if ($_SESSION['pag'] == 'cadastro.php') {
                            $result = $p->verifica_email($email);
                            $email_livre = $result["cont"];
                        } else {
                            if (isset($_SESSION['nome'])) {
                                $result = $p->verifica_alterar_email($email, $_SESSION['email']);
                                $email_livre = $result["cont"];
                            }
                        }

                        if($email_livre > 0){
                            $erro = true;
                            if ($input_erro == null) {
                                $input_erro = 'email';
                            }
                            $msg_erro = $msg_erro . '- Este email já está em uso.<br>';  
                        }
                    }
                }
            }
        }
        if ($_SESSION['pag'] !== "alterar_usuario.php") {
            if(isset($_POST['senha'])){
                if (empty($_POST['senha'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'senha';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe uma senha.<br>';
                } else if (strlen($_POST['senha']) < 8 || strlen($_POST['senha']) > 30) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'senha';
                    }
                    $msg_erro = $msg_erro . '- A senha deve haver entre 8 e 30 caracteres.<br>';
                }   
            }
        }
        if($_SESSION['pag']!=='alterar_senha.php'){
            if(isset($_POST['aniver']) && isset($_POST['foto'])){
                if (empty($_POST['aniver'])) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'aniver';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe sua data de nascimento.<br>';
                } else if (strlen($_POST['aniver']) != 10) {
                    $erro = true;
                    if ($input_erro == null) {
                        $input_erro = 'aniver';
                    }
                    $msg_erro = $msg_erro . '- Por favor, informe uma data válida.<br>';
                } else {
                    $dat = $_POST['aniver'];
                    $data = explode("-", "$dat"); // fatia a string $dat em pedados, usando / como referência
                    $y = $data[0];
                    $m = $data[1];
                    $d = $data[2];

                    if($y < 1902){
                        $erro = true;
                        if ($input_erro == null) {
                            $input_erro = 'aniver';
                        }
                        $msg_erro = $msg_erro . '- O ano deve ser no mínimo 1902.<br>';
                    }else{
                        $res = checkdate($m, $d, $y);
                        if ($res != 1) {
                            $erro = true;
                            if ($input_erro == null) {
                                $input_erro = 'aniver';
                            }
                            $msg_erro = $msg_erro . '- Por favor, informe uma data válida.<br>';
                        } else {
                            $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                            $nascimento = mktime(0, 0, 0, $m, $d, $y);
                            $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
                            if ($idade < 2) {
                                $erro = true;
                                if ($input_erro == null) {
                                    $input_erro = 'aniver';
                                }
                                $msg_erro = $msg_erro . '- Por favor, você deve haver no mínimo 2 anos.<br>';
                            }
                        }
                    }
                }

                if (!empty($_POST['foto'])) {
                    $arr = array("img/usuarios/m1.png",
                        "img/usuarios/m2.png",
                        "img/usuarios/m3.png",
                        "img/usuarios/m4.png",
                        "img/usuarios/m5.png",
                        "img/usuarios/h1.png",
                        "img/usuarios/h2.png",
                        "img/usuarios/h3.png",
                        "img/usuarios/h4.png",
                        "img/usuarios/h5.png",
                        "img/usuarios/desconhecido.png"
                    );
                    $flag = false;
                    foreach ($arr as $linha) {
                        if ($_POST['foto'] == $linha) {
                            $flag = true;
                        }
                    }

                    if ($flag == false) {
                        $erro = true;
                        if ($input_erro == null) {
                            $input_erro = 'foto';
                        }
                        $msg_erro = $msg_erro . '- Por favor, não altere o código espertinho!<br>';
                    }
                }
            }
        }
    }else{
        $erro = false;
        $input_erro = null;
        $msg_erro = null;
    }
?>