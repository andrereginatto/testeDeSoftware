<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $email = 'exemplo@email.com';
        $senha = 'testes123456';


        print 'Caso de Teste 07 - Realizar Logout)
            
  ##### Neste caso de teste estamos informando todos os parametros obrigatorios para realizar login e apos realizar login nos realizaremos o logout #####
  

';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $usuario = $p->login($email);
            if ($usuario && password_verify($senha, $usuario["SENHA"])) {
                $logado = $usuario['NOME'];
                $cont = 1;
                while ($cont <= 2){
                    $cont++;
                    if(isset($logado)){
                        print '----------------                   
-Usuario Logado-
----------------
  
';
                    }else{
                        print '------------------                   
-Logout Realizado-
------------------
  
';
                    }
                    unset($logado);
                }
                
                if(!isset($logado)){
                    $logado=null;
                }
                
                $this->assertNull($logado);
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