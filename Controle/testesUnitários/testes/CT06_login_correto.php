<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $email = 'exemplo@email.com';
        $senha = 'testes123456';


        print 'Caso de Teste 06 - Realizar Login
            
  ##### Neste caso de teste estamos informando todos os parametros obrigatorios, estamos informando o email(exemplo@email.com) e a senha (testes123456) ' .
                'e tentaremos fazer login no sistema, se os usuarios e senhas forem validos devera finalizar o teste com sucesso. #####
  

';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $usuario = $p->login($email);
            if ($usuario && password_verify($senha, $usuario["SENHA"])) {
                print '----------------                   
-Usuario Logado-
----------------
  
';
                $this->assertTrue(true);
            } else {
            print '----------------                   
-Login Invalido-
----------------
  

';
            $this->assertTrue(false);
        }
    }

}

?>