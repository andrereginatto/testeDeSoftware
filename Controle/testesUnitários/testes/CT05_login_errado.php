<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $email = 'exemplo@email.com';
        $senha = 'qualquersenha';


        print 'Caso de Teste 05 - Realizar Login
            
  ##### Neste caso de teste estamos informando todos os parametros obrigatorios, estamos informando o email(exemplo@email.com) e a senha (qualquersenha) ' .
                'e tentaremos fazer login no sistema, se os usuarios e senhas forem invalidos devera finalizar o teste com sucesso. #####
  

';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $usuario = $p->login($email);
            if ($usuario && password_verify($senha, $usuario["SENHA"])) {
                print '--------------------                   
---Usuario Logado---
--------------------
  
';
                $this->assertTrue(false);
            } else {
            print '-------------------                   
| Login Invalido  |
-------------------
  

';
            $this->assertTrue(true);
        }
    }

}

?>