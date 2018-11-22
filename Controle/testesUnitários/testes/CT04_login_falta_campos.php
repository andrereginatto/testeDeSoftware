<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $email = null;
        $senha = 'testes1234567';
        $aux = null;


        print 'Caso de Teste 04 - Realizar Login
            
  ##### Neste caso de teste nao estamos informando todos os parametros obrigatorios o sistema devera retornar quais sao os campos que nao estao sendo informados e devera encerrar com sucesso. #####
  

';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $usuario = $p->login($email);
        if ($email == null) {
            print '---------------------                   
-Email nao informado-
---------------------
  
';
            $aux = 1;
        }

        if ($senha == null) {
            print '---------------------                   
-Senha nao informada-
---------------------
  
';
            $aux = 1;
        }

        if ($aux !== 1) {
            if ($usuario && password_verify($senha, $usuario["SENHA"])) {
                print '----------------                   
-Usuario Logado-
----------------
  
';
                $this->assertTrue(false);
            } else {
                print '----------------                   
-Login Invalido-
----------------
  

';
                $this->assertTrue(false);
            }
        }else{
            print '------------------------------------                   
-Campos Obrigatorios nao informados-
------------------------------------
  

';
            
            $this->assertTrue(true);
        }
    }

}

?>