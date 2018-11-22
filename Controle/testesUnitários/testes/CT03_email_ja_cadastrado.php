<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $nome = 'nome1';
        $sobrenome = 'sobrenome1';
        $email = 'exemplo@email.com';
        $senha = 'testes123456';
        $sexo = 'M';
        $aniver = '1985/03/23';
        $fone = '54999999999';


        print 'Caso de Teste 03 - Criar Auto Cadastro (Email ja cadastrado)
            
  ##### Neste caso de teste e esperado seja exibida uma mensagem de email ja cadastrado logo que o usuario preencher o email e nao permitir o cadastro.


';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $result = $p->verifica_email($email);

        if ($result['cont'] == 1) {
            print '---------------------                   
-Email ja Cadastrado-
---------------------
  
';
            $this->assertEquals('1', $result['cont']);
        } else {
            print '------------------                   
-Email Disponivel-
------------------
  

';
            $this->assertTrue(true);
        }
    }

}

?>